@extends('client.layouts.app')

@section('title', 'Tư Vấn Thiết Kế - Meteor Shop')

@push('head')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #111 0%, #333 100%);
        color: #fff;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 50px;
    }

    .contact-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .contact-hero p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto;
    }

    .contact-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px 60px;
    }

    .contact-form-card {
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .contact-form-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 30px;
        color: #111;
        text-align: center;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-label .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #ffb703;
        box-shadow: 0 0 0 3px rgba(255, 183, 3, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 6px;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        background: #111;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: #ffb703;
        color: #111;
        transform: translateY(-2px);
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .contact-hero {
            padding: 40px 0;
        }

        .contact-hero h1 {
            font-size: 2rem;
        }

        .contact-form-card {
            padding: 30px 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="contact-hero">
    <div class="contact-container">
        <h1>Tư Vấn Thiết Kế</h1>
        <p>Để lại thông tin liên hệ, chúng tôi sẽ tư vấn và hỗ trợ bạn thiết kế không gian sống hoàn hảo</p>
    </div>
</div>

<div class="contact-container">
    <div class="contact-form-card">
        <h2 class="contact-form-title">Thông tin liên hệ</h2>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('client.contact.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">
                    Tên <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       placeholder="Nhập họ và tên">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    Email <span class="required">*</span>
                </label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       placeholder="Nhập địa chỉ email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">
                    Số điện thoại <span class="required">*</span>
                </label>
                <input type="text" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       placeholder="Nhập số điện thoại">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">
                    Địa chỉ
                </label>
                <textarea class="form-control @error('address') is-invalid @enderror" 
                          id="address" 
                          name="address" 
                          rows="3"
                          placeholder="Nhập địa chỉ (tùy chọn)">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-send"></i> Gửi liên hệ
            </button>
        </form>
    </div>
</div>
@endsection

