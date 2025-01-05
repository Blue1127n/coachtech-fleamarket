<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // おすすめ商品の表示
    public function index(Request $request)
    {
        $search = $request->input('search'); // 検索クエリ
        $query = Item::query();

        // 自分の出品を除外（ログイン中のみ）
        if (auth()->check()) {
            $query->where('user_id', '!=', auth()->id());
        }

        // 検索機能
        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        // 商品取得
        $products = $query->with('status') // ステータスのリレーションをロード
                            ->where('status_id', 1) // 販売中の商品
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('products.index', compact('products'));
    }

    // マイリストの表示
    public function mylist()
    {
        // ユーザーが「いいね」した商品を取得
        $products = auth()->user()
                            ->likes()
                            ->with('item.status') // ステータス情報もロード
                            ->get()
                            ->pluck('item'); // アイテム情報だけ取得

        return view('products.index', compact('products'));
    }
}
