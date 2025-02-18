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

        <form id="purchase-form" action="{{ route('item.processPurchase', ['item_id' => $item->id]) }}" method="POST">
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
                <p class="error-message" id="payment_method-error"></p>
            </div>

            <div class="shipping-address">
                <h2>配送先</h2>
                <div class="shipping-content">
                    <div class="shipping-info">
                        <p>〒 {{ preg_replace('/(\d{3})(\d{4})/', '$1-$2', old('postal_code', $postalCode)) }}</p>
                        <p class="error-message" id="postal_code-error"></p>
                        <p>{{ old('address', $address) }}</p>
                        <p class="error-message" id="address-error"></p>
                        @if(!empty(trim($building)))
                            <p>{{ old('building', $building) }}</p>
                        @endif
                    </div>

                    <input type="hidden" name="postal_code" value="{{ old('postal_code', $postalCode) }}">
                    <input type="hidden" name="address" value="{{ old('address', $address) }}">
                    <input type="hidden" name="building" value="{{ old('building', $building) }}">

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
    const form = document.getElementById("purchase-form");

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            const csrfToken = document.querySelector('input[name=_token]').value;
            const paymentMethod = document.getElementById("paymentInput").value;

            console.log("送信する支払い方法:", paymentMethod); // デバッグ用

            if (!paymentMethod) {
                document.getElementById("payment_method-error").textContent = "支払い方法を選択してください";
                document.getElementById("payment_method-error").style.color = "red";
                return;
            }

            const formData = new FormData(form);

            // エラーメッセージをクリア
            document.querySelectorAll(".error-message").forEach(el => el.textContent = "");

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    payment_method: document.getElementById("paymentInput").value,
                    postal_code: document.querySelector('input[name="postal_code"]').value,
                    address: document.querySelector('input[name="address"]').value,
                    building: document.querySelector('input[name="building"]').value
                })
            });

                const text = await response.text(); // まずテキストとして取得
                console.log("サーバーレスポンス:", text); // デバッグ用

                try {
                    const data = JSON.parse(text); // JSONに変換

                    if (!response.ok) {
                        console.log("エラー内容:", data);
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const errorField = document.getElementById(`${key}-error`);
                                if (errorField) {
                                    errorField.textContent = data.errors[key][0];
                                    errorField.style.color = "red";
                                }
                            });
                        }
                    } else {
                        console.log("成功レスポンス:", data);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    }
                } catch (e) {
                    console.error("JSON パースエラー:", text);
                }

            } catch (error) {
                console.error("エラーが発生しました:", error);
            }
        });
    }

    const selectBox = document.querySelector(".custom-payment-select");
    const selectedOption = document.getElementById("selectedPayment");
    const selectedMethod = document.getElementById("selected-method");
    const dropdownOptions = document.getElementById("paymentDropdown");
    const options = document.querySelectorAll(".dropdown-option");
    const paymentInput = document.getElementById("paymentInput");

    if (selectBox) {
        selectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownOptions.style.display = dropdownOptions.style.display === "block" ? "none" : "block";
        });

        options.forEach(option => {
            option.addEventListener("click", function () {
                options.forEach(opt => opt.classList.remove("selected"));

                option.classList.add("selected");

                const selectedText = option.textContent.trim();
                selectedOption.textContent = selectedText;
                paymentInput.value = option.dataset.value;

                selectedMethod.textContent = selectedText;

                setTimeout(() => {
                    dropdownOptions.style.display = "none";
                }, 100);
            });

            option.addEventListener("mouseenter", function () {
                this.querySelector(".check-icon").style.display = "inline-block";
            });

            option.addEventListener("mouseleave", function () {
                this.querySelector(".check-icon").style.display = "none";
            });
        });

        document.addEventListener("click", function (event) {
            if (!selectBox.contains(event.target) && !dropdownOptions.contains(event.target)) {
                dropdownOptions.style.display = "none";
            }
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                dropdownOptions.style.display = "none";
            }
        });
    }
});
</script>
@endpush
