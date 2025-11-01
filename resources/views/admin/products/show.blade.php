@extends('admin.layouts.app')
    @section('title', 'Chi tiết sản phẩm')

        @push('styles')
            <style>
                /* 40% ảnh (trái) – 60% thông tin (phải) cho màn ≥ 992px */
                @media (min-width: 992px) {
                    .col-left-40 { flex: 0 0 40%; max-width: 40%; }
                    .col-right-60 { flex: 0 0 60%; max-width: 60%; }
                }
                /* Khung ảnh đẹp & giữ tỉ lệ */
                .product-cover {
                    border: 1px solid #e9ecef; border-radius: .5rem; overflow: hidden;
                    background: #f8f9fa;
                }
                .product-cover img {
                    width: 100%; height: 100%; object-fit: cover;
                }
                .img-zoom:hover { transform: scale(1.02); transition: .25s ease; }
                /* Nhãn trạng thái */
                .badge-soft { padding: .5rem .75rem; border-radius: 50rem; font-weight: 600; }
                .badge-active   { background: #e6f4ea; color: #137333; }
                .badge-inactive { background: #eceff1; color: #455a64; }
            </style>
        @endpush

@section('content')
    <div class="container-fluid py-4">
        <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary">
            ← Danh sách
        </a>
        <div class="d-flex align-items-center my-3">
            <h1 class="h4 mb-0">Chi tiết: <strong> {{ $product->name }}</strong></h1>    
        </div>

        <div class="row g-4 align-items-start">
            {{-- LEFT: Ảnh (≈40%) --}}
        <div class="col-12 col-sm-2 col-md-4 col-lg-5 col-xl-5 col-xxl-5">   {{-- ~42% ở lg; ~33% ở xl --}}
            <div class="product-cover ratio ratio-1x1">
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="img-zoom">
            
                @if($product->image)
                <img
                    src="{{ asset('storage/'.$product->image) }}"
                    alt="{{ $product->name }}"
                    class="w-100 h-100 object-fit-cover rounded img-zoom"
                >
                @else
                <div class="d-flex align-items-center justify-content-center h-auto text-secondary">
                    Không có ảnh
                </div>
                @endif
            </div>

            {{-- Thông tin ngắn dưới ảnh (tuỳ thích) --}}
            <div class="mt-3">
                <span class="badge badge-soft {{ $product->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                {{ strtoupper($product->status) }}
                </span>
            </div>
            </div>

            {{-- RIGHT: Thông tin (≈60%) --}}
            <div class="col-12 col-sm-9 col-md-8 col-lg-7 col-xl-7 col-xxl-7">   {{-- ~58% ở lg; ~67% ở xl --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <div class="text-muted small">Slug</div>
                                <div class="fw-semibold">{{ $product->slug }}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small">Giá</div>
                                    <div class="fs-4 fw-bold">
                                        {{ number_format($product->price, 0, ',', '.') }} đ
                                    </div>
                                </div>
                            </div>

                            <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Tồn kho</div>
                                <div class="fw-semibold">{{ $product->stock }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Danh mục</div>
                                <div class="fw-semibold">{{ $product->category->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Thương hiệu</div>
                                <div class="fw-semibold">{{ $product->brand->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Trạng thái</div>
                                <div class="fw-semibold">{{ $product->status}}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Cập nhật</div>
                                <div class="fw-semibold">{{ $product->updated_at?->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                        <hr>

                        {{-- Biến thể sản phẩm --}}
                            <h5 class="mb-3">Danh sách biến thể</h5>
                            @if ($product->variants->count())
                                <div class="table table-border align-middle">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th>Màu</th>
                                                <th>Kích thước (D × R × C)</th>
                                                <th>Giá</th>
                                                <th>Tồn kho</th>
                                                <th>SKU</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variants as $i =>$v)
                                                <tr class="text-center">
                                                    <td>{{ $i+1 }}</td>
                                                    <td>
                                                        @if ($v->color_code)
                                                            <span class="d-inline-block rounded border" style="width: 25px; height:25px;background:{{ $v->color_code }}" ></span>
                                                            <div>{{ $v->color_name ?? $v->color_code }}</div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($v->length || $v->width || $v->height)
                                                            {{ $v->length ?? '-' }}  × {{  $v->width ?? '-' }}  × {{ $v->height ?? '-' }} cm
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($v->price ?? $product->price, 0, ',', '.') }} đ</td>
                                                    <td>{{ $v->stock}}</td>
                                                    <td>{{ $v->sku ?? '-'}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else 
                                <p class="text-muted"> Sản phẩm này chưa có biến thể nào. </p>                                
                            @endif
                        {{-- End biến thể sản phẩm --}}
                        
                        <div>
                            <div class="text-muted small mb-1">Mô tả</div>
                            <div class="lh-base">
                            {!! nl2br(e($product->description)) ?: '<span class="text-secondary">—</span>' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action phụ --}}
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">Chỉnh sửa</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                        onsubmit="return confirm('Chuyển sản phẩm vào thùng rác?')">
                    @csrf @method('DELETE')
                    <button class=" btn btn-danger">Xoá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
