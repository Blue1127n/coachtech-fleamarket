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

    public function showPaymentPage(Request $request, $item_id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $transaction = Transaction::firstOrCreate(
            ['item_id' => $item_id, 'buyer_id' => $user->id],
            [
                'status_id' => 1,
                'payment_method' => '未選択',
            ]
        );

        $payment_method = $transaction->payment_method ?? '未選択';

        return view('payment.payment', compact('item', 'payment_method'));
    }

    public function checkout(Request $request, $item_id)
    {

        $apiKey = config('services.stripe.secret');

        if (empty($apiKey)) {

            return back()->with('error', '決済処理に問題が発生しました。管理者に連絡してください。');
        }

        Stripe::setApiKey($apiKey);

        $item = Item::findOrFail($item_id);


        $user = Auth::user();

        $transaction = Transaction::firstOrCreate(
            ['item_id' => $item->id, 'buyer_id' => $user->id],
            ['status_id' => 2]
        );

        $transaction->update([
            'payment_method' => $request->input('payment_method'),
        ]);

        $paymentMethod = $request->input('payment_method');

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
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['transaction_id' => $transaction->id]),
            'cancel_url' => route('payment.cancel', ['transaction_id' => $transaction->id]),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, $transaction_id)
{
    $transaction = Transaction::findOrFail($transaction_id);

    if ($transaction->status_id == 3) {
        return redirect()->route('mypage')->with('info', 'この取引はすでに完了しています');
    }


    $paymentMethod = $request->input('payment_method', $transaction->payment_method);

    $transaction->update([
        'status_id' => 3,
        'payment_method' => $paymentMethod
    ]);

    $transaction->item->update(['status_id' => 5]);

    return redirect()->route('mypage')->with('success', '購入が完了しました！');
}

    public function cancel(Request $request, $transaction_id)
    {
        return redirect()->route('item.purchase', ['item_id' => Transaction::findOrFail($transaction_id)->item_id])
            ->with('error', '支払いがキャンセルされました。');
    }
}