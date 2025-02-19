@extends('layouts.main')

@section('title', '送付先住所の変更')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/address_change.css') }}">
@endpush

@section('content')
<div class="address-change-container">
    <h1 class="page-title">住所の変更</h1>

    <form action="{{ route('item.updateAddress', ['item_id' => $item->id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $shippingPostalCode) }}">
            @if ($errors->has('postal_code'))
            <p class="error-message">{{ $errors->first('postal_code') }}</p>
        @endif
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $shippingAddress) }}">
            @if ($errors->has('address'))
            <p class="error-message">{{ $errors->first('address') }}</p>
        @endif
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', !is_null($shippingBuilding) ? $shippingBuilding : auth()->user()->building) }}">
            @if ($errors->has('building'))
            <p class="error-message">{{ $errors->first('building') }}</p>
        @endif
        </div>

        <div class="form-group">
            <button type="submit" class="update-btn">更新する</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("address-form");

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            document.querySelectorAll(".error-message").forEach(el => el.textContent = "");

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (!response.ok) {
                    const data = await response.json();
                    console.log("エラー:", data);

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
                    const data = await response.json();
                    window.location.href = data.redirect_url;
                }
            } catch (error) {
                console.error("エラーが発生しました:", error);
            }
        });
    }
});
</script>
@endpush
