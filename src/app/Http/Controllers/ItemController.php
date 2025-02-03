<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\AddressChangeRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
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

        // デバッグ用ログ
        \Log::info('Like Count:', ['likeCount' => $likeCount]);
        \Log::info('Liked State:', ['liked' => $liked]);

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

    $validator = \Validator::make($request->all(), [
        'content' => 'required|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
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
        // 商品を取得
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // 以前に選択した支払い方法を取得（セッションから）
        $selectedPaymentMethod = session('selected_payment_method', '');

        // 郵便番号をハイフン付きに整形
        $postalCode = $user->postal_code ? preg_replace('/(\d{3})(\d{4})/', '$1-$2', $user->postal_code) : '';

        // そのままビューを返す（リダイレクトしない）
        return view('item.purchase', compact('item', 'user', 'postalCode', 'selectedPaymentMethod'));
    }

    // 購入処理 (POST) - フォームリクエスト適用
    public function processPurchase(PurchaseRequest $request, $item_id)
{
    \Log::info('🚀 processPurchase が呼ばれた!', ['item_id' => $item_id, 'request' => $request->all()]);

    // **バリデーションの適用**
    $validated = $request->validated();
    \Log::info('✅ バリデーション通過:', $validated);

    // **transactions テーブルに保存 or 更新**
    $transaction = \App\Models\Transaction::updateOrCreate(
        ['item_id' => $item_id, 'buyer_id' => auth()->id()], // 既存レコードを検索
        [
            'status_id' => 1, // 初期ステータスを設定
            'payment_method' => $validated['payment_method'],
            'shipping_postal_code' => auth()->user()->postal_code,
            'shipping_address' => auth()->user()->address,
            'shipping_building' => auth()->user()->building
        ]
    );

    // **決済画面にリダイレクト**
    return redirect()->route('payment.page', ['item_id' => $item_id])->with('success', '支払い方法が選択されました');
}

    public function changeAddress($item_id)
{
    $item = Item::findOrFail($item_id);
    $user = auth()->user();

    // 郵便番号をハイフン付きで整形
    $postalCode = $user->postal_code ? preg_replace('/(\d{3})(\d{4})/', '$1-$2', $user->postal_code) : '';

    return view('item.address_change', compact('item', 'user', 'postalCode'));
}

public function updateAddress(AddressChangeRequest $request, $item_id)
{
    $user = auth()->user();

    // **購入情報（transactions テーブル）を取得 or 作成**
    $transaction = \App\Models\Transaction::updateOrCreate(
        ['item_id' => $item_id, 'buyer_id' => $user->id], // 条件（既にレコードがあれば取得、なければ作成）
        [
            'status_id' => 1, // 例: "購入処理中"
            'payment_method' => '未設定',// 変更済みのテーブルに合わせる
            'shipping_postal_code' => $request->postal_code,
            'shipping_address' => $request->address,
            'shipping_building' => $request->filled('building') ? $request->building : null, // 空の場合は `null`
        ]
    );

    // すでに `transactions` にデータがある場合は更新
    $transaction->update([
        'shipping_postal_code' => $request->postal_code,
        'shipping_address' => $request->address,
        'shipping_building' => $request->filled('building') ? $request->building : null, // `null` を許可
    ]);

    return redirect()->route('item.purchase', ['item_id' => $item_id])->with('success', '住所が更新されました');
}

public function create()
{
    $categories = Category::all();
    $conditions = Condition::all();
    return view('items.sell', compact('categories', 'conditions'));
}
}
