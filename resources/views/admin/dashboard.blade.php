@extends('admin.layouts.app')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@section('title','Admin Dashboard')
@section('content')
    <h1 class="text-center mb-4">Trang quản trị</h1>
@endsection
