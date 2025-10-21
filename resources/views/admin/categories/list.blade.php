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

@section('title','Danh sách danh mục')
@section('content')
    <h1 class="text-center mb-4">Danh sách danh mục</h1>
@endsection
