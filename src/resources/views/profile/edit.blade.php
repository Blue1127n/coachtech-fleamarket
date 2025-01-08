@extends('layouts.main')

@section('title', 'プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
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
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="profile_image">プロフィール画像</label>
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)">
            <div class="image-preview">
                @if(auth()->user()->profile_image)
                    <img id="preview" src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="現在のプロフィール画像">
                @else
                    <img id="preview" src="#" alt="プロフィール画像を選択してください">
                @endif
            </div>
            @error('profile_image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}" required>
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}" required>
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection
