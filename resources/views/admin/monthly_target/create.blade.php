@extends('admin.layouts.app')

@section('title', 'Đặt mục tiêu tháng')

@section('content')
<div class="container mt-4">
    <h3>Đặt mục tiêu tháng {{ now()->month }}/{{ now()->year }}</h3>

    <form action="{{ route('admin.monthly_target.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="target_amount" class="form-label">Mục tiêu doanh thu (VNĐ)</label>
            <input type="number" class="form-control" id="target_amount" name="target_amount" required min="0">
        </div>
        <button type="submit" class="btn btn-primary">Lưu mục tiêu</button>
    </form>
</div>
@endsection
