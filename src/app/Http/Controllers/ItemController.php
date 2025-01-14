<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // おすすめ商品の表示
    public function index(Request $request)
    {
        $search = $request->input('search'); // 検索クエリ
        $query = Item::query();

        // ログイン済みかをチェック
        if (auth()->check()) {
            // ログイン済みの場合はマイリストを表示
            return $this->mylist($request); // ログイン済みの場合はマイリストを表示
        }

        // 非ログインの場合は全商品を表示
        $query = $this->applySearchFilter($query, $search);

        // 商品取得
        $products = $query->with('status') // ステータスのリレーションをロード
                            ->where('status_id', 1) // 販売中の商品
                            ->orderBy('created_at', 'desc')
                            ->get();

        // 画像パスをURLに変換
        foreach ($products as $product) {
            $product->image_url = asset($product->image);
        }

        return view('products.index', ['products' => $products, 'isMyList' => false]);
    }

    // マイリストの表示
    public function mylist(Request $request)
    {
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

        return view('products.index', ['products' => $products, 'isMyList' => true]);
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
