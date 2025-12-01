@extends('admin.layouts.app')

@section('title', 'Danh sách liên hệ')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-envelope me-2"></i>Danh sách liên hệ
                </h3>
            </div>
        </div>
    </div>


    @if ($contacts->isEmpty())
        <p class="text-center">Chưa có liên hệ nào.</p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $index => $contact)
                        <tr>
                            <td data-label="STT">{{ $contacts->firstItem() + $index }}</td>
                            <td data-label="Tên">{{ $contact->name }}</td>
                            <td data-label="Email">{{ $contact->email }}</td>
                            <td data-label="Số điện thoại">{{ $contact->phone }}</td>
                            <td data-label="Địa chỉ">{{ $contact->address ?? '—' }}</td>
                            <td data-label="Trạng thái">
                                @if ($contact->status === 'processed')
                                    <span class="badge bg-success">Đã Xử lý</span>
                                @else
                                    <span class="badge bg-warning">Chưa Xử lý</span>
                                @endif
                            </td>
                            <td data-label="Ngày gửi">
                                @if($contact->contacted_at)
                                    {{ $contact->contacted_at->format('d/m/Y H:i') }}
                                @else
                                    {{ $contact->created_at->format('d/m/Y H:i') }}
                                @endif
                            </td>
                            <td data-label="Hành động">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="#" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

