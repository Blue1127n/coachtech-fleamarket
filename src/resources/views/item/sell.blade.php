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
                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
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
                @error('category')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group condition-group">
                <label class="condition-label">商品の状態</label>
                <div class="custom-condition-select">
                    <div class="selected-option" id="selectedCondition">選択してください</div>
                        <div class="dropdown-options" id="dropdownOptions">
                        @foreach($conditions as $condition)
                            <div class="dropdown-option" data-value="{{ $condition->id }}">
                            <span class="check-icon"></span>{{ $condition->condition }}
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="condition" id="conditionInput">
                </div>
                @error('condition')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group product-name-description">
                <h2>商品名と説明</h2>
            </div>

            <div class="form-group product-name">
                <label class="name-label">商品名</label>
                <input type="text" name="name" class="name-input">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group product-description">
                <label class="description-label">商品の説明</label>
                <textarea name="description" class="description-textarea" rows="4"></textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group price">
                <label class="price-label">販売価格</label>
                <input type="number" name="price" class="price-input" placeholder="¥">
                @error('price')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // CSRFトークンを取得
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelector(".submit-button").addEventListener("click", function (event) {
        //event.preventDefault(); // フォームのデフォルト送信を防ぐ

        const form = document.querySelector("form");
        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error("エラーの詳細:", text);
                    throw new Error(text);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log("成功:", data);
            alert("商品が出品されました！");
            window.location.href = "/sell"; // 成功後にリダイレクト
        })
        .catch(error => {
            console.error("エラー:", error);
            error.response?.text().then(text => console.error("サーバーエラー詳細:", text));
            alert("エラーが発生しました。もう一度試してください。");
        });
    });
});

    // **カテゴリー選択の処理**
    const categoryOptions = document.querySelectorAll(".category-option");

    categoryOptions.forEach(option => {
        option.addEventListener("click", function () {
            const checkbox = this.querySelector(".category-checkbox");
            checkbox.checked = !checkbox.checked;
            this.classList.toggle("selected", checkbox.checked);
        });
    });

    // **商品の状態プルダウンの「✓」処理**
    const selectCondition = document.getElementById("condition_select");

    if (selectCondition) {
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
            }
        });

        // **選択時にドロップダウンを閉じる**
        selectCondition.addEventListener("change", function () {
            setTimeout(() => {
                selectCondition.blur(); // フォーカスを外して閉じる
            }, 100);
        });
    }

    // **カスタムドロップダウンの処理**
    const selectBox = document.querySelector(".custom-condition-select");
    const selectedOption = document.getElementById("selectedCondition");
    const dropdownOptions = document.getElementById("dropdownOptions");
    const options = document.querySelectorAll(".dropdown-option");
    const conditionInput = document.getElementById("conditionInput");

    if (selectBox) {
        selectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownOptions.style.display = dropdownOptions.style.display === "block" ? "none" : "block";
        });

        options.forEach(option => {
            option.addEventListener("click", function () {
                options.forEach(opt => opt.classList.remove("selected"));
                option.classList.add("selected");

                // **選択された項目を表示**
                selectedOption.textContent = option.textContent.trim();
                conditionInput.value = option.dataset.value;

                // **ドロップダウンを閉じる**
                dropdownOptions.style.display = "none";
            });
        });

        // **外部クリックでドロップダウンを閉じる**
        document.addEventListener("click", function (event) {
            if (!selectBox.contains(event.target) && !dropdownOptions.contains(event.target)) {
                dropdownOptions.style.display = "none";
            }
        });

        // **選択時に自動で閉じる**
        dropdownOptions.addEventListener("click", function () {
            setTimeout(() => {
                dropdownOptions.style.display = "none";
            }, 100);
        });
    }
</script>
@endpush
