<div class="quick-view-container">

    {{-- Product Info Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-box-seam"></i>
            <h6 class="quick-view-card-title">Thông tin sản phẩm</h6>
        </div>
        <div class="product-card">
            @php
                $productImage = null;
                if ($review->product) {
                    // Try main image field first
                    if ($review->product->image) {
                        $productImage = asset('storage/' . $review->product->image);
                    }
                    // Fallback to first product image
                    elseif ($review->product->images && $review->product->images->count() > 0) {
                        $firstImage = $review->product->images->first();
                        $productImage = asset('storage/' . ($firstImage->image_path ?? $firstImage->path ?? $firstImage->image ?? ''));
                    }
                }
            @endphp
            @if($productImage)
                <img src="{{ $productImage }}" 
                     alt="{{ $review->product->name ?? 'Product' }}" 
                     class="product-image"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23f0f0f0\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'30\'%3E%3F%3C/text%3E%3C/svg%3E';">
            @else
                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                </div>
            @endif
            <div class="product-info flex-grow-1">
                <h6>
                    <a href="{{ route('admin.products.show', $review->product_id) }}" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1" style="font-size: 0.8rem;"></i>
                        {{ $review->product->name ?? 'N/A' }}
                    </a>
                </h6>
                <small>
                    <i class="bi bi-hash"></i> ID: <strong>{{ $review->product_id }}</strong>
                </small>
            </div>
        </div>
    </div>

    {{-- User Info Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-person-circle"></i>
            <h6 class="quick-view-card-title">Thông tin người dùng</h6>
        </div>
        <div class="user-card">
            <div class="user-avatar">
                {{ substr($review->user->name ?? 'U', 0, 1) }}
            </div>
            <div class="user-info flex-grow-1">
                <h6>
                    <i class="bi bi-person me-1"></i>
                    {{ $review->user->name ?? 'N/A' }}
                </h6>
                @if($review->user->email)
                    <small>
                        <i class="bi bi-envelope me-1"></i>
                        {{ $review->user->email }}
                    </small>
                @endif
                @if($review->user->phone)
                    <small>
                        <i class="bi bi-telephone me-1"></i>
                        {{ $review->user->phone }}
                    </small>
                @endif
            </div>
        </div>
    </div>

    {{-- Rating Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-star-fill"></i>
            <h6 class="quick-view-card-title">Đánh giá</h6>
        </div>
        <div class="rating-display">
            <div class="rating-stars">
                @for($i = 1; $i <= 5; $i++)
                    {{ $i <= $review->rating ? '★' : '☆' }}
                @endfor
            </div>
            <div>
                <span class="rating-badge">
                    <i class="bi bi-star-fill me-1"></i>
                    {{ $review->rating }}/5 sao
                </span>
            </div>
        </div>
    </div>

    {{-- Helpful Count Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-hand-thumbs-up"></i>
            <h6 class="quick-view-card-title">Đánh giá hữu ích</h6>
        </div>
        <div class="helpful-display">
            <span class="badge bg-primary fs-6">
                <i class="bi bi-hand-thumbs-up-fill"></i>
                {{ $review->helpful_votes_count ?? 0 }} lượt hữu ích
            </span>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-chat-text"></i>
            <h6 class="quick-view-card-title">Nội dung bình luận</h6>
        </div>
        <div class="content-box">
            <p>
                @if($review->content ?? $review->comment)
                    {{ $review->content ?? $review->comment }}
                @else
                    <span class="text-muted fst-italic">Không có nội dung</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Images Gallery Card --}}
    @if($review->images && count($review->images) > 0)
        <div class="quick-view-card">
            <div class="quick-view-card-header">
                <i class="bi bi-images"></i>
                <h6 class="quick-view-card-title">Hình ảnh đính kèm ({{ count($review->images) }})</h6>
            </div>
            <div class="images-gallery">
                @foreach($review->images as $index => $img)
                    <div class="image-item" onclick="openImageLightbox({{ $review->id }}, {{ $index }})">
                        <img src="{{ asset('storage/' . $img) }}" alt="Review image">
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Status & Info Card --}}
    <div class="quick-view-card">
        <div class="quick-view-card-header">
            <i class="bi bi-info-circle"></i>
            <h6 class="quick-view-card-title">Trạng thái & Thông tin</h6>
        </div>
        <div class="info-badges">
            @php
                $statusBadges = [
                    'pending' => ['class' => 'warning', 'text' => 'Chờ duyệt', 'icon' => 'clock'],
                    'approved' => ['class' => 'success', 'text' => 'Đã duyệt', 'icon' => 'check-circle'],
                    'rejected' => ['class' => 'danger', 'text' => 'Từ chối', 'icon' => 'x-circle'],
                    'hidden' => ['class' => 'secondary', 'text' => 'Ẩn', 'icon' => 'eye-slash'],
                ];
                $status = $statusBadges[$review->status ?? 'pending'] ?? $statusBadges['pending'];
            @endphp
            <span class="badge bg-{{ $status['class'] }} info-badge">
                <i class="bi bi-{{ $status['icon'] }}"></i>
                {{ $status['text'] }}
            </span>
            @if($review->is_verified_purchase)
                <span class="badge bg-success info-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    Đã mua hàng
                </span>
            @endif
            @if($review->images && count($review->images) > 0)
                <span class="badge bg-info info-badge">
                    <i class="bi bi-image-fill"></i>
                    Có {{ count($review->images) }} hình ảnh
                </span>
            @endif
            @if($review->reported_count > 0)
                <span class="badge bg-danger info-badge">
                    <i class="bi bi-flag-fill"></i>
                    {{ $review->reported_count }} báo cáo
                </span>
            @endif
        </div>
        <div class="date-info">
            <i class="bi bi-calendar3"></i>
            <small>
                <strong>Ngày tạo:</strong> {{ $review->created_at->format('d/m/Y H:i:s') }}
            </small>
        </div>
    </div>
</div>
