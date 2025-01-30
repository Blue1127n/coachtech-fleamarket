<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
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


        public function purchase(Request $request, $item_id)
    {
        // 商品を取得
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // セッションに選択された支払い方法を保存（リダイレクトせずに処理する）
        if ($request->has('payment_method')) {
            session(['selected_payment_method' => $request->payment_method]);
        }

        // そのままビューを返す（リダイレクトしない）
        return view('item.purchase', compact('item', 'user'));
    }

    public function changeAddress($item_id)
{
    $item = Item::findOrFail($item_id);
    $user = auth()->user();

    return view('item.address_change', compact('item', 'user'));
}

    public function updateAddress(Request $request, $item_id)
{
    $request->validate([
        'postal_code' => 'required|string|max:10',
        'address' => 'required|string|max:255',
        'building' => 'nullable|string|max:255',
    ]);

    $user = auth()->user();
    $user->update([
        'postal_code' => $request->postal_code,
        'address' => $request->address,
        'building' => $request->building,
    ]);

    return redirect()->route('item.purchase', ['item_id' => $item_id])->with('success', '住所が更新されました');
}


}
