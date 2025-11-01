@extends('admin.layouts.app')

<<<<<<< HEAD
@if (@session('success'))
=======
@if (session('success'))
>>>>>>> 5b833b85b2c1795c4b56c34cd61d94684e33eca5
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

=======
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('title','Admin Dashboard')
@section('content')
    <h1 class="text-center mb-4">Trang quản trị</h1>
@endsection
>>>>>>> 5b833b85b2c1795c4b56c34cd61d94684e33eca5
