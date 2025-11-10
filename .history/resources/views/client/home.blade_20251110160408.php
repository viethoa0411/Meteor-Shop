@extends('client.layouts.app')

@section('title', 'Trang chủ')
@section('content')

{{-- Slide --}}
<div class="slide-wrapper">
    <div class="slide active">
        <img class="imageSlide" src="https://picsum.photos/1200/800?random=1">
        <h2 style="color: #fff">Siêu ưu đãi</h2>
        <button onclick="alert('Bạn đang ở slide 1')">Mua ngay</button>
    </div>
    <div class="slide">
        <img class="imageSlide" src="https://picsum.photos/1200/800?random=2">
        <h2 style="color: #fff">Phong cách Minimalism </h2>
        <button onclick="alert('Bạn đang ở slide 2')">Mua ngay</button>
    </div>
    <div class="slide">
        <img class="imageSlide" src="https://picsum.photos/1200/800?random=3">
        <h2 style="color: #fff">Phong cách nội thất Indochine </h2>
        <button onclick="alert('Bạn đang ở slide 3')">Mua ngay</button>
    </div>
</div>

{{-- Sản phẩm mới --}}
<div class="product" style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
    <h2 style="padding-left: 20px; font-size:20px; font-weight:600; margin-bottom:8px; display:flex; align-items: center; gap:8px">
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
                {{-- ĐÃ SỬA: Dùng ID thay vì slug --}}
                <a href="{{ route('client.product.detail', ['slug' => $p->slug]) }}" class="product-card">
                    <div class="product-img">
                        <img src="{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
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
                    onmouseover="this.style.transform='scale(1.2)'"
                    onmouseout="this.style.transform='scale(1)'">
            </div>
            <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Sofa</h4>
        </div>
        <div>
            <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                <img src="https://picsum.photos/800/1200?random=21" alt=""
                    style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                    onmouseover="this.style.transform='scale(1.2)'" 
                    onmouseout="this.style.transform='scale(1)'">
            </div>
            <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Giường</h4>
        </div>
            <div>
            <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                <img src="https://picsum.photos/800/1200?random=31" alt=""
                    style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                    onmouseover="this.style.transform='scale(1.2)'"
                    onmouseout="this.style.transform='scale(1)'">
            </div>
            <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Bàn làm việc</h4>
        </div>
    </div>
</div>
{{-- end  --}}

{{--  Sản phẩm bán chạy --}}
<div class="product" style="padding-bottom: 50px; padding-left:20px; padding-right:20px">
    <h2 style="padding-left: 20px; font-size:20px; font-weight:600; margin-bottom:8px; display:flex; align-items: center; gap:8px">
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
                {{-- ĐÃ SỬA: Dùng ID thay vì slug --}}
                <a href="{{ route('client.product.detail', ['slug' => $p->slug]) }}" class="product-card">
                    <div class="product-img">
                        <img src="{{ $o->image ? asset('storage/'.$o->image) : 'https://via.placeholder.com/400x400?text=No+Image' }}" 
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
    <div style="display: flex; width:100%; height:130vh; gap:24px;padding:100px 10px 100px 0; box-sizing:border-box; background-color: rgb(1,49,49)"> 
        <div style="width: 50%; height:100%;overflow:hidden;">
            <img src="https://picsum.photos/1000/800?random=1" style="width:100%; height: 100%; object-fit:cover;transition:1s;" onmouseover="this.style.transform='scale(1.3)'" onmouseout="this.style.transform='scale(1)'">
        </div>

        <div style="width: 50%; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:20px; text-align:center;">
            <div style="width: 70%; height:40vh;overflow:hidden; border-radius:10px">
                <img src="https://picsum.photos/600/400?random=2"  style="width:100%; height: 100%; object-fit:cover">
            </div>
            <div>
                <h2 style="font-size:24px; font-weight:600; margin:12px 0; color:#fff">Phong cách hiện đại</h2>
                <p style="color: #cecece; fonr-size:14px; line-height:1.6; margin-bottom:20px; margin:50px">
                      Khám phá bộ sưu tập nội thất mới nhất mang đậm hơi thở đương đại.
                        Ra đời vào năm 2024, là một trong những thương hiệu tiên phong 
                        trong ngành nội thất, với nguồn cảm hứng văn hóa Việt và gu thẩm mỹ
                        tinh tế. Qua 26 năm hoạt động, Nhà Xinh luôn chú trọng đổi mới để 
                        duy trì vị thế là thương hiệu nội thất hàng đầu tại Việt Nam.
                </p>
                <button style="background: #393939; color:#fff; border:none; padding:10px 24px; border-radius: 6px; cursor: pointer; transition:.3s " onmouseover="this.style.background=#444" onmouseout="this.style.background='#222'">Xem ngay</button>
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
                        onmouseover="this.style.transform='scale(1.2)'"
                        onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng khách</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=121" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'"
                        onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng ngủ</h4>
            </div>
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=131" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'"
                        onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng ăn</h4>
            </div>  
            <div>
                <div style="overflow: hidden; border:1px solid #ccc; border-radius:6px;">
                    <img src="https://picsum.photos/800/1200?random=41" alt=""
                        style="width: 100%; aspect-ratio:2/3;display:block;transition:.8s;cursor: pointer;"
                        onmouseover="this.style.transform='scale(1.2)'"
                        onmouseout="this.style.transform='scale(1)'">
                </div>
                <h4 style="font-size:20px; color:#313131; text-align:center; padding:10px 0;">Phòng làm việc</h4>
            </div>
        </div>
    </div>
{{-- end  --}}


