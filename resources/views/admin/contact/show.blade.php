@extends('admin.layouts.app')

@section('title', 'Chi tiết liên hệ')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Chi tiết liên hệ</h1>
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-envelope"></i> Thông tin liên hệ</h5>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tên:</strong>
                        <p>{{ $contact->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p>{{ $contact->email }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Số điện thoại:</strong>
                        <p>{{ $contact->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Địa chỉ:</strong>
                        <p>{{ $contact->address ?? '—' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Trạng thái:</strong>
                        <p>
                            @if ($contact->status === 'processed')
                                <span class="badge bg-success">Đã Xử lý</span>
                            @else
                                <span class="badge bg-warning">Chưa Xử lý</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Ngày gửi:</strong>
                        <p>
                            @if($contact->contacted_at)
                                {{ $contact->contacted_at->format('d/m/Y H:i') }}
                            @else
                                {{ $contact->created_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Ngày tạo:</strong>
                        <p>{{ $contact->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Cập nhật lần cuối:</strong>
                        <p>{{ $contact->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

