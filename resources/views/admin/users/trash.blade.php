@extends('admin.layouts.app')

@section('title', 'Thùng rác người dùng')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Thùng rác người dùng</h1>
        <a href="{{ route('admin.users.list') }}" class="btn btn-secondary">⬅ Quay lại danh sách</a>
    </div>

    @if ($users->isEmpty())
        <p class="text-center">Không có người dùng nào trong thùng rác.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày xóa</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">
                                        <i class="bi bi-arrow-clockwise"></i> Khôi phục
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    @endif
@endsection