{{-- goc chia sẻ --}}

{{-- <div class="product" style="padding-bottom: 50px; padding-left:20px; padding-right:20px"> --}}
<div style="text-align:center; padding: 0; background:#f9f9f9;">
    <h2 style="font-size:28px; font-weight:700; margin-bottom:30px;">Góc Cảm Hứng</h2>

    <div style="max-width:1300px; margin:0 auto; padding:0 20px;">
        <div style="display:flex; justify-content:center; gap:30px; flex-wrap:wrap;">

            <div class="article-card" 
                style="width:510px; background:#fff; border-radius:10px; overflow:hidden; text-align:left;
                box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:.3s; cursor:pointer;">
                <img src="https://picsum.photos/800/600?random=12" style="width:100%; height:300px; object-fit:cover;">
                <h3 style="font-size:20px; font-weight:600; margin:16px;">Thiết kế hiện đại cho không gian nhỏ</h3>
                <p style="font-size:14px; color:#555; line-height:1.6; margin:0 16px 20px;">
                    Những ý tưởng thông minh giúp tận dụng tối đa diện tích, mang lại sự tiện nghi và phong cách hiện đại.
                </p>
            </div>

            <div class="article-card"
                style="width:510px; background:#fff; border-radius:10px; overflow:hidden; text-align:left;
                box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:.3s; cursor:pointer;">
                <img src="https://picsum.photos/800/600?random=32" style="width:100%; height:300px; object-fit:cover;">
                <h3 style="font-size:20px; font-weight:600; margin:16px;">Thiết kế hiện đại cho không gian nhỏ</h3>
                <p style="font-size:14px; color:#555; line-height:1.6; margin:0 16px 20px;">
                    Cây xanh và ánh sáng tự nhiên đang trở thành xu hướng trong thiết kế nội thất hiện đại.
                </p>
            </div>

        </div>
    </div>
</div>

{{--  --}}



{{-- bai content 2 --}}
     <div style="display:flex;width:100%;height:60vh;gap:24px;padding:0;           
                    box-sizing:border-box;background-color: rgb(4, 52, 110)">
        <div style="width:50%;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:20px;text-align:center;">
            <h2 style="font-size:24px;font-weight:600;margin:12px 0; color:#fff">Phong cách hiện đại</h2>
            <p style="color:#cecece;font-size:14px;line-height:1.6
                        ;margin-bottom:20px;margin:50px">
                Khám phá bộ sưu tập nội thất mới nhất mang đậm hơi thở đương đại.
                Ra đời vào năm 2024, là một trong những thương hiệu tiên phong 
                trong ngành nội thất, với nguồn cảm hứng văn hóa Việt và gu thẩm mỹ
                tinh tế. Qua 26 năm hoạt động, Nhà Xinh luôn chú trọng đổi mới để 
                duy trì vị thế là thương hiệu nội thất hàng đầu tại Việt Nam.
            </p>
            <button style="background:#393939;color:#fff;border:none;
                            padding:10px 24px; border-radius:6px;cursor:pointer;transition:.3s;" onmouseover="this.style.background='#444'" onmouseout="this.style.background='#222'">Xem ngay</button>
        </div>
        <div style="width:50%;height:100%;overflow:hidden;;">
            <img src="https://picsum.photos/1000/800?random=9" style="width:100%;height:100%;object-fit:cover;transition:1s;" onmouseover="this.style.transform='scale(1.3)'" onmouseout="this.style.transform='scale(1)'">
        </div>
    </div>
                
{{--  --}}

    <script>
        let i=0, s=document.querySelectorAll('.slide');
        setInterval(() => {
            s[i].classList.remove('active');
            i=(i+1)%s.length;
            s[i].classList.add('active');
        }, 2);
    </script>

@endsection