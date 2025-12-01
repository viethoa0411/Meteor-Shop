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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                
                {{-- Bộ lọc trạng thái --}}
                <div class="d-flex gap-1 align-items-center">
                    <a href="{{ route('admin.contacts.index', ['status' => 'all'] + request()->except('status')) }}"
                       class="btn {{ request('status', 'all') == 'all' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm px-2 py-1" style="font-size: 0.85rem;">
                        <i class="bi bi-list-ul"></i> Tất cả
                    </a>
                    <a href="{{ route('admin.contacts.index', ['status' => 'processed'] + request()->except('status')) }}"
                       class="btn {{ request('status', 'all') == 'processed' ? 'btn-success' : 'btn-outline-success' }} btn-sm px-2 py-1" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle-fill"></i> Đã Xử lý
                    </a>
                    <a href="{{ route('admin.contacts.index', ['status' => 'pending'] + request()->except('status')) }}"
                       class="btn {{ request('status', 'all') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm px-2 py-1" style="font-size: 0.85rem;">
                        <i class="bi bi-clock-history"></i> Chưa Xử lý
                    </a>
                </div>


                {{-- Ô tìm kiếm --}}
                <form action="{{ route('admin.contacts.index') }}" method="GET"
                    class="d-flex align-items-center flex-grow-1 mx-md-4" style="max-width: 500px;">
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">

                    <div class="input-group w-100">
                        <input type="text" name="keyword" class="form-control"
                            placeholder="VD: tên, email hoặc số điện thoại..." value="{{ request('keyword') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        @if (request('keyword'))
                            <a href="{{ route('admin.contacts.index', ['status' => request('status', 'all')]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </form>
                
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
                                    <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Xem Chi Tiết 
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
        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $contacts->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection

