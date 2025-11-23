@extends('client.layouts.app')

@section('title', 'Trang chủ')
@section('content')

    {{-- Banner Carousel --}}
    <section class="banner-carousel-wrapper">
        @if ($banners->count() > 0)
            <div class="banner-carousel" id="bannerCarousel">
                @foreach ($banners as $index => $banner)
                    <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                        <div class="banner-image-wrapper">
                            @if ($banner->image_url)
                                <img class="banner-image" 
                                     src="{{ $banner->image_url }}" 
                                     alt="{{ $banner->title ?? 'Banner' }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/1920x800?text=Banner';">
                            @else
                                <img class="banner-image" 
                                     src="https://via.placeholder.com/1920x800?text=Banner" 
                                     alt="Banner"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            @endif
                            <div class="banner-overlay"></div>
                        </div>
                        
                        <div class="banner-content">
                            <div class="container">
                                <div class="banner-content-inner">
                                    @if ($banner->title)
                                        <h2 class="banner-title">{{ $banner->title }}</h2>
                                    @endif
                                    @if ($banner->description)
                                        <p class="banner-description">{{ $banner->description }}</p>
                                    @endif
                                    @if ($banner->link)
                                        <a href="{{ $banner->link }}" class="banner-btn">
                                            <span>Khám phá ngay</span>
                                            
                                        </a>
                                    @else
                                        <button class="banner-btn" onclick="alert('{{ $banner->title ?? 'Banner' }}')">
                                            <span>Khám phá ngay</span>
                                    
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Navigation Controls --}}
            <button class="banner-nav-btn banner-prev" aria-label="Previous banner">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="banner-nav-btn banner-next" aria-label="Next banner">
                <i class="bi bi-chevron-right"></i>
            </button>

            {{-- Indicators --}}
            <div class="banner-indicators">
                @foreach ($banners as $index => $banner)
                    <button class="banner-indicator {{ $index === 0 ? 'active' : '' }}" 
                            data-slide-to="{{ $index }}" 
                            aria-label="Go to slide {{ $index + 1 }}"></button>
                @endforeach
            </div>

            {{-- Progress Bar --}}
            <div class="banner-progress">
                <div class="banner-progress-bar"></div>
            </div>
        @else
            {{-- Fallback nếu không có banner --}}
            <div class="banner-carousel">
                <div class="banner-slide active">
                    <div class="banner-image-wrapper">
                        <img class="banner-image" 
                             src="https://picsum.photos/1920/800?random=1" 
                             alt="Welcome Banner">
                        <div class="banner-overlay"></div>
                    </div>
                    <div class="banner-content">
                        <div class="container">
                            <div class="banner-content-inner">
                                <h2 class="banner-title">Chào mừng đến với Meteor Shop</h2>
                                <p class="banner-description">Khám phá bộ sưu tập nội thất hiện đại và sang trọng</p>
                                <button class="banner-btn" onclick="alert('Khám phá ngay')">
                                    <span>Khám phá ngay</span>
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- Sản phẩm mới --}}
    <div class="product" style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
        <h2
            style="padding-left: 20px; font-size:20px; font-weight:600; margin-bottom:8px; display:flex; align-items: center; gap:8px">
            Sản phẩm mới
            <span class="badge-new">New</span>
        </h2>
        <p style="padding-left: 20px; color:#555; font-size:14px; margin:14px; margin:0 0 24px">
            Hàng mới cập nhật gần đây.
        </p>
        <hr style="margin-left: 20px; margin-right: 20px">

        @if ($newProducts->count() === 0)
            <p>Hiện chưa có sản phẩm.</p>
        @else
            <div class="grid-products">
                @foreach ($newProducts as $p)
                    <a href="{{ route('client.product.detail', $p->slug) }}" class="product-card">
                        <div class="product-img">
                            <img src="{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                alt="{{ $p->name }}">
                        </div>
                        <div class="product-name">{{ $p->name }}</div>
                        <div class="product-price">
                            {{ number_format($p->price, 0, ',', '.') }} đ
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    {{-- end  --}}

    {{-- danh mục theo đồ --}}
    <div style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
        <div class="room" style="padding-top: 25px; display:grid; grid-template-columns:repeat(3,1fr); gap:16px">
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=11" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">

                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Sofa</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=21" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Giường</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=31" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Bàn làm việc</h4>
            </div>
        </div>
    </div>
    {{-- end  --}}

    {{--  Sản phẩm bán chạy --}}
    <div class="product" style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
        <h2
            style="padding-left: 20px; font-size:20px; font-weight:600; margin-bottom:8px; display:flex; align-items: center; gap:8px">
            Sản phẩm bán chạy
            <span class="badge-new">New</span>
        </h2>
        <p style="padding-left: 20px; color:#555; font-size:14px; margin:14px; margin:0 0 24px">
            Hàng mới nổi bật.
        </p>
        <hr style="margin-left: 20px; margin-right:20px; ">
        @if ($outstandingProducts->count() === 0)
            <p>Hiện chưa có sản phẩm.</p>
        @else
            <div class="grid-products">
                @foreach ($outstandingProducts as $o)
                    <a href="{{ route('client.product.detail', $o->slug) }}" class="product-card">
                        <div class="product-img">
                            <img src="{{ $o->image ? asset('storage/' . $o->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}"
                                alt="{{ $o->name }}">
                        </div>
                        <div class="product-name">{{ $o->name }}</div>
                        <div class="product-price">
                            {{ number_format($o->price, 0, ',', '.') }} đ
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    {{-- end --}}

    {{-- bai content 1 --}}
    <div
        style="display: flex; width:100%; height:130vh; gap:24px;padding:100px 10px 100px 0; box-sizing:border-box; background-color: rgb(1,49,49)">
        <div style="width: 50%; height:100%;overflow:hidden;">
            <img src="https://picsum.photos/1000/800?random=1"
                style="width:100%; height: 100%; object-fit:cover;transition:1s;"
                onmouseover="this.style.transform='scale(1.3)'" onmouseout="this.style.transform='scale(1)'">
        </div>

        <div
            style="width: 50%; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:20px; text-align:center;">
            <div style="width: 70%; height:40vh;overflow:hidden; border-radius:10px">
                <img src="https://picsum.photos/600/400?random=2" style="width:100%; height: 100%; object-fit:cover">
            </div>
            <div>
                <h2 style="font-size:24px; font-weight:600; margin:12px 0; color:#fff">Phong cách hiện đại</h2>
                <p style="color: #cecece; fonr-size:14px; line-height:1.6; margin-bottom:20px; margin:50px">
                    Khám phá bộ sưu tập nội thất mới nhất mang đậm hơi thở đương đại.
                    Ra đời vào năm 2024, là một trong những thương hiệu tiên phong
                    trong ngành nội thất, với nguồn cảm hứng văn hóa Việt và gu thẩm mỹ
                    tinh tế. Qua 26 năm hoạt động, Nhà Xinh luôn chú trọng đổi mới để
                    duy trì vị thế là thương hiệu nội thất hàng đầu tại Việt Nam.
                </p>
                <button
                    style="background: #393939; color:#fff; border:none; padding:10px 24px; border-radius: 6px; cursor: pointer; transition:.3s "
                    onmouseover="this.style.background=#444" onmouseout="this.style.background='#222'">Xem ngay</button>
            </div>
        </div>
    </div>
    {{--  --}}


    {{-- danh mục theo loại phòng --}}
    <div style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
        <div class="room" style="padding-top: 25px; display:grid; grid-template-columns:repeat(4,1fr); gap:16px">
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=11" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng khách</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=121" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng ngủ</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=131" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng ăn</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=41" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform=scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng làm việc</h4>
            </div>
        </div>
    </div>
    {{-- end  --}}


    {{-- goc chia sẻ --}}
    <div class="container" style="max-width:1200px; margin:0 auto; padding:40px 15px;">

        <div style="text-align:center; margin-bottom:30px;">
            <h2 style="font-size: 28px; font-weight:700; margin:0;">Góc Cảm Hứng</h2>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach ($latestBlogs as $blog)
                <div class="col-12 col-md-6">
                    <div class="article-card"
                        style="
                    height:100%;
                    background:#fff;
                    border-radius:10px;
                    overflow:hidden;
                    text-align:left;
                    box-shadow:0 4px 12px rgba(0,0,0,0.1);
                    transition: all 0.4s ease;
                    box-sizing:border-box;
                ">
                        <img src="{{ $blog->thumbnail ? asset('blogs/images/' . $blog->thumbnail) : 'https://picsum.photos/800/600?random=' . rand(10, 99) }}"
                            alt="{{ $blog->title }}" style="width:100%; height:300px; object-fit:cover; display:block;">

                        <h3 style="font-size:20px; font-weight:600; margin:16px;">
                            <a href="{{ route('client.blog.show', $blog->slug) }}"
                                style="text-decoration:none; color:#222; display:inline-block;">
                                {{ $blog->title }}
                            </a>
                        </h3>

                        <p style="font-size:14px; color:#555; line-height:1.6; margin:0 16px 20px;">
                            {{ $blog->excerpt }}
                        </p>

                        <a href="{{ route('client.blog.show', $blog->slug) }}"
                            style="
                            display:inline-block;
                            margin: 0 16px 20px;
                            font-size:18px;
                            font-weight:600;
                            color:#007bff;
                            text-decoration:none;
                            transition:0.3s;
                        ">
                            Đọc thêm →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{--  --}}



    {{-- bai content 2 --}}
    <div
        style="display:flex;width:100%;height:60vh;gap:24px;padding:0;           
                    box-sizing:border-box;background-color: rgb(4, 52, 110)">
        <div
            style="width:50%;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:20px;text-align:center;">
            <h2 style="font-size:24px;font-weight:600;margin:12px 0; color:#fff">Phong cách hiện đại</h2>
            <p
                style="color:#cecece;font-size:14px;line-height:1.6
                        ;margin-bottom:20px;margin:50px">
                Khám phá bộ sưu tập nội thất mới nhất mang đậm hơi thở đương đại.
                Ra đời vào năm 2024, là một trong những thương hiệu tiên phong
                trong ngành nội thất, với nguồn cảm hứng văn hóa Việt và gu thẩm mỹ
                tinh tế. Qua 26 năm hoạt động, Nhà Xinh luôn chú trọng đổi mới để
                duy trì vị thế là thương hiệu nội thất hàng đầu tại Việt Nam.
            </p>
            <button
                style="background:#393939;color:#fff;border:none;
                            padding:10px 24px; border-radius:6px;cursor:pointer;transition:.3s;"
                onmouseover="this.style.background='#444'" onmouseout="this.style.background='#222'">Xem ngay</button>
        </div>
        <div style="width:50%;height:100%;overflow:hidden;;">
            <img src="https://picsum.photos/1000/800?random=9"
                style="width:100%;height:100%;object-fit:cover;transition:1s;"
                onmouseover="this.style.transform='scale(1.3)'" onmouseout="this.style.transform='scale(1)'">
        </div>
    </div>

    {{--  --}}

    @push('styles')
    <link rel="stylesheet" href="{{ route('assets.css', ['file' => 'banner-carousel']) }}">
    @endpush

    @push('scripts')
    <script src="{{ route('assets.js', ['file' => 'banner-carousel']) }}"></script>
    @endpush


@endsection
