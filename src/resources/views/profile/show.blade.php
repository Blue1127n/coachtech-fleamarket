@extends('layouts.main')

@section('title', 'プロフィール画面')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image">
            <img src="{{ session('profile_image_temp') ?: (auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '') }}" alt="">
        </div>

        <div class="profile-info">
            <h2>{{ $user->name }}</h2>
        </div>
        <div class="profile-btn">
            <a href="{{ route('mypage.profile') }}" class="btn">プロフィールを編集</a>
        </div>
    </div>

    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items">
        @if($items->isNotEmpty())
            @foreach($items as $item)
                <div class="item">
                    <div class="product-image">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                        @if($item->status_id == 5)
                            <div class="sold-badge">SOLD</div>
                        @endif
                    </div>
                    <p>{{ $item->name }}</p>
                </div>
            @endforeach
        @else
            <p>{{ $page === 'sell' ? '出品した商品がありません' : '購入した商品がありません' }}</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("profile-form");

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            const formData = new FormData(form);
            document.querySelectorAll(".error-message").forEach(el => el.textContent = "");

            // フォームデータを JSON に変換
            let data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            try {
                const response = await fetch(form.action, {
                    method: "PUT",  // ルートの設定に合わせて PUT にする
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(data) // JSON に変換して送信
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.log("エラー:", errorData);

                    if (errorData.errors) {
                        Object.keys(errorData.errors).forEach(key => {
                            const errorField = document.getElementById(`${key}-error`);
                            if (errorField) {
                                errorField.textContent = errorData.errors[key][0];
                                errorField.style.color = "red";
                            }
                        });
                    }
                } else {
                    const successData = await response.json();
                    console.log("成功:", successData);

                    if (successData.redirect_url) {
                        window.location.href = successData.redirect_url;
                    }
                }
            } catch (error) {
                console.error("エラーが発生しました:", error);
            }
        });
    }
});
</script>
@endpush
