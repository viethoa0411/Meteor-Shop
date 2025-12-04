@extends('client.layouts.app')

@section('title', 'Trang chủ')
@section('content')

    {{-- Hero Banner / Slider --}}
    <section class="hero-banner">
        <div class="hero-banner-inner">
            @if ($banners->count() > 0)
                <div class="hero-banner-viewport">
                    <div class="hero-banner-track">
                        @foreach ($banners as $index => $banner)
                            @php
                                $bannerImage = $banner->image_url ?? null;
                                $isFirst = $index === 0;
                            @endphp
                            <div class="hero-slide">
                                @if ($bannerImage)
                                    <img class="hero-slide-image"
                                        src="{{ $bannerImage }}"
                                        @unless($isFirst) loading="lazy" @endunless
                                        alt="{{ $banner->title ?? 'Banner trang chủ Meteor Shop' }}"
                                        onerror="this.src='https://via.placeholder.com/1600x700?text=Banner';">
                                @else
                                    <img class="hero-slide-image"
                                        src="https://via.placeholder.com/1600x700?text=Banner"
                                        @unless($isFirst) loading="lazy" @endunless
                                        alt="Banner trang chủ Meteor Shop">
                                @endif

                                <div class="hero-slide-overlay"></div>

                                <div class="hero-slide-content container">
                                    @if (!empty($banner->title))
                                        <h1 class="hero-slide-title">
                                            {{ $banner->title }}
                                        </h1>
                                    @else
                                        <h1 class="hero-slide-title">
                                            Meteor Shop – Nội thất hiện đại
                                        </h1>
                                    @endif

                                    @if (!empty($banner->description))
                                        <p class="hero-slide-subtitle">
                                            {{ $banner->description }}
                                        </p>
                                    @endif

                                    @if (!empty($banner->link))
                                        <a href="{{ $banner->link }}"
                                            class="hero-slide-cta"
                                            title="Xem chi tiết {{ $banner->title ?? 'banner' }}">
                                            Xem ngay
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($banners->count() > 1)
                    <button class="hero-nav hero-nav-prev" type="button" aria-label="Banner trước">
                        <span>&lt;</span>
                    </button>
                    <button class="hero-nav hero-nav-next" type="button" aria-label="Banner tiếp theo">
                        <span>&gt;</span>
                    </button>

                    <div class="hero-dots">
                        @foreach ($banners as $index => $banner)
                            <button type="button"
                                class="hero-dot {{ $index === 0 ? 'is-active' : '' }}"
                                data-index="{{ $index }}"
                                aria-label="Chuyển đến banner {{ $index + 1 }}"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                {{-- Fallback nếu không có banner --}}
                <div class="hero-banner-viewport">
                    <div class="hero-banner-track">
                        <div class="hero-slide">
                            <img class="hero-slide-image" src="https://picsum.photos/1600/700?random=1"
                                alt="Meteor Shop Banner">
                            <div class="hero-slide-overlay"></div>
                            <div class="hero-slide-content container">
                                <h1 class="hero-slide-title">
                                    Chào mừng đến với Meteor Shop
                                </h1>
                                <p class="hero-slide-subtitle">
                                    Khám phá bộ sưu tập nội thất hiện đại, tinh tế cho không gian sống của bạn.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        .hero-banner {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: #111;
        }

        .hero-banner-inner {
            position: relative;
            max-width: 1400px;
            margin: 0 auto;
        }

        .hero-slide {
            position: relative;
            flex: 0 0 100%;
            height: clamp(260px, 55vw, 520px);
        }

        .hero-banner-viewport {
            position: relative;
            overflow: hidden;
        }

        .hero-banner-track {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.6s ease-in-out;
        }

        .hero-slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hero-slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.35) 45%, transparent 100%);
        }

        .hero-slide-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 16px;
            color: #fff;
            padding: clamp(16px, 4vw, 40px);
            text-align: center;
        }

        .hero-slide-title {
            font-size: clamp(22px, 3vw, 36px);
            font-weight: 700;
            margin: 0;
            max-width: 640px;
        }

        .hero-slide-subtitle {
            margin: 0;
            max-width: 640px;
            font-size: 14px;
            line-height: 1.6;
            color: #e2e2e2;
        }

        .hero-slide-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            padding: 10px 24px;
            border-radius: 999px;
            background: #f97316;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.35);
            transition: all 0.25s ease;
        }

        .hero-slide-cta:hover {
            background: #ea580c;
            box-shadow: 0 12px 30px rgba(234, 88, 12, 0.45);
            transform: translateY(-1px);
        }

        .hero-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease, background 0.2s ease;
            z-index: 5;
        }

        .hero-nav span {
            font-size: 20px;
            line-height: 1;
        }

        .hero-nav.is-visible {
            opacity: 1;
            visibility: visible;
        }

        .hero-nav:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .hero-nav-prev {
            left: 12px;
        }

        .hero-nav-next {
            right: 12px;
        }

        .hero-dots {
            position: absolute;
            left: 50%;
            bottom: 16px;
            transform: translateX(-50%);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            z-index: 5;
        }

        .hero-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            border: none;
            background: rgba(255, 255, 255, 0.4);
            padding: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .hero-dot.is-active {
            width: 22px;
            background: #f97316;
        }

        @media (max-width: 768px) {
            .hero-slide-subtitle {
                max-width: 100%;
            }

            .hero-slide-cta {
                width: 100%;
                max-width: 260px;
                justify-content: center;
            }

            .hero-nav {
                width: 32px;
                height: 32px;
            }

            .hero-nav-prev {
                left: 8px;
            }

            .hero-nav-next {
                right: 8px;
            }
        }
    </style>


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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.querySelector('.hero-banner-track');
            const slides = Array.from(document.querySelectorAll('.hero-slide'));
            const dots = Array.from(document.querySelectorAll('.hero-dot'));
            const prevBtn = document.querySelector('.hero-nav-prev');
            const nextBtn = document.querySelector('.hero-nav-next');
            const slider = document.querySelector('.hero-banner-viewport');

            if (!track || !slides.length) return;

            let current = 0;
            let timer = null;
            const AUTO_TIME = 5000;
            let touchStartX = 0;
            let touchEndX = 0;
            const SWIPE_THRESHOLD = 50; // px
            let navTimer = null;
            const NAV_IDLE_TIME = 2500;

            const heroNavs = [prevBtn, nextBtn].filter(Boolean);

            const showNav = () => {
                heroNavs.forEach(btn => btn.classList.add('is-visible'));
                if (navTimer) {
                    clearTimeout(navTimer);
                }
                navTimer = setTimeout(() => {
                    heroNavs.forEach(btn => btn.classList.remove('is-visible'));
                }, NAV_IDLE_TIME);
            };

            const goTo = (index) => {
                current = (index + slides.length) % slides.length;

                track.style.transform = `translateX(-${current * 100}%)`;

                if (dots[current]) {
                    dots[current].classList.add('is-active');
                }
                dots.forEach((dot, i) => {
                    if (i !== current) {
                        dot.classList.remove('is-active');
                    }
                });
            };

            const next = () => goTo(current + 1);
            const prev = () => goTo(current - 1);

            const startAuto = () => {
                if (timer || slides.length <= 1) return;
                timer = setInterval(next, AUTO_TIME);
            };

            const stopAuto = () => {
                if (!timer) return;
                clearInterval(timer);
                timer = null;
            };

            nextBtn && nextBtn.addEventListener('click', () => {
                stopAuto();
                next();
                startAuto();
                showNav();
            });

            prevBtn && prevBtn.addEventListener('click', () => {
                stopAuto();
                prev();
                startAuto();
                showNav();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    stopAuto();
                    goTo(index);
                    startAuto();
                    showNav();
                });
            });

            if (slider) {
                slider.addEventListener('mouseenter', () => {
                    stopAuto();
                    showNav();
                });
                slider.addEventListener('mouseleave', () => {
                    startAuto();
                });

                slider.addEventListener('mousemove', () => {
                    showNav();
                });

                // Swipe trên mobile
                slider.addEventListener('touchstart', (e) => {
                    if (!e.touches || !e.touches.length) return;
                    stopAuto();
                    showNav();
                    touchStartX = e.touches[0].clientX;
                    touchEndX = touchStartX;
                }, { passive: true });

                slider.addEventListener('touchmove', (e) => {
                    if (!e.touches || !e.touches.length) return;
                    touchEndX = e.touches[0].clientX;
                }, { passive: true });

                slider.addEventListener('touchend', () => {
                    const deltaX = touchEndX - touchStartX;
                    if (Math.abs(deltaX) > SWIPE_THRESHOLD) {
                        if (deltaX < 0) {
                            next();
                        } else {
                            prev();
                        }
                    }
                    startAuto();
                });
            }

            startAuto();
        });
    </script>


@endsection
