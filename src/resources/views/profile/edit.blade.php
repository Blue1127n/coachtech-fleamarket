@extends('layouts.main')

@section('title', 'プロフィール編集')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        console.log('File selected:', input.files);
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
        }
    }

    document.getElementById('profile_image').addEventListener('change', (event) => {
    const file = event.target.files[0];
    console.log('選択されたファイル:', file);
    if (file) {
        console.log('ファイル名:', file.name);
        console.log('ファイルタイプ:', file.type);
        console.log('ファイルサイズ:', file.size);
    } else {
        console.log('ファイルが選択されていません');
    }
});

document.getElementById('profile-form').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    formData.append('_method', 'PUT');

    document.querySelectorAll('.error-message').forEach(el => el.innerText = '');

    fetch("{{ route('mypage.profile.update') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json"
        },
        body: formData
    })
    .then(response => response.json().then(body => ({ status: response.status, body })))
    .then(({ status, body }) => {
        if (status === 422) {
            console.log("バリデーションエラー:", body.errors);

            if (body.errors.profile_image) {
                document.getElementById('profile_image-error').innerText = body.errors.profile_image[0];
            }
            if (body.errors.name) {
                document.getElementById('name-error').innerText = body.errors.name[0];
            }
            if (body.errors.postal_code) {
                document.getElementById('postal_code-error').innerText = body.errors.postal_code[0];
            }
            if (body.errors.address) {
                document.getElementById('address-error').innerText = body.errors.address[0];
            }
            if (body.errors.building) {
                document.getElementById('building-error').innerText = body.errors.building[0];
            }
        } else if (status === 200) {
            console.log("リダイレクト先:", body.redirect_url);
            if (body.redirect_url) {
                window.location.href = body.redirect_url;
            }
        }
    })
    .catch(error => {
        console.error('通信エラー:', error);
    });
});
</script>
@endpush

@section('content')
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>

    <form id="profile-form" action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

    <div class="profile-image-section">
        <div class="image-preview">
        <img
        id="preview"
        src="{{ session('profile_image_temp') ?: (auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '') }}" 
        alt=""
        class="{{ session('profile_image_temp') || auth()->user()->profile_image ? 'show' : '' }}">
        @if (!auth()->user()->profile_image && !session('profile_image_temp'))
            <div id="placeholder" class="placeholder"></div>
        @endif
        </div>

        <label class="btn-select-image">
            画像を選択する
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)" style="display: none;">
        </label>
        <div class="error-message" id="profile_image-error"></div>
        @error('profile_image')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}">
            <div class="error-message" id="name-error"></div>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
            <div class="error-message" id="postal_code-error"></div>
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}">
            <div class="error-message" id="address-error"></div>
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}">
            <div class="error-message" id="building-error"></div>
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection

