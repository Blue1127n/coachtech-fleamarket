<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Transaction;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{

    /**
     * 支払いページを表示
     */
    public function showPaymentPage(Request $request, $item_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // **transactions テーブルのデータを取得**
        $transaction = Transaction::where('item_id', $item_id)
                            ->where('buyer_id', $user->id)
                            ->orderBy('updated_at', 'desc') // 最新の更新日時を基準に取得
                            ->first();

        // **`transactions` のデータを優先する**
        $shipping = [
            'postal_code' => $transaction && !empty($transaction->shipping_postal_code) ? preg_replace('/(\d{3})(\d{4})/', '$1-$2', $transaction->shipping_postal_code) : preg_replace('/(\d{3})(\d{4})/', '$1-$2', $user->postal_code),
            'address' => $transaction && !empty($transaction->shipping_address) ? $transaction->shipping_address : $user->address,
            'building' => isset($transaction->shipping_building) ? $transaction->shipping_building : (isset($user->building) ? $user->building : ''),
        ];

        // **GETリクエストから支払い方法を取得**
        $payment_method = $transaction ? $transaction->payment_method : '未選択';


        return view('payment.payment', compact('item', 'shipping', 'payment_method'));
    }

    /**
     * Stripe決済を開始する
     */
    public function checkout(Request $request, $item_id)
    {

        // **Stripe APIキーをログに出力して確認**
        $apiKey = config('services.stripe.secret');

        // **APIキーをログで確認**
        \Log::info('Using Stripe API Key:', ['key' => $apiKey]);

        // **APIキーが取得できているかチェック**
        if (empty($apiKey)) {
            \Log::error('Stripe API Key is missing! Check .env file.');
            return back()->with('error', '決済処理に問題が発生しました。管理者に連絡してください。');
        }

        // Stripe APIキーを設定
        Stripe::setApiKey($apiKey);

        // **ログで確認**
        \Log::info('Set Stripe API Key successfully.');

        // 商品情報を取得
        $item = Item::findOrFail($item_id);

        // **ログで商品情報を確認**
        \Log::info('Processing payment for item:', ['item_id' => $item->id, 'price' => $item->price]);

        // 購入者情報
        $user = Auth::user();

        // 取引情報を取得 or 作成
        $transaction = Transaction::firstOrCreate(
            ['item_id' => $item->id, 'buyer_id' => $user->id],
            ['status_id' => 2]
        );

        // ここで `payment_method` を更新
        $transaction->update([
            'payment_method' => $request->input('payment_method'),
            'shipping_postal_code' => $request->input('shipping_postal_code', $user->postal_code),
            'shipping_address' => $request->input('shipping_address', $user->address),
            'shipping_building' => $request->input('shipping_building', $user->building),
        ]);

        // 支払い方法の取得
        $paymentMethod = $request->input('payment_method');

        // Stripe決済のセッション作成
        $session = Session::create([
            'payment_method_types' => $paymentMethod === 'コンビニ払い' ? ['konbini'] : ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1, // 商品は1個固定
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['transaction_id' => $transaction->id]),
            'cancel_url' => route('payment.cancel', ['transaction_id' => $transaction->id]),
        ]);

        return redirect($session->url);
    }

    /**
     * 支払い成功処理
     */
    public function success(Request $request, $transaction_id)
{
    $transaction = Transaction::findOrFail($transaction_id);

    // すでに購入済みの場合はスキップ
    if ($transaction->status_id == 3) { // 3 = 取引完了
        return redirect()->route('mypage')->with('info', 'この取引はすでに完了しています');
    }

    // デバッグ用ログ出力（更新前）
    \Log::info('Updating transaction status:', [
        'transaction_id' => $transaction_id, 
        'old_status' => $transaction->status_id, 
        'old_payment_method' => $transaction->payment_method
    ]);

    // `payment_method` がリクエストに存在すれば更新、なければ既存の値を維持
    $paymentMethod = $request->input('payment_method', $transaction->payment_method);

    // ステータスを購入完了に更新
    $transaction->update([
        'status_id' => 3,
        'payment_method' => $paymentMethod
    ]);

    // 商品の状態も売り切れに変更
    $transaction->item->update(['status_id' => 5]); // 5 = 売り切れ

    // デバッグ用ログ出力（更新後）
    \Log::info('Transaction updated:', [
        'transaction_id' => $transaction_id, 
        'new_status' => 3, 
        'new_payment_method' => $paymentMethod
    ]);

    return redirect()->route('mypage')->with('success', '購入が完了しました！');
}

    /**
     * 支払いキャンセル処理
     */
    public function cancel(Request $request, $transaction_id)
    {
        return redirect()->route('item.purchase', ['item_id' => Transaction::findOrFail($transaction_id)->item_id])
            ->with('error', '支払いがキャンセルされました。');
    }
}