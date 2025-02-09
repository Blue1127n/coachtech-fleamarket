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

class ItemController extends Controller
{
    // おすすめ商品の表示
    public function index(Request $request)
    {
        $search = $request->input('search'); // 検索クエリ
        $query = Item::query();

        // 検索条件を適用
        $query = $this->applySearchFilter($query, $search);

        // 全商品を取得（ログイン後は自分の商品を除外）
        $products = $query->with('status') // ステータスのリレーションをロード
                            ->when(auth()->check(), function ($query) {
                            $query->where('user_id', '!=', auth()->id()); // 自分の商品を除外
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('products.index', [
            'products' => $products,
            'isMyList' => false,
            'search' => $search, // 検索クエリをビューに渡す
        ]);
    }

    // マイリストの表示
    public function mylist(Request $request)
    {
        $search = $request->input('search'); // 検索クエリ

        // ログインしていない場合は空のコレクションを渡す
        if (!auth()->check()) {
            return view('products.index', [
                'products' => collect(), // 空のコレクションを渡す
                'isMyList' => true,
                'search' => $search, // 検索クエリをビューに渡す
            ]);
        }

        // ログインしている場合は「いいね」した商品を取得
        $products = auth()->user()
                            ->likes()
                            ->with('status') // ステータスをロード
                            ->get();

        // 検索条件の適用（コレクションに対するフィルタリング）
        if ($search) {
            $products = $products->filter(function ($product) use ($search) {
                return stripos($product->name, $search) !== false;
            });
        }

        return view('products.index', [
            'products' => $products,
            'isMyList' => true,
            'search' => $search, // 検索クエリをビューに渡す
        ]);
    }

    // 検索条件の適用
    private function applySearchFilter($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', "%$search%");
        }
        return $query;
    }

    // 商品詳細の表示
    public function show($item_id)
    {
        $item = Item::with(['categories', 'condition', 'user', 'likes'])->findOrFail($item_id);

        $isLiked = false;

        // ログインしている場合、いいね済みかどうか確認
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

        // ユーザーが既にいいねしているか確認
        $isLiked = $user->likes()->where('item_id', $item->id)->exists();

        if ($isLiked) {
            $user->likes()->detach($item->id); // いいねを解除
            $liked = false;
        } else {
            $user->likes()->attach($item->id); // いいねを追加
            $liked = true;
        }

        // いいねの合計数を取得
        $likeCount = $item->likes()->count();

        // セッションにフラッシュデータとして保存
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

   // GETリクエスト: 購入画面表示（バリデーションなし）
    public function purchase(Request $request, $item_id)
    {
        // 商品情報を取得
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // ログインしていない場合はリダイレクト
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

       // 取引データを取得（あれば使用、なければ `users` テーブルのデータを使用）
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

        return view('item.purchase', compact('item', 'postalCode', 'address', 'building'));
    }

    // 購入処理 (POST) - フォームリクエスト適用
    public function processPurchase(PurchaseRequest $request, $item_id)
{
    // **ログインユーザー情報**
    $user = auth()->user();

    // **既存の `transactions` を取得 or 作成**
    $transaction = Transaction::updateOrCreate(
        ['item_id' => $item_id, 'buyer_id' => $user->id],
        [
            'status_id' => 1, // 初期ステータス
            'payment_method' => $request->payment_method,
        ]
    );

    return redirect()->route('payment.page', ['item_id' => $item_id])->with('success', '支払い方法が選択されました');
}

public function changeAddress($item_id)
{
    $item = Item::findOrFail($item_id);
    $user = auth()->user();

    // 取引データを取得
    $transaction = Transaction::where('item_id', $item_id)
                                ->where('buyer_id', $user->id)
                                ->first();

    // **変更前のデータをセット**
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

    // **transactions テーブルを更新**
    $transaction = Transaction::updateOrCreate(
        ['item_id' => $item_id, 'buyer_id' => $user->id],
        [
            'status_id' => 1,
            'payment_method' => '未設定',
            'shipping_postal_code' => $request->postal_code,
            'shipping_address' => $request->address,
            'shipping_building' => $request->filled('building') ? $request->building : null,
        ]
    );

    return redirect()->route('item.purchase', ['item_id' => $item_id])->with('success', '住所が更新されました');
}

public function create()
{
    $categories = Category::all();
    $conditions = Condition::all();

    return view('item.sell', compact('categories', 'conditions'));
}

public function store(ExhibitionRequest $request)
{
    dd($request->all()); // デバッグ用

    if (!Auth::check()) {
        return response()->json(['error' => 'ログインしていません'], 403);
    }

    // 商品画像を保存
    $imagePath = $request->file('image')->store('items', 'public');

    // 商品データを保存
    $item = Item::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'condition_id' => $request->condition,
        'image' => $imagePath,
        'brand' => null,
    ]);

    // カテゴリーの保存
    if ($request->has('category')) {
        $item->categories()->attach($request->category);
    }

    return response()->json([
        'message' => '商品が出品されました',
        'item' => $item
    ]);
}

}
