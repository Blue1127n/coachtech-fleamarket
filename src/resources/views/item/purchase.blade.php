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
                    @if(!empty($building))
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
    const paymentSelectBox = document.querySelector(".custom-payment-select");
    const selectedPaymentOption = document.getElementById("selectedPayment");
    const paymentDropdownOptions = document.getElementById("paymentDropdown");
    const paymentOptions = document.querySelectorAll(".dropdown-option");
    const paymentInput = document.getElementById("paymentInput");

    if (paymentSelectBox) {
        // **クリックで開閉**
        paymentSelectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            paymentDropdownOptions.style.display = paymentDropdownOptions.style.display === "block" ? "none" : "block";
        });

        // **オプション選択時の処理**
        paymentOptions.forEach(option => {
            option.addEventListener("click", function () {
                // **すべてのオプションの "✓" を削除**
                paymentOptions.forEach(opt => opt.classList.remove("selected"));

                // **選択されたオプションに "✓" を追加**
                option.classList.add("selected");

                // **選択した項目を表示し、前の「✓」を除去**
                selectedPaymentOption.textContent = option.textContent.replace(/^✓\s*/, "").trim();
                paymentInput.value = option.dataset.value;

                // **選択後にドロップダウンを閉じる**
                setTimeout(() => {
                    paymentDropdownOptions.style.display = "none";
                    paymentSelectBox.blur();
                }, 100);
            });
        });

        // **外部クリックでドロップダウンを閉じる**
        document.addEventListener("click", function (event) {
            if (!paymentSelectBox.contains(event.target) && !paymentDropdownOptions.contains(event.target)) {
                paymentDropdownOptions.style.display = "none";
            }
        });

        // **キーボードの "Escape" でもドロップダウンを閉じる**
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                paymentDropdownOptions.style.display = "none";
            }
        });
    }
});
</script>
@endpush
