<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\AddressChangeRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Item::query();

        $query = $this->applySearchFilter($query, $search);

        $products = $query->with('status')
                            ->when(auth()->check(), function ($query) {
                            $query->where('user_id', '!=', auth()->id());
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('products.index', [
            'products' => $products,
            'isMyList' => false,
            'search' => $search,
        ]);
    }

    public function mylist(Request $request)
    {
        $search = $request->input('search');

        if (!auth()->check()) {
            return view('products.index', [
                'products' => collect(),
                'isMyList' => true,
                'search' => $search,
            ]);
        }

        $products = auth()->user()
                            ->likes()
                            ->with('status')
                            ->get();

        if ($search) {
            $products = $products->filter(function ($product) use ($search) {
                return stripos($product->name, $search) !== false;
            });
        }

        return view('products.index', [
            'products' => $products,
            'isMyList' => true,
            'search' => $search,
        ]);
    }

    private function applySearchFilter($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', "%$search%");
        }
        return $query;
    }

    public function show($item_id)
    {
        $item = Item::with(['categories', 'condition', 'user', 'likes'])->findOrFail($item_id);

        $isLiked = false;

        if (Auth::check()) {
            $isLiked = Auth::user()->likes()->where('item_id', $item->id)->exists();
        }

        return view('item.detail', [
            'item' => $item,
            'isLiked' => $isLiked,
        ]);
    }

    public function like(Request $request, $item_id)
    {

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $isLiked = $user->likes()->where('item_id', $item->id)->exists();

        if ($isLiked) {
            $user->likes()->detach($item->id);
            $liked = false;
        } else {
            $user->likes()->attach($item->id);
            $liked = true;
        }

        $likeCount = $item->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }

    public function comment(CommentRequest $request, $item_id)
{
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'redirect' => route('login'),
        ], 401);
    }

    $item = Item::findOrFail($item_id);
    $comment = $item->comments()->create([
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    $comments_count = $item->comments()->count();

    return response()->json([
        'success' => true,
        'comment' => [
            'user' => [
                'name' => Auth::user()->name,
                'profile_image_url' => Auth::user()->profile_image_url ?: null,
            ],
            'content' => $comment->content,
            'created_at' => $comment->created_at->format('Y-m-d H:i'),
        ],
        'comments_count' => $comments_count,
    ]);
}

    public function purchase(Request $request, $item_id)
    {

        Log::info('purchase メソッド開始', ['item_id' => $item_id]);
    try {

        $item = Item::findOrFail($item_id);
        Log::info('取得した商品情報', ['item' => $item]);

        $user = auth()->user();

        if (!$user) {
            Log::warning('未ログインのユーザーが購入ページにアクセスしました', ['item_id' => $item_id]);
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

        $transaction = Transaction::where('item_id', $item_id)
                                    ->where('buyer_id', $user->id)
                                    ->first();

        $postalCode = !empty($transaction) && !empty($transaction->shipping_postal_code)
                        ? preg_replace('/(\d{3})(\d{4})/', '$1-$2', $transaction->shipping_postal_code)
                        : preg_replace('/(\d{3})(\d{4})/', '$1-$2', $user->postal_code);

        $address = !empty($transaction) && !empty($transaction->shipping_address)
                        ? $transaction->shipping_address
                        : $user->address;

        $building = !empty($transaction) && !is_null($transaction->shipping_building)
                        ? $transaction->shipping_building
                        : ($transaction ? null : (!empty($user->building) ? $user->building : null));

                        Log::info('購入ページのデータをセット', [
                            'postalCode' => $postalCode,
                            'address' => $address,
                            'building' => $building
                        ]);

        return view('item.purchase', compact('item', 'postalCode', 'address', 'building'));

    } catch (\Exception $e) {
        Log::error('purchase メソッドでエラー発生', ['error' => $e->getMessage()]);
        return response()->json(['error' => '内部エラーが発生しました'], 500);
    }
    }

    public function processPurchase(PurchaseRequest $request, $item_id)
{
    try {
        \Log::info("processPurchase - 開始", ['item_id' => $item_id]);

        $user = auth()->user();
        \Log::info("ログインユーザー", ['user_id' => $user->id]);

        if (!$user) {
            \Log::error('認証ユーザーが見つかりません');
            return response()->json(['error' => '未認証のリクエストです'], 401);
        }

        // デバッグログ追加
        Log::info('購入処理開始', [
            'user_id' => $user->id,
            'item_id' => $item_id,
            'payment_method' => $request->payment_method,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // `firstOrNew` で取得（新規作成 or 既存データ取得）
        $transaction = Transaction::firstOrNew(
            ['item_id' => $item_id, 'buyer_id' => $user->id]
        );

        // データをセット
        $transaction->status_id = 1;
        $transaction->payment_method = $request->payment_method;
        $transaction->shipping_postal_code = $request->postal_code;
        $transaction->shipping_address = $request->address;
        $transaction->shipping_building = $request->building;

        // **データを保存**
        $transaction->save();

        // 🛠️ ここでデータベースの確認ログを出す
        \Log::info("購入処理時の transactions テーブル", [
            'transaction' => Transaction::where('item_id', $item_id)
                ->where('buyer_id', $user->id)
                ->first()
        ]);

        return response()->json([
            'message' => '支払い方法が選択されました',
            'redirect_url' => route('payment.page', ['item_id' => $item_id])
        ]);

    } catch (\Throwable $e) {
        \Log::error("購入処理中にエラー発生", ['message' => $e->getMessage()]);
        return response()->json(['error' => '内部エラーが発生しました'], 500);
    }
}

public function changeAddress($item_id)
{
    $item = Item::findOrFail($item_id);
    $user = auth()->user();

    $transaction = Transaction::where('item_id', $item_id)
                                ->where('buyer_id', $user->id)
                                ->first();

    $shippingPostalCode = !empty($transaction) && !empty($transaction->shipping_postal_code)
        ? preg_replace('/(\d{3})(\d{4})/', '$1-$2', $transaction->shipping_postal_code)
        : preg_replace('/(\d{3})(\d{4})/', '$1-$2', $user->postal_code);

    $shippingAddress = !empty($transaction) && !empty($transaction->shipping_address)
        ? $transaction->shipping_address
        : $user->address;

    $shippingBuilding = !empty($transaction) && !is_null($transaction->shipping_building)
        ? $transaction->shipping_building
        : ($user->building ?? '');

    return view('item.address_change', compact('item', 'shippingPostalCode', 'shippingAddress', 'shippingBuilding'));
}

public function updateAddress(AddressChangeRequest $request, $item_id)
{
    $user = auth()->user();
    \Log::info(" updateAddress() が呼ばれた!", [
        'item_id' => $item_id,
        'user_id' => $user->id
    ]);

    // `firstOrNew` で取得（新規作成 or 既存データ取得）
    $transaction = Transaction::firstOrNew(
        ['item_id' => $item_id, 'buyer_id' => $user->id]
    );

    \Log::info(" 取得した Transaction データ", [
        'transaction' => $transaction
    ]);

    // データをセット
    $transaction->status_id = 1;
    $transaction->payment_method = '未設定';
    $transaction->shipping_postal_code = $request->postal_code;
    $transaction->shipping_address = $request->address;
    $transaction->shipping_building = $request->filled('building') ? $request->building : null;

    // **データを保存**
    $transaction->save();

    \Log::info(" 住所更新後の transactions テーブル", [
        'updated_transaction' => Transaction::where('item_id', $item_id)->where('buyer_id', $user->id)->first()
    ]);

    return redirect()->route('item.purchase', ['item_id' => $item_id])
        ->with('success', '住所が更新されました');
}



public function create()
{
    $categories = Category::all();
    $conditions = Condition::all();

    return view('item.sell', compact('categories', 'conditions'));
}

public function store(ExhibitionRequest $request)
{
    if (!Auth::check()) {
        return response()->json(['error' => 'ログインしていません'], 403);
    }

    if (!$request->hasFile('image')) {
        return response()->json(['error' => '商品画像がアップロードされていません'], 422);
    }

    $imagePath = $request->file('image')->store('items', 'public');

    $item = Item::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'condition_id' => $request->condition,
        'image' => $imagePath,
        'brand' => null,
    ]);

    if ($request->has('category')) {
        $categories = is_array($request->category) ? $request->category : [$request->category];
        $item->categories()->attach($categories);
    }

    return response()->json([
        'message' => '商品が出品されました',
        'image_path' => $imagePath
    ]);
}
}
