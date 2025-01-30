@extends('layouts.main')

@section('title', '住所の変更')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/address_change.css') }}">
@endpush

@section('content')
<div class="address-change-container">
    <h1 class="page-title">住所の変更</h1>

    <form action="{{ route('item.updateAddress', ['item_id' => $item->id]) }}" method="POST">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', auth()->user()->postal_code ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', auth()->user()->address ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', auth()->user()->building ?? '') }}">
        </div>

        <div class="form-group">
            <button type="submit" class="update-btn">更新する</button>
        </div>
    </form>
</div>
@endsection
