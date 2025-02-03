@extends('layouts.app')

@section('content')
<div class="sell-container">
    <h2 class="title">商品を出品</h2>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-group">
            <label class="image-label">商品画像</label>
            <input type="file" name="image" class="image-input" required>
        </div>

        {{-- カテゴリー --}}
        <div class="form-group">
            <label class="category-label">カテゴリー</label>
            <div class="category-options">
                @foreach($categories as $category)
                    <label class="category-option">
                        <input type="checkbox" name="category[]" value="{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- 商品の状態 --}}
        <div class="form-group">
            <label class="condition-label">商品の状態</label>
            <select name="condition_id" class="condition-select" required>
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
                @endforeach
            </select>
        </div>

        {{-- 商品名 --}}
        <div class="form-group">
            <label class="name-label">商品名</label>
            <input type="text" name="name" class="name-input" required>
        </div>

        {{-- 商品の説明 --}}
        <div class="form-group">
            <label class="description-label">商品の説明</label>
            <textarea name="description" class="description-textarea" rows="4" required></textarea>
        </div>

        {{-- 販売価格 --}}
        <div class="form-group">
            <label class="price-label">販売価格 (円)</label>
            <input type="number" name="price" class="price-input" required>
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</div>
@endsection
