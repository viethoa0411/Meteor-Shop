@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@push('styles')
    <style>
        /* 40% ảnh (trái) – 60% thông tin (phải) cho màn ≥ 992px */
        @media (min-width: 992px) {
            .col-left-40 {
                flex: 0 0 40%;
                max-width: 40%;
            }

            .col-right-60 {
                flex: 0 0 60%;
                max-width: 60%;
            }
        }

        .product-cover {
            border: 1px solid #e9ecef;
            border-radius: .5rem;
            overflow: hidden;
            background: #f8f9fa;
        }

        .product-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .img-zoom:hover {
            transform: scale(1.02);
            transition: .25s ease;
        }

        .badge-soft {
            padding: .5rem .75rem;
            border-radius: 50rem;
            font-weight: 600;
        }

        .badge-active {
            background: #e6f4ea;
            color: #137333;
        }

        .badge-inactive {
            background: #eceff1;
            color: #455a64;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary mb-3">← Danh sách</a>

        <div class="row g-4 align-items-start">
            {{-- LEFT: Ảnh --}}
            <div class="col-12 col-lg-5 col-xl-4 col-left-40">
                {{-- Ảnh đại diện --}}
                <div class="product-cover mb-3 ratio ratio-1x1">
                    @if ($product->image)
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="img-zoom rounded" style="transition: opacity 0.3s ease;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-secondary">
                            Không có ảnh
                        </div>
                    @endif
                </div>

                {{-- Gallery ảnh phụ --}}
                @if ($product->images && $product->images->count() > 0)
                    <div class="row g-2 mt-3">
                        @foreach ($product->images as $img)
                            <div class="col-3 col-md-2">
                                    <div class="thumbnail-img" style="
                                            width:100%;
                                            aspect-ratio: 1/1;
                                            border:1px solid #e0e0e0;
                                            border-radius:8px;
                                            overflow:hidden;
                                            cursor:pointer;
                                            background:#fff;
                                            box-shadow:0 2px 6px rgba(0,0,0,0.06);
                                            transition:all 0.25s ease-in-out;"
                                        onclick="
                                        document.getElementById('mainImage').src='{{ asset('storage/' . $img->image) }}';
                                        document.querySelectorAll('.thumbnail-img').forEach(el => el.style.borderColor = '#e0e0e0');
                                        this.style.borderColor = '#0d6efd';
                                    "                                    
                                        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 6px 14px rgba(0,0,0,0.12)';"
                                        onmouseout="if(this.style.borderColor !== 'rgb(13, 110, 253)') { this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.06)'; }">                                    <img src="{{ asset('storage/' . $img->image) }}" alt="Ảnh phụ"
                                        style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s ease-in-out;"
                                        onmouseover="this.style.transform='scale(1.12)'"
                                        onmouseout="this.style.transform='scale(1)'">

                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Trạng thái --}}
                <div class="mt-3">
                    <span class="badge badge-soft {{ $product->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ strtoupper($product->status) }}
                    </span>
                </div>
            </div>

            {{-- RIGHT: Thông tin --}}
            <div class="col-12 col-lg-7 col-xl-8 col-right-60">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <div class="fw-semibold">Tên sản phẩm</div>
                                <div class="text-muted fw-semibold">{{ $product->name }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold">Giá</div>
                                <div class="text-muted fw-semibold red">{{ number_format($product->price, 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <hr>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="fw-semibold">Danh mục</div>
                                <div class="text-muted small">{{ $product->category->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold">Cập nhật</div>
                                <div class="text-muted small">{{ $product->updated_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-semibold">Trạng thái</div>
                                <div class="text-muted small">{{ $product->status }}</div>
                            </div>
                        </div>

                        {{-- Biến thể --}}
                        <h5 class="mb-3">Danh sách biến thể</h5>
                        @if ($product->variants->count())
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>#</th>
                                            <th>Màu</th>
                                            <th>Kích thước (D × R × C)</th>
                                            <th>Giá</th>
                                            <th>Tồn kho</th>
                                            <th>Cân nặng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($product->variants as $i => $v)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    @if ($v->color_code)
                                                        <span class="d-inline-block rounded border"
                                                            style="width:25px;height:25px;background:{{ $v->color_code }}"></span>
                                                        <div>{{ $v->color_name ?? $v->color_code }}</div>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($v->length || $v->width || $v->height)
                                                        {{ $v->length ?? '—' }} × {{ $v->width ?? '—' }} ×
                                                        {{ $v->height ?? '—' }} cm
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ number_format($v->price ?? $product->price, 0, ',', '.') }} đ</td>
                                                <td>{{ $v->stock }}</td>
                                                <td>{{ $v->weight }}{{ $v->weight_unit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Sản phẩm này chưa có biến thể nào.</p>
                        @endif

                        {{-- Mô tả --}}
                        <div class="mt-3">
                            <div class="fw-semibold">Mô tả</div>
                            <div class="lh-base">
                                {!! nl2br(e($product->description)) ?: '<span class="text-secondary">—</span>' !!}
                            </div>
                        </div>

                        {{-- Action --}}
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="btn btn-primary btn-sm">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
