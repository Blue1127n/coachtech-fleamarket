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

        <form action="{{ route('item.processPurchase', ['item_id' => $item->id]) }}" method="POST">
        @csrf

            <div class="payment-method">
                <h2>支払い方法</h2>
                <div class="custom-payment-select">
                    <div class="selected-option" id="selectedPayment">
                        {{ old('payment_method') ?: '選択してください' }}
                    </div>
                    <div class="dropdown-options" id="paymentDropdown">
                        <div class="dropdown-option" data-value="コンビニ払い">
                            <span class="check-icon"></span>コンビニ払い
                        </div>
                        <div class="dropdown-option" data-value="カード支払い">
                            <span class="check-icon"></span>カード支払い
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="paymentInput" value="{{ old('payment_method') ?: '' }}">
                </div>
                @error('payment_method')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="shipping-address">
                <h2>配送先</h2>
                <div class="shipping-content">
                    <div class="shipping-info">
                        <p>〒 {{ preg_replace('/(\d{3})(\d{4})/', '$1-$2', $postalCode) }}</p>
                        <p>{{ $address }}</p>
                        @if(!empty(trim($building)))
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
                <button type="submit" class="purchase-summary-btn">購入する</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectBox = document.querySelector(".custom-payment-select");
    const selectedOption = document.getElementById("selectedPayment");
    const selectedMethod = document.getElementById("selected-method"); // 追加
    const dropdownOptions = document.getElementById("paymentDropdown");
    const options = document.querySelectorAll(".dropdown-option");
    const paymentInput = document.getElementById("paymentInput");

    if (selectBox) {
        // **クリックでプルダウン表示**
        selectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownOptions.style.display = dropdownOptions.style.display === "block" ? "none" : "block";
        });

        options.forEach(option => {
            option.addEventListener("click", function () {
                // すべてのオプションの "✓" を削除
                options.forEach(opt => opt.classList.remove("selected"));

                // 選択されたオプションに "✓" を追加
                option.classList.add("selected");

                // **選択した項目を表示**
                const selectedText = option.textContent.trim();
                selectedOption.textContent = selectedText;
                paymentInput.value = option.dataset.value;

                // **支払い方法の表示を更新**
                selectedMethod.textContent = selectedText; // 修正

                // **選択後にドロップダウンを閉じる**
                setTimeout(() => {
                    dropdownOptions.style.display = "none";
                }, 100);
            });

            // **ポインターをかざすと ✓ を表示**
            option.addEventListener("mouseenter", function () {
                this.querySelector(".check-icon").style.display = "inline-block";
            });

            // **ポインターを外すと ✓ を非表示**
            option.addEventListener("mouseleave", function () {
                this.querySelector(".check-icon").style.display = "none";
            });
        });

        // **外部クリックでドロップダウンを閉じる**
        document.addEventListener("click", function (event) {
            if (!selectBox.contains(event.target) && !dropdownOptions.contains(event.target)) {
                dropdownOptions.style.display = "none";
            }
        });

        // **キーボードの "Escape" でもドロップダウンを閉じる**
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                dropdownOptions.style.display = "none";
            }
        });
    }
});

</script>
@endpush
