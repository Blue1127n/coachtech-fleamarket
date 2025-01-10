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
        const placeholder = document.getElementById('placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                placeholder.style.display = 'none';
            }
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
            if (placeholder) {
            placeholder.style.display = 'block';
        }
        }
    }
</script>
@endpush

@section('content')
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-image-section">
        <div class="image-preview">
        <img 
        id="preview" 
        src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '#' }}" 
        alt="プロフィール画像" 
        style="display: {{ auth()->user()->profile_image ? 'block' : 'none' }};">
    @if(!auth()->user()->profile_image)
        <div id="placeholder" class="placeholder"></div>
    @endif
</div>
        <label class="btn-select-image">
            画像を選択する
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)" style="display: none;">
        </label>
        @error('profile_image')
            <div class="error-message">{{ $message }}</div>
        @enderror
        </div>

        <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}">
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
