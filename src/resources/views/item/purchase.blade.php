@extends('layouts.main')

@section('title', '商品購入')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="purchase-container">
    <div class="purchase-details">
        <div class="item-info">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
                <div class="item-details">
                    <h1 class="item-name">{{ $item->name }}</h1>
                    <p class="item-price">
                        <span class="item-price-symbol">¥</span>
                        <span class="item-price-value">{{ number_format($item->price) }}</span>
                    </p>
                </div>
        </div>

        <div class="payment-method">
            <h2>支払い方法</h2>
            <form action="{{ route('item.purchase', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <select name="payment_method" id="payment_method" class="payment-select" style="background-image: url('{{ asset('storage/items/triangle.svg') }}');">
                    <option value="" class="default-option" disabled selected hidden>選択してください</option>
                    <option value="コンビニ払い" class="convenience-option">コンビニ払い</option>
                    <option value="カード払い" class="card-option">カード払い</option>
                </select>
            </form>
        </div>

        <div class="shipping-address">
            <h2>配送先</h2>
            <div class="shipping-content">
                <div class="shipping-info">
                    @if(auth()->check())
                        <p>〒 {{ auth()->user()->postal_code ?? '未登録' }}</p>
                        <p>{{ auth()->user()->address ?? '未登録' }}</p>
                        <p>{{ auth()->user()->building ?? '未登録' }}</p>
                    @else
                        <p>配送先情報がありません。</p>
                    @endif
                </div>
                <a href="{{ route('item.changeAddress', ['item_id' => $item->id]) }}" class="change-address-link">変更する</a>
            </div>
        </div>
    </div>

    <div class="summary-container">
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td>商品代金</td>
                    <td class="price">
                        <span class="price-symbol">¥</span>
                        <span class="price-value">{{ number_format($item->price) }}</span>
                    </td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-method">未選択</td>
                </tr>
            </table>
        </div>
        <div class="summary-button">
            <button class="purchase-summary-btn">購入する</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("payment_method");
    const selectedMethod = document.getElementById("selected-method");

    select.addEventListener("change", function () {
        // 選択されたオプションに✓をつける（プルダウンを開いた時のみ）
        for (let option of select.options) {
            if (option.value === select.value) {
                option.textContent = `✓ ${option.value}`;
            } else {
                option.textContent = option.value;
            }
        }

        // 選択後（プルダウンを閉じた時）は✓を消す
        setTimeout(() => {
            for (let option of select.options) {
                option.textContent = option.value;
            }
        }, 100);

        // **支払い方法を即時反映**
        selectedMethod.textContent = select.value;

        // **ローカルストレージを使って支払い方法を保存**
        localStorage.setItem('selectedPaymentMethod', select.value);
        });

        // **ページをリロードしても選択した支払い方法を復元**
        const savedPaymentMethod = localStorage.getItem('selectedPaymentMethod');
        if (savedPaymentMethod) {
            select.value = savedPaymentMethod;
            selectedMethod.textContent = savedPaymentMethod;
        }
    });
</script>
@endpush
