@extends('layouts.main')

@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">
    <h2 class="title">商品を出品</h2>

    <form id="sell-form" action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <div class="form-group image-upload">
                <label class="image-label">商品画像</label>
                    <div class="image-upload-box">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="image-input">
                        <label for="imageInput" class="image-button">画像を選択する</label>
                    </div>
                    <div class="error-message" id="error-image"></div>
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
                <div class="error-message" id="error-category"></div>
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
                <div class="error-message" id="error-condition"></div>
            </div>

            <div class="form-group product-name-description">
                <h2>商品名と説明</h2>
            </div>

            <div class="form-group product-name">
                <label class="name-label">商品名</label>
                <input type="text" name="name" class="name-input">
                <div class="error-message" id="error-name"></div>
            </div>

            <div class="form-group product-description">
                <label class="description-label">商品の説明</label>
                <textarea name="description" class="description-textarea" rows="4"></textarea>
                <div class="error-message" id="error-description"></div>
            </div>

            <div class="form-group price">
                <label class="price-label">販売価格</label>
                <input type="number" name="price" class="price-input" placeholder="¥">
                <div class="error-message" id="error-price"></div>
            </div>

            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".submit-button").addEventListener("click", async function (event) {
        event.preventDefault(); // フォームのデフォルト送信を防ぐ

        const form = document.querySelector("#sell-form");
        const formData = new FormData(form); // フォームデータを取得
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const fileInput = document.querySelector('input[name="image"]');

        // **CSRFトークンを追加**
        formData.append("_token", csrfToken);

        // **既存のエラーメッセージをクリア**
        document.querySelectorAll(".error-message").forEach(el => el.textContent = "");

        // **未入力チェック（フロントエンドでバリデーション）**
        let error = false;

        if (!formData.get("name")) {
            document.getElementById("error-name").textContent = "商品名を入力してください";
            error = true;
        }
        if (!formData.get("description")) {
            document.getElementById("error-description").textContent = "商品説明を入力してください";
            error = true;
        }
        if (!formData.get("price")) {
            document.getElementById("error-price").textContent = "価格を入力してください";
            error = true;
        }
        if (!formData.get("condition")) {
            document.getElementById("error-condition").textContent = "商品の状態を選択してください";
            error = true;
        }
        if (fileInput.files.length === 0) {
            document.getElementById("error-image").textContent = "商品画像をアップロードしてください";
            error = true;
        }

        if (error) {
            return; // バリデーションエラーがあれば送信しない
        }

        // **画像が正しく追加されているか確認**
        if (fileInput.files.length > 0) {
            formData.append("image", fileInput.files[0]);
        } else {
            console.warn("⚠️ 画像が選択されていません！");
        }

        // **デバッグ用: 送信データを確認**
        console.log("送信データ:", Object.fromEntries(formData.entries()));

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData // ヘッダーを設定しない（自動で multipart/form-data になる）
            });

            const data = await response.json();

            if (!response.ok) {
                throw data; // バリデーションエラーを投げる
            }

            console.log("成功:", data);
            alert("商品が出品されました！");
            window.location.href = "/sell"; // 成功時にリダイレクト

        } catch (error) {
            console.error("エラー発生:", error);

            if (error.errors) {
                console.log("エラーデータ:", error.errors); // エラーの詳細をコンソールに表示

                Object.keys(error.errors).forEach(key => {
                    const errorDiv = document.getElementById(`error-${key}`);
                    if (errorDiv) {
                        errorDiv.textContent = error.errors[key][0]; // 最初のエラーを表示
                        errorDiv.style.color = "rgba(255, 86, 85, 1)";
                    }
                });
            } else {
                alert("予期しないエラーが発生しました。");
            }
        }
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
