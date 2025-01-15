<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // 販売中の商品を取得
        $products = $query->with('status') // ステータスのリレーションをロード
                            ->where('status_id', 1) // 販売中の商品
                            ->orderBy('created_at', 'desc')
                            ->get();

        // デバッグ用ログ
        \Log::info('Products fetched for index:', ['products' => $products]);

        // 画像パスをURLに変換
        foreach ($products as $product) {
            $product->image_url = asset($product->image);
        }

        return view('products.index', [
            'products' => $products,
            'isMyList' => false,
            'search' => $search, // 検索クエリをビューに渡す
        ]);
    }

    // マイリストの表示
    public function mylist(Request $request)
    {
        if (!auth()->check()) {
            // ログインしていない場合はログインページにリダイレクト
            return redirect()->route('login');
        }

        $search = $request->input('search'); // 検索クエリ

        // ユーザーが「いいね」した商品を取得
        $products = auth()->user()
                            ->likes()
                            ->with('item.status') // ステータス情報もロード
                            ->get()
                            ->pluck('item'); // アイテム情報だけ取得

        // 検索条件の適用（コレクションに対するフィルタリング）
        if ($search) {
            $products = $products->filter(function ($product) use ($search) {
                return stripos($product->name, $search) !== false;
            });
        }

        // 画像パスをURLに変換
        foreach ($products as $product) {
            $product->image_url = asset($product->image); // 'image'を使用してURLを生成
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
}
