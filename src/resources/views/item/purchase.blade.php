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

        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <div class="payment-method">
            <h2>支払い方法</h2>
            <form action="{{ route('item.processPurchase', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <select name="payment_method" id="payment_method" class="payment-select" style="background-image: url('{{ asset('storage/items/triangle.svg') }}');">
                    <option value="" class="default-option" disabled hidden>選択してください</option>
                    <option value="コンビニ払い" class="convenience-option">コンビニ払い</option>
                    <option value="カード支払い" class="card-option">カード支払い</option>
                </select>
                @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <div class="shipping-address">
            <h2>配送先</h2>
            <div class="shipping-content">
                <div class="shipping-info">
                    <p>〒 {{ preg_replace('/(\d{3})(\d{4})/', '$1-$2', $postalCode) }}</p>
                    <p>{{ $address }}</p>
                    @if(isset($building) && $building !== '')
                        <p>{{ $building }}</p>
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
            <form id="purchase-form" action="{{ route('item.processPurchase', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" value="" id="selected-payment-method">
                <button type="submit" class="purchase-summary-btn">購入する</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("payment_method");
    const selectedMethod = document.getElementById("selected-method");
    const selectedPaymentMethod = document.getElementById("selected-payment-method");
    const purchaseForm = document.getElementById("purchase-form");

    // **バリデーションエラー時に old() の値を復元**
    const oldPaymentMethod = "{{ old('payment_method', '') }}";

    if (select) {
        if (oldPaymentMethod) {
            select.value = oldPaymentMethod;
            selectedMethod.textContent = oldPaymentMethod;
            selectedPaymentMethod.value = oldPaymentMethod;
        } else {
            select.selectedIndex = 0;
            selectedMethod.textContent = "未選択";
            selectedPaymentMethod.value = "";
        }

        // **プルダウンの変更イベントで hidden input を更新**
        select.addEventListener("change", function () {
            selectedMethod.textContent = select.value;
            selectedPaymentMethod.value = select.value;
            localStorage.setItem("selectedPaymentMethod", select.value);

            // **選択されたオプションに "✓" を付与**
            select.querySelectorAll("option").forEach(option => {
                option.textContent = option.value === select.value ? `✓ ${option.value}` : option.value;
            });

            // **200ms 後に元の状態に戻す**
            setTimeout(() => {
                select.querySelectorAll("option").forEach(option => {
                    option.textContent = option.value;
                });
            }, 200);
        });

        // **購入ボタンのクリック時にバリデーションをチェック**
        purchaseForm.addEventListener("submit", function (event) {
            if (!select.value) {
                event.preventDefault();
                alert("支払い方法を選択してください。");
            }
        });
    }
});

</script>
@endpush
