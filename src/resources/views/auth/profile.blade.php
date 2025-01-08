@extends('layouts.main')

@section('title', 'プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>
@endpush

@section('content')
<div class="profile-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="profile_image">プロフィール画像</label>
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)">
            <div class="image-preview">
                <img id="preview" src="#" alt="プロフィール画像を選択してください">
            </div>
        </div>
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
        </div>
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required>
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" required>
        </div>
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building') }}">
        </div>
        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection
