@extends('layouts.main')

@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">
    <h2 class="title">商品を出品</h2>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <div class="form-group image-upload">
                <label class="image-label">商品画像</label>
                    <div class="image-upload-box">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="image-input">
                        <label for="imageInput" class="image-button">画像を選択する</label>
                    </div>
            </div>

            <div class="form-group product-details">
                <h2>商品の詳細</h2>
            </div>

            <div class="form-group category-group">
                <label class="category-label">カテゴリー</label>
                <div class="category-options">
                    @foreach($categories as $category)
                        <label class="category-option">
                            <input type="checkbox" name="category[]" value="{{ $category->id }}" class="category-checkbox">
                            <span class="category-name">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group condition-group">
                <label class="condition-label">商品の状態</label>
                <select name="condition_id" id="condition_select" class="condition-select" style="background-image: url('{{ asset('storage/items/triangle.svg') }}');">
                    <option value="" class="default-option" disabled selected hidden>選択してください</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}">{{ $condition->condition }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group product-name-description">
                <h2>商品名と説明</h2>
            </div>

            <div class="form-group product-name">
                <label class="name-label">商品名</label>
                <input type="text" name="name" class="name-input" required>
            </div>

            <div class="form-group product-description">
                <label class="description-label">商品の説明</label>
                <textarea name="description" class="description-textarea" rows="4" required></textarea>
            </div>

            <div class="form-group price">
                <label class="price-label">販売価格</label>
                <input type="number" name="price" class="price-input" required placeholder="¥">
            </div>

            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const categoryOptions = document.querySelectorAll(".category-option");

    categoryOptions.forEach(option => {
        option.addEventListener("click", function () {
            const checkbox = this.querySelector(".category-checkbox");
            checkbox.checked = !checkbox.checked; // チェックのON/OFFを切り替え

            // クラスを切り替えて選択状態の見た目を変更
            this.classList.toggle("selected", checkbox.checked);
        });
    });

    // **商品の状態プルダウンの「✓」処理**
    const selectCondition = document.getElementById("condition_select");

    if (selectCondition) {
        // **オプションの元のテキストを dataset に保存**
        Array.from(selectCondition.options).forEach(option => {
            option.dataset.originalText = option.textContent;
        });

        // **マウスホバー時に ✓ を追加**
        selectCondition.addEventListener("mouseover", function (event) {
            if (event.target.tagName === "OPTION") {
                event.target.textContent = `✓ ${event.target.dataset.originalText}`;
            }
        });

        // **マウスが離れたら元のテキストに戻す**
        selectCondition.addEventListener("mouseout", function (event) {
            if (event.target.tagName === "OPTION") {
                event.target.textContent = event.target.dataset.originalText;
        });
    }
});

</script>
@endpush
