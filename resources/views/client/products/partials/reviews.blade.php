{{-- REVIEW SECTION - CLIENT SIDE --}}
<div class="reviews-section mt-5" id="reviewsSection">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">Đánh giá sản phẩm</h3>
        @auth
            @php
                $userHasPurchased = auth()->user()->orders()
                    ->whereHas('items', function($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })
                    ->where('order_status', 'completed')
                    ->exists();
            @endphp
            @if($userHasPurchased)
                <button type="button" class="btn btn-orange-theme shadow-sm" id="btnWriteReview" style="border-radius: 12px; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.3s ease;">
                    <i class="bi bi-plus-circle me-1"></i>
                    Viết đánh giá của bạn
                </button>
            @endif
        @endauth
    </div>

    {{-- 1. Summary Rating --}}
    <div class="card mb-4 border-0 shadow-lg" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 20px; overflow: hidden;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="rating-summary">
                        <div class="rating-average">
                            <span class="rating-number">{{ number_format($ratingAvg, 1) }}</span>
                            <span class="rating-max">/5</span>
                        </div>
                        <div class="rating-stars-large mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($ratingAvg))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @elseif($i - 0.5 <= $ratingAvg)
                                    <i class="bi bi-star-half text-warning"></i>
                                @else
                                    <i class="bi bi-star text-muted"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted mb-0">
                            <strong class="text-dark">{{ $totalReviews }}</strong> đánh giá
                        </p>
                    </div>
                </div>
                <div class="col-md-8">
                    @php
                        // $ratingBreakdown được tính sẵn ở controller để tránh lặp lại truy vấn
                        $ratingCounts = $ratingBreakdown ?? [];
                    @endphp
                    @foreach($ratingCounts as $rating => $count)
                        @php
                            $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="rating-bar-item mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <span class="rating-label">{{ $rating }} sao</span>
                                <div class="rating-bar-wrapper flex-grow-1 mx-3">
                                    <div class="rating-bar-bg">
                                        <div class="rating-bar-fill" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                                <span class="rating-count text-muted">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Review Filters (Pill Buttons) --}}
    <div class="review-filters mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-pill review-filter active" data-rating="all" data-filter="">
                <i class="bi bi-list-ul me-1"></i>
                Tất cả
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="5" data-filter="">
                <i class="bi bi-star-fill me-1"></i>
                5 sao
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="4" data-filter="">
                <i class="bi bi-star-fill me-1"></i>
                4 sao
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="3" data-filter="">
                <i class="bi bi-star-fill me-1"></i>
                3 sao
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="2" data-filter="">
                <i class="bi bi-star-fill me-1"></i>
                2 sao
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="1" data-filter="">
                <i class="bi bi-star-fill me-1"></i>
                1 sao
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="all" data-filter="with-images">
                <i class="bi bi-image me-1"></i>
                Có hình ảnh
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="all" data-filter="verified">
                <i class="bi bi-check-circle me-1"></i>
                Đã mua hàng
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="all" data-filter="" data-sort="newest">
                <i class="bi bi-clock me-1"></i>
                Mới nhất
            </button>
            <button type="button" class="btn btn-pill review-filter" data-rating="all" data-filter="" data-sort="helpful">
                <i class="bi bi-hand-thumbs-up me-1"></i>
                Hữu ích nhất
            </button>
            </div>
        </div>
    </div>

    {{-- 3. Reviews List (AJAX Loaded) --}}
    <div id="reviews-list" class="reviews-list">
        <div class="reviews-loading">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <p class="loading-text">Đang tải đánh giá...</p>
        </div>
    </div>

    {{-- 4. Pagination --}}
    <div id="reviews-pagination" class="mt-4 d-none"></div>

    {{-- 5. Write Review Form Modal --}}
    @auth
        @php
            $userHasPurchased = auth()->user()->orders()
                ->whereHas('items', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->where('order_status', 'completed')
                ->exists();
        @endphp
        @if($userHasPurchased)
            <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-hidden="true" aria-labelledby="writeReviewModalLabel">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content review-form-modal">
                        <div class="modal-header bg-gradient-orange text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-star-fill text-white me-2"></i>
                                Viết đánh giá của bạn
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="review-form" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">
                                        <i class="bi bi-star-fill text-orange-theme me-2"></i>
                                        Đánh giá của bạn <span class="text-danger">*</span>
                                    </label>
                                    <div class="rating-input-wrapper">
                                        <div class="rating-input" role="radiogroup" aria-label="Đánh giá sao">
                                        @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required
                                                       aria-label="{{ $i }} sao">
                                                <label for="rating{{ $i }}" class="rating-star" data-rating="{{ $i }}" tabindex="0"
                                                       onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('rating{{ $i }}').click();}">
                                                <i class="bi bi-star-fill"></i>
                                            </label>
                                        @endfor
                                    </div>
                                        <div class="rating-text text-center mt-2">
                                            <small id="ratingText" class="text-muted">Chọn số sao từ 1 đến 5</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="bi bi-chat-left-text me-2 text-warning"></i>
                                        Nhận xét <span class="text-danger">*</span>
                                    </label>
                                    <div class="textarea-wrapper">
                                        <textarea name="comment" id="reviewComment" class="form-control review-textarea" rows="5"
                                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                                                  required minlength="20" maxlength="1000"
                                                  aria-label="Nhận xét đánh giá"
                                                  aria-describedby="charCountHelp"></textarea>
                                        <div class="char-count-wrapper mt-2">
                                            <div class="char-count-progress">
                                                <div class="char-count-bar" id="charCountBar"></div>
                                            </div>
                                            <div class="char-count-text">
                                                <small>
                                                    <span id="charCount">0</span>/1000 ký tự
                                                    <span class="text-danger ms-2" id="minCharWarning" style="display: none;">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>Tối thiểu 20 ký tự
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                        <small id="charCountHelp" class="text-muted d-block mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Vui lòng viết tối thiểu 20 ký tự để đánh giá có ý nghĩa
                                        </small>
                                    </div>
                                </div>

                                @php
                                    $allowImages = config('reviews.allow_images', true);
                                    $maxImages = config('reviews.max_images', 5);
                                @endphp
                                @if($allowImages)
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold mb-3 d-flex align-items-center">
                                            <i class="bi bi-images me-2 text-warning"></i>
                                            Hình ảnh
                                            <span class="badge bg-dark ms-2">{{ $maxImages }} ảnh tối đa</span>
                                        </label>
                                        <div class="upload-area" id="uploadArea">
                                            <input type="file" name="images[]" id="reviewImages"
                                                   class="d-none" multiple accept="image/jpeg,image/jpg,image/png,image/webp"
                                                   data-max="{{ $maxImages }}">
                                            <div class="upload-content">
                                                <div class="upload-icon-wrapper">
                                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                                    <div class="upload-icon-bg"></div>
                                            </div>
                                                <div class="upload-text-content">
                                                    <h6 class="upload-title">Kéo thả ảnh vào đây</h6>
                                                    <p class="upload-subtitle">
                                                        hoặc
                                                        <span class="upload-link" onclick="document.getElementById('reviewImages').click()">
                                                            <i class="bi bi-folder2-open me-1"></i>Chọn ảnh từ máy tính
                                                        </span>
                                                    </p>
                                                    <div class="upload-hints">
                                                        <span class="upload-hint-item">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            JPG, PNG, WEBP
                                                        </span>
                                                        <span class="upload-hint-item">
                                                            <i class="bi bi-file-earmark-image me-1"></i>
                                                            Tối đa 2MB/ảnh
                                                        </span>
                                                        <span class="upload-hint-item">
                                                            <i class="bi bi-collection me-1"></i>
                                                            Tối đa {{ $maxImages }} ảnh
                                                        </span>
                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="imagePreview" class="mt-4 d-none">
                                            <div class="preview-header d-flex justify-content-between align-items-center mb-3">
                                                <div class="preview-title">
                                                    <i class="bi bi-images me-2"></i>
                                                    <span>Ảnh đã chọn</span>
                                                    <span class="badge bg-secondary ms-2" id="imageCount">0</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllPreviews()">
                                                    <i class="bi bi-trash me-1"></i>
                                                    Xóa tất cả
                                                </button>
                                            </div>
                                            <div class="preview-grid" id="previewContainer"></div>
                                            <div id="uploadProgress" class="mt-3" style="display: none;">
                                                <div class="progress progress-modern">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                         role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted d-block mt-2 text-center">
                                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                                    Đang tải lên...
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer review-form-footer">
                                <div class="d-flex flex-wrap gap-2 w-100 justify-content-end">
                                    <button type="button" class="btn btn-light btn-action" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1"></i>
                                        Hủy
                                    </button>
                                    <button type="submit" class="btn btn-orange-theme btn-submit">
                                    <i class="bi bi-send me-1"></i>
                                    Gửi đánh giá
                                </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>

{{-- Lightbox for review images (tách biệt với lightbox ảnh sản phẩm) --}}
<div class="modal fade" id="reviewImageLightbox" tabindex="-1" aria-hidden="true" style="background: rgba(10, 10, 10, 0.92); backdrop-filter: blur(5px);">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 position-relative">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 lightbox-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="lightbox-container position-relative">
                <button type="button" class="lightbox-nav lightbox-prev" id="lightboxPrev" onclick="lightboxPrev()" style="display: none;">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="lightbox-image-wrapper text-center">
                    <img id="lightboxImage" src="" alt="Review image" class="img-fluid rounded shadow-lg" style="max-height: 85vh; object-fit: contain;">
                </div>

                <button type="button" class="lightbox-nav lightbox-next" id="lightboxNext" onclick="lightboxNext()" style="display: none;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="lightbox-counter position-absolute bottom-0 start-50 translate-middle-x mb-4 lightbox-counter-badge" id="lightboxCounter" style="display: none;"></div>
        </div>
    </div>
</div>

@push('head')
<style>
    /* Custom Orange Theme */
    .bg-gradient-orange {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%) !important;
    }

    .text-orange-theme {
        color: #f97316 !important;
    }

    .btn-orange-theme {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3) !important;
    }

    .btn-orange-theme:hover {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.4) !important;
    }

    /* ========= REVIEWS SECTION ========= */
    .reviews-section {
        padding: 3rem 0;
        background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    }

    .reviews-section h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1a1a;
        letter-spacing: -0.5px;
        margin-bottom: 0;
    }

    /* Rating Summary Card */
    .rating-summary {
        padding: 0;
    }

    .rating-average {
        font-size: 3.5rem;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .rating-number {
        color: #212529;
        font-weight: 900;
    }

    .rating-max {
        font-size: 1.75rem;
        color: #8e9aaf;
        font-weight: 500;
    }

    .rating-stars-large {
        font-size: 1.75rem;
        letter-spacing: 4px;
        margin: 1rem 0;
    }

    .rating-stars-large i {
        filter: drop-shadow(0 2px 4px rgba(249, 115, 22, 0.3));
    }

    /* Rating Bar */
    .rating-bar-item {
        position: relative;
        margin-bottom: 1rem;
    }

    .rating-label {
        min-width: 70px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .rating-label::before {
        content: '★';
        color: #f97316;
        font-size: 0.85rem;
    }

    .rating-bar-wrapper {
        position: relative;
        flex: 1;
        margin: 0 1rem;
    }

    .rating-bar-bg {
        height: 28px;
        background: #f1f3f5;
        border-radius: 14px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .rating-bar-fill {
        height: 100%;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        border-radius: 14px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .rating-bar-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .rating-count {
        min-width: 45px;
        text-align: right;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
    }

    /* Pill Buttons */
    .review-filters {
        background: white;
        padding: 1.25rem;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
    }

    .btn-pill {
        border-radius: 50px;
        padding: 0.625rem 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px solid #e2e8f0;
        background: white;
        color: #475569;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .btn-pill::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(33, 37, 41, 0.1);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-pill:hover {
        border-color: #212529;
        color: #212529;
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(33, 37, 41, 0.15);
    }

    .btn-pill:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-pill.active {
        background: #212529;
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 16px rgba(33, 37, 41, 0.3);
    }

    .btn-pill.active::before {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Reviews List Container */
    .reviews-list {
        min-height: 200px;
        position: relative;
    }

    /* Loading State */
    .reviews-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        min-height: 300px;
    }

    .loading-spinner {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .spinner-ring {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #212529;
        animation: spinner-bounce 1.4s ease-in-out infinite both;
    }

    .spinner-ring:nth-child(1) {
        animation-delay: -0.32s;
    }

    .spinner-ring:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes spinner-bounce {
        0%, 80%, 100% {
            transform: scale(0);
            opacity: 0.5;
        }
        40% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .loading-text {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 500;
        margin: 0;
        letter-spacing: 0.3px;
    }

    /* Review Item */
    .review-item {
        background: white;
        border: 1px solid #e8ecf1;
        border-radius: 16px;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .review-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: #212529;
        transform: scaleY(0);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0 3px 3px 0;
    }

    .review-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
        border-color: #cbd5e1;
    }

    .review-item:hover::before {
        transform: scaleY(1);
    }

    .review-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #212529;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(33, 37, 41, 0.25);
        border: 2px solid #f8f9fa;
        transition: all 0.3s ease;
        position: relative;
    }

    .review-avatar::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        padding: 2px;
        background: #212529;
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .review-item:hover .review-avatar {
        transform: scale(1.03);
        box-shadow: 0 4px 16px rgba(33, 37, 41, 0.35);
    }

    .review-item:hover .review-avatar::after {
        opacity: 1;
    }

    .review-user-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 1rem;
        letter-spacing: -0.2px;
        line-height: 1.5;
        transition: color 0.2s ease;
    }

    .review-user-name:hover {
        color: #212529;
    }


    .review-verified-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.5rem 0.875rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.3s ease;
        border: none;
    }

    .review-verified-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .review-verified-badge i {
        font-size: 0.875rem;
    }

    .review-verified-badge::before {
        content: '✓';
        font-weight: 800;
    }

    .review-rating {
        color: #fbbf24; /* Amber-400 */
        font-size: 1.1rem;
        letter-spacing: 2px;
        margin: 0.5rem 0;
    }

    /* Synchronize star colors globally in reviews */
    .text-warning {
        color: #fbbf24 !important;
    }

    .rating-stars-large .bi-star-fill,
    .rating-stars-large .bi-star-half {
        color: #fbbf24 !important;
    }

    .review-filter i {
        color: #fbbf24;
    }

    .review-filter.active {
        background-color: #fbbf24;
        border-color: #fbbf24;
        color: white;
    }

    .review-filter.active i {
        color: white;
    }

    .review-content {
        color: #475569;
        line-height: 1.75;
        margin: 1rem 0;
        font-size: 0.9375rem;
        word-wrap: break-word;
        white-space: pre-wrap;
        overflow: visible;
        max-width: 100%;
    }

    .review-content .review-images-grid {
        margin-top: 0.75rem;
        display: flex !important;
        flex-wrap: nowrap !important;
        flex-direction: row !important;
        gap: 0.5rem;
        align-items: flex-start;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
    }

    .review-images-grid {
        display: flex !important;
        flex-wrap: nowrap !important;
        flex-direction: row !important;
        gap: 0.5rem;
        margin-top: 0.75rem;
        align-items: flex-start;
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        max-width: 100%;
        white-space: nowrap;
    }

    /* Scrollbar styling cho review-images-grid */
    .review-images-grid::-webkit-scrollbar {
        height: 6px;
    }

    .review-images-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .review-images-grid::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .review-images-grid::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .review-image-item {
        position: relative;
        border-radius: 0;
        overflow: visible;
        cursor: pointer;
        width: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
        height: 120px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        border: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: none;
        background: transparent;
        display: inline-block;
    }

    .review-image-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(33, 37, 41, 0);
        transition: all 0.3s ease;
        z-index: 1;
        border-radius: 6px;
        pointer-events: none;
    }

    .review-image-item:hover {
        transform: translateY(-3px);
        z-index: 10;
        cursor: zoom-in;
    }

    .review-image-item:hover .image-wrapper {
        border-color: #212529;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .review-image-item:hover::before {
        background: rgba(33, 37, 41, 0.08);
    }

    .review-image-item:hover img {
        transform: scale(1);
    }

    .review-image-item .image-wrapper {
        position: relative;
        width: 120px !important;
        height: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
        min-height: 120px !important;
        max-height: 120px !important;
        overflow: hidden !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        box-sizing: border-box;
        vertical-align: middle;
    }

    .review-image-item img {
        width: 120px !important;
        height: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
        min-height: 120px !important;
        max-height: 120px !important;
        object-fit: contain !important;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: block !important;
        border-radius: 0;
        flex-shrink: 0;
    }

    /* Force tất cả ảnh trong review-images-grid */
    .review-images-grid .review-image-item {
        display: inline-block !important;
        float: none !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
    }

    .review-images-grid .review-image-item .image-wrapper,
    .review-images-grid .review-image-item .image-wrapper img {
        width: 120px !important;
        height: 120px !important;
        max-width: 120px !important;
        max-height: 120px !important;
    }

    .review-image-item img.image-error {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        color: #94a3b8;
        padding: 0.5rem;
        text-align: center;
    }


    .review-date {
        color: #64748b;
        font-size: 0.8125rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        white-space: nowrap;
    }

    .review-date::before {
        content: '🕒';
        font-size: 0.75rem;
        opacity: 0.7;
    }

    /* Admin Reply */
    .admin-reply {
        background: #f8f9fa;
        border-left: 4px solid #212529;
        border-radius: 12px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        box-shadow: 0 2px 8px rgba(33, 37, 41, 0.1);
        position: relative;
    }

    .admin-reply::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #212529;
        border-radius: 12px 12px 0 0;
    }

    .admin-reply-header {
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .admin-reply-header::before {
        content: '👨‍💼';
        font-size: 1.1rem;
    }

    .admin-reply-content {
        color: #4a5568;
        margin: 0;
        line-height: 1.75;
        font-size: 0.9rem;
    }

    /* Rating Input - Clean & Professional Design */
    .rating-input-wrapper {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
    }

    .rating-input-wrapper:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .rating-input-wrapper.is-invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .rating-input {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        position: relative;
        padding: 5px;
    }

    /* Hide radio buttons absolutely */
    .rating-input input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
        appearance: none;
        -webkit-appearance: none;
    }

    .rating-input label {
        font-size: 3rem;
        color: #e2e8f0;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: inline-block;
        padding: 0 2px;
    }

    /* Hover and Checked States */
    .rating-input label:hover,
    .rating-input label:hover ~ label,
    .rating-input input[type="radio"]:checked ~ label {
        color: #fbbf24; /* Amber-400 */
    }

    /* Hover Animation - Subtle */
    .rating-input label:hover {
        transform: scale(1.1);
    }

    .rating-input label:hover ~ label {
        transform: scale(1.05);
    }

    /* Checked Animation - Subtle Pop */
    .rating-input input[type="radio"]:checked + label {
        transform: scale(1.15);
        animation: starPopSubtle 0.3s ease-out;
    }

    @keyframes starPopSubtle {
        0% { transform: scale(1); }
        50% { transform: scale(1.25); }
        100% { transform: scale(1.15); }
    }

    .rating-text {
        min-height: 28px;
        margin-top: 0.75rem;
    }

    #ratingText {
        font-size: 1rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
        display: inline-block;
        padding: 0.25rem 1rem;
    }

    #ratingText.highlight {
        color: #ea580c; /* Orange-600 */
        transform: scale(1.02);
    }

    /* Review Form Modal */
    .review-form-modal {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .review-form-modal .modal-header {
        background: #212529;
        border: none;
        padding: 1.5rem 2rem;
    }

    .review-form-modal .modal-body {
        padding: 2rem;
    }

    .review-form-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        backdrop-filter: blur(10px);
    }

    /* Textarea Wrapper */
    .textarea-wrapper {
        position: relative;
    }

    .review-textarea {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        resize: vertical;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .review-textarea:focus {
        border-color: #212529;
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.15);
        outline: none;
    }

    .review-textarea.is-valid {
        border-color: #28a745;
    }

    .review-textarea.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }

    .char-count-wrapper {
        margin-top: 0.75rem;
    }

    .char-count-progress {
        height: 4px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .char-count-bar {
        height: 100%;
        background: #212529;
        border-radius: 10px;
        transition: width 0.3s ease, background 0.3s ease;
        width: 0%;
    }

    .char-count-text {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #6c757d;
        font-weight: 500;
    }

    /* Upload Area - Professional Design */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .upload-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(33, 37, 41, 0.08), transparent);
        transition: left 0.6s ease;
    }

    .upload-area:hover {
        border-color: #212529;
        background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(33, 37, 41, 0.15);
    }

    .upload-area:hover::before {
        left: 100%;
    }

    .upload-area.dragover {
        border-color: #212529;
        background: linear-gradient(135deg, #e7f3ff 0%, #f0f7ff 100%);
        transform: scale(1.02);
        box-shadow: 0 15px 40px rgba(33, 37, 41, 0.25);
        border-style: solid;
    }

    .upload-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .upload-icon-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .upload-icon-bg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        background: #212529;
        border-radius: 50%;
        opacity: 0.1;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0.1;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
            opacity: 0.15;
        }
    }

    .upload-icon {
        font-size: 3.5rem;
        color: #212529;
        position: relative;
        z-index: 2;
        animation: float 3s ease-in-out infinite;
        filter: drop-shadow(0 4px 8px rgba(33, 37, 41, 0.2));
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .upload-text-content {
        max-width: 400px;
        margin: 0 auto;
    }

    .upload-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .upload-subtitle {
        font-size: 0.95rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .upload-link {
        color: #212529;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    .upload-link:hover {
        color: #212529;
        background: rgba(33, 37, 41, 0.1);
        transform: translateY(-1px);
    }

    .upload-hints {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .upload-hint-item {
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        color: #6b7280;
        padding: 0.375rem 0.75rem;
        background: rgba(33, 37, 41, 0.05);
        border-radius: 20px;
        border: 1px solid rgba(33, 37, 41, 0.1);
    }

    .upload-hint-item i {
        color: #212529;
    }

    /* Image Preview - Professional Design */
    .preview-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .preview-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .preview-title i {
        color: #212529;
    }

    .preview-grid {
        display: flex !important;
        flex-wrap: nowrap !important;
        flex-direction: row !important;
        gap: 0.75rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        min-height: 70px;
        overflow-x: auto;
        overflow-y: hidden;
        align-items: flex-start;
    }

    .preview-grid::-webkit-scrollbar {
        height: 6px;
    }

    .preview-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .preview-grid::-webkit-scrollbar-thumb {
        background: #212529;
        border-radius: 10px;
    }

    .preview-grid::-webkit-scrollbar-thumb:hover {
        background: #212529;
    }

    .preview-item,
    #previewContainer .preview-item,
    .preview-grid .preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 1;
        border: 2px solid #e9ecef;
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        max-width: 50px !important;
        max-height: 50px !important;
        margin: 0 !important;
        padding: 0 !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background: #fff;
        box-sizing: border-box !important;
    }

    .preview-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(33, 37, 41, 0.1) 0%, rgba(33, 37, 41, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .preview-item:hover {
        transform: scale(1.15) translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 37, 41, 0.3);
        border-color: #212529;
        z-index: 10;
    }

    .preview-item:hover::before {
        opacity: 1;
    }

    .preview-item img,
    #previewContainer .preview-item img,
    .preview-grid .preview-item img {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        max-width: 50px !important;
        max-height: 50px !important;
        object-fit: cover !important;
        display: block !important;
        position: relative;
        z-index: 0;
        box-sizing: border-box !important;
    }

    #previewContainer {
        display: flex !important;
        flex-wrap: nowrap !important;
        flex-direction: row !important;
        gap: 0.75rem;
        overflow-x: auto;
        overflow-y: hidden;
        align-items: flex-start;
    }

    #previewContainer > div {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        max-width: 50px !important;
        max-height: 50px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
    }

    #previewContainer::-webkit-scrollbar {
        height: 6px;
    }

    #previewContainer::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #previewContainer::-webkit-scrollbar-thumb {
        background: #212529;
        border-radius: 10px;
    }

    #previewContainer::-webkit-scrollbar-thumb:hover {
        background: #343a40;
    }

    .preview-remove {
        position: absolute;
        top: -10px;
        right: -10px;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        z-index: 20;
        opacity: 0;
        transform: scale(0.8);
    }

    .preview-item:hover .preview-remove {
        opacity: 1;
        transform: scale(1);
    }

    .preview-remove:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.6);
    }

    .progress-modern {
        height: 10px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
    }

    .progress-modern .progress-bar {
        background: #212529;
        border-radius: 10px;
    }

    /* Lightbox */
    #reviewImageLightbox .modal-content {
        background: rgba(0, 0, 0, 0.95);
        border-radius: 12px;
        overflow: hidden;
    }

    .lightbox-container {
        position: relative;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-image-wrapper {
        flex: 1;
        padding: 2rem;
    }

    #lightboxImage {
        max-height: 85vh;
        max-width: 100%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        transition: opacity 0.3s ease;
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1050;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.95);
        color: #1a1a1a;
        border-color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .lightbox-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
    }

    .lightbox-close {
        z-index: 1051;
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border-radius: 50%;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0.8;
    }

    .lightbox-close:hover {
        background-color: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg) scale(1.1);
        opacity: 1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    .lightbox-prev {
        left: 20px;
    }

    .lightbox-next {
        right: 20px;
    }

    .lightbox-counter-badge {
        z-index: 1051;
        font-size: 0.95rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: white;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        letter-spacing: 1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Empty State */
    .empty-reviews {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
        margin: 2rem 0;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .empty-reviews i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1.25rem;
        opacity: 0.7;
        display: block;
    }

    .empty-reviews p {
        font-size: 1rem;
        font-weight: 500;
        color: #475569;
        margin: 0;
        line-height: 1.6;
    }

    /* Review Actions */
    .review-actions {
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
        margin-top: 1rem;
    }

    .review-actions .btn {
        font-size: 0.8125rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .review-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .review-actions .btn-helpful.active,
    .review-actions .btn-helpful.btn-primary {
        background: #212529;
        border-color: transparent;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .reviews-section {
            padding: 2rem 0;
        }

        .reviews-section h3 {
            font-size: 1.5rem;
        }

        .rating-average {
            font-size: 2.5rem;
        }

        .rating-stars-large {
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .btn-pill {
            padding: 0.5rem 1.25rem;
            font-size: 0.8rem;
        }

        .review-item {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .review-avatar {
            width: 56px;
            height: 56px;
            font-size: 1.25rem;
        }

        .review-user-name {
            font-size: 1rem;
        }

        .review-images-grid {
            gap: 0.375rem;
            flex-wrap: nowrap !important;
            flex-direction: row !important;
        }

        .review-image-item {
            width: 90px !important;
            min-width: 90px !important;
            max-width: 90px !important;
            height: 90px !important;
            flex-shrink: 0 !important;
        }

        .review-image-item .image-wrapper {
            width: 90px;
            height: 90px;
        }


        .review-filters {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .rating-average {
            font-size: 2rem;
        }

        .rating-bar-item {
            margin-bottom: 0.75rem;
        }

        .rating-bar-bg {
            height: 24px;
        }

        .review-item {
            padding: 1.25rem;
            border-radius: 12px;
        }

        .review-avatar {
            width: 48px;
            height: 48px;
            font-size: 1.1rem;
        }

        .review-user-name {
            font-size: 0.9375rem;
        }

        .review-content {
            font-size: 0.875rem;
            line-height: 1.7;
        }

        .review-images-grid {
            gap: 0.25rem;
            flex-wrap: nowrap !important;
            flex-direction: row !important;
        }

        .review-image-item {
            width: 90px !important;
            min-width: 90px !important;
            max-width: 90px !important;
            height: 90px !important;
            flex-shrink: 0 !important;
        }

        .review-image-item .image-wrapper {
            width: 90px;
            height: 90px;
        }


        .review-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .review-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const productSlug = '{{ $product->slug }}';
    const reviewsUrl = '{{ route("client.product.reviews", ["slug" => $product->slug]) }}';
    const reviewStoreUrl = '{{ route("client.product.review.store", ["slug" => $product->slug]) }}';
    let currentPage = 1;
    let currentRating = 'all';
    let currentFilter = '';
    let currentSort = 'newest';

    // Real-time update checker
    let lastUpdateCheck = Date.now();
    let updateCheckInterval = null;

    function startUpdateChecker() {
        if (updateCheckInterval) {
            clearInterval(updateCheckInterval);
        }

        updateCheckInterval = setInterval(async () => {
            // Chỉ check khi tab đang active
            if (document.visibilityState === 'visible') {
                try {
                    const checkUrl = `{{ route('client.product.reviews.check-updates', $product->slug) }}?last_check=${lastUpdateCheck}`;
                    const response = await fetch(checkUrl);
                    const data = await response.json();

                    if (data.has_updates) {
                        console.log('Reviews updated, reloading silently...');
                        // Reload reviews silently (không scroll to top)
                        loadReviews(currentPage, false, true);
                        lastUpdateCheck = Date.now();
                    }
                } catch (error) {
                    console.error('Update check failed:', error);
                }
            }
        }, 30000); // Check every 30 seconds
    }

    function stopUpdateChecker() {
        if (updateCheckInterval) {
            clearInterval(updateCheckInterval);
            updateCheckInterval = null;
        }
    }

    // Load reviews on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadReviews();
        startUpdateChecker();

        // Check for open_review param to auto-open review modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('open_review')) {
            // Activate the reviews tab if it exists
            const reviewsTabBtn = document.getElementById('reviews-tab');
            if (reviewsTabBtn) {
                const tabTrigger = new bootstrap.Tab(reviewsTabBtn);
                tabTrigger.show();
            }

            const btnWriteReview = document.getElementById('btnWriteReview');
            const reviewsSection = document.getElementById('reviewsSection');

            if (reviewsSection) {
                // Scroll to reviews section immediately
                // Wait a bit for the tab to switch
                setTimeout(() => {
                    reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 200);
            }

            if (btnWriteReview) {
                // Open modal after a short delay to allow scrolling
                setTimeout(() => {
                    btnWriteReview.click();
                }, 1000);
            }
        }

        // Không gọi initReviewForm ở đây vì modal chưa được render
        // Sẽ gọi khi modal được mở

        // Force resize images on load
        setTimeout(() => {
            try {
                forceResizeReviewImages();
            } catch (e) {
                console.warn('Error resizing images on load:', e);
            }
        }, 500);

        // Rating Text Logic - Lung Linh
        const ratingInputs = document.querySelectorAll('input[name="rating"]');
        const ratingText = document.getElementById('ratingText');
        const ratingLabels = {
            1: 'Tệ',
            2: 'Không hài lòng',
            3: 'Bình thường',
            4: 'Hài lòng',
            5: 'Tuyệt vời'
        };

        if (ratingInputs.length > 0 && ratingText) {
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const val = this.value;
                    if (ratingLabels[val]) {
                        ratingText.textContent = ratingLabels[val];
                        ratingText.classList.add('highlight');

                        // Add sparkle effect class to wrapper if desired, but CSS handles it nicely
                    }
                });

                // Hover effects
                const label = document.querySelector(`label[for="${input.id}"]`);
                if (label) {
                    label.addEventListener('mouseenter', () => {
                        ratingText.textContent = ratingLabels[input.value];
                    });
                    label.addEventListener('mouseleave', () => {
                        const checked = document.querySelector('input[name="rating"]:checked');
                        if (checked) {
                            ratingText.textContent = ratingLabels[checked.value];
                        } else {
                            ratingText.textContent = 'Chọn số sao từ 1 đến 5';
                            ratingText.classList.remove('highlight');
                        }
                    });
                }
            });
        }
    });

    // Stop checker khi page unload
    window.addEventListener('beforeunload', function() {
        stopUpdateChecker();
    });

    // Observer để resize images khi có thay đổi
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            try {
                forceResizeReviewImages();
            } catch (e) {
                console.warn('Error resizing images in observer:', e);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const reviewsList = document.getElementById('reviews-list');
            if (reviewsList) {
                observer.observe(reviewsList, {
                    childList: true,
                    subtree: true
                });
            }
        });
    }

    // Load reviews via AJAX
    function loadReviews(page = 1, append = false, silent = false) {
        currentPage = page;
        const reviewsList = document.getElementById('reviews-list');
        const pagination = document.getElementById('reviews-pagination');

        if (!append) {
            reviewsList.innerHTML = `
                <div class="reviews-loading">
                    <div class="loading-spinner">
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                    </div>
                    <p class="loading-text">Đang tải đánh giá...</p>
                </div>
            `;
        }

        const params = new URLSearchParams({
            page: page,
            per_page: 10,
        });

        if (currentRating !== 'all') {
            params.append('rating', currentRating);
        }
        if (currentFilter === 'with-images') {
            params.append('has_images', '1');
        }
        if (currentFilter === 'verified') {
            params.append('verified', '1');
        }
        if (currentSort) {
            params.append('sort', currentSort);
        }

        if (!reviewsUrl) {
            console.error('reviewsUrl is not defined');
            if (reviewsList) {
                reviewsList.innerHTML = `
                    <div class="empty-reviews">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>Lỗi cấu hình: Không tìm thấy URL đánh giá.</p>
                    </div>
                `;
            }
            return;
        }

        fetch(`${reviewsUrl}?${params}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (!reviewsList) {
                    console.error('reviews-list element not found');
                    return;
                }

                if (data.status === 'success') {
                    try {
                        if (append) {
                            // Append mode - add to existing reviews
                            const existingHtml = reviewsList.innerHTML;
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = existingHtml;
                            const newReviewsHtml = renderReviewsHTML(data.data.reviews || []);
                            tempDiv.innerHTML += newReviewsHtml;
                            reviewsList.innerHTML = tempDiv.innerHTML;
                        } else {
                            // Normal mode - replace all
                            renderReviews(data.data.reviews || []);
                        }

                        if (data.data && data.data.pagination) {
                            renderPagination(data.data.pagination);
                        }

                        if (!append && data.data) {
                            updateSummary(data.data);

                            // Scroll to reviews section (only if not silent)
                            if (!silent) {
                                const reviewsSection = document.getElementById('reviewsSection');
                                if (reviewsSection && page === 1) {
                                    reviewsSection.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start'
                                    });
                                }
                            }
                        }

                        // Re-enable load more button
                        const loadMoreBtn = document.querySelector('.btn-load-more');
                        if (loadMoreBtn) {
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down me-1"></i>Tải thêm đánh giá';
                        }

                        // Force resize images to 90px
                        setTimeout(() => {
                            try {
                                forceResizeReviewImages();
                            } catch (e) {
                                console.warn('Error resizing images:', e);
                            }
                        }, 100);
                    } catch (renderError) {
                        console.error('Error rendering reviews:', renderError);
                        reviewsList.innerHTML = `
                            <div class="empty-reviews">
                                <i class="bi bi-exclamation-triangle"></i>
                                <p>Có lỗi xảy ra khi hiển thị đánh giá. Vui lòng thử lại.</p>
                            </div>
                        `;
                    }
                } else {
                    reviewsList.innerHTML = `
                        <div class="empty-reviews">
                            <i class="bi bi-inbox"></i>
                            <p>${data.message || 'Không thể tải đánh giá. Vui lòng thử lại sau.'}</p>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error('Error loading reviews:', err);
                console.error('Error details:', {
                    message: err.message,
                    stack: err.stack,
                    reviewsUrl: reviewsUrl,
                    params: params.toString()
                });
                if (reviewsList) {
                    reviewsList.innerHTML = `
                        <div class="empty-reviews">
                            <i class="bi bi-exclamation-triangle"></i>
                            <p>Có lỗi xảy ra khi tải đánh giá. Vui lòng thử lại sau.</p>
                            <small class="text-muted mt-2 d-block">Lỗi: ${err.message || 'Unknown error'}</small>
                            <button class="btn btn-sm btn-orange-theme mt-2" onclick="loadReviews(1)">Thử lại</button>
                        </div>
                    `;
                }
            });
    }

    // Render reviews HTML (return HTML string)
    function renderReviewsHTML(reviews) {
        if (!reviews || !Array.isArray(reviews) || reviews.length === 0) {
            return '';
        }

        let html = '';
        reviews.forEach((review, reviewIndex) => {
            try {
            let imagesHtml = '';
            // Kiểm tra và xử lý images
            if (review.images && Array.isArray(review.images) && review.images.length > 0) {
                // Lọc bỏ các image rỗng hoặc null
                const validImages = review.images.filter(img => img && img.trim() !== '');

                if (validImages.length > 0) {
                    imagesHtml = '<div class="review-images-grid">';
                    validImages.forEach((img, index) => {
                        // Escape URL để tránh XSS và lỗi syntax
                        const safeImg = String(img).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        imagesHtml += `
                        <div class="review-image-item" onclick="openReviewLightbox(this, '${safeImg}', ${index})" aria-label="Xem ảnh đánh giá" role="button">
                            <div class="image-wrapper">
                                <img src="${safeImg}"
                                     alt="Review image ${index + 1}"
                                     loading="lazy"
                                     onerror="handleImageError(this, '${safeImg}');">
                            </div>
                        </div>
                    `;
                    });
                    imagesHtml += '</div>';
                }
            }

            let adminReplyHtml = '';
            const adminRepliesData = Array.isArray(review.admin_replies) ? [...review.admin_replies] : [];
            if (adminRepliesData.length === 0 && review.admin_reply && review.admin_reply.content) {
                adminRepliesData.push(review.admin_reply);
            }
            if (adminRepliesData.length > 0) {
                adminReplyHtml = adminRepliesData.map((reply) => {
                    const adminName = reply.admin_name || 'Admin';
                    const adminCreatedAt = reply.created_at || '';
                    const replyContent = reply.content ? escapeHtml(reply.content) : '';
                    if (!replyContent) {
                        return '';
                    }
                    return `
                        <div class="admin-reply">
                            <div class="admin-reply-header">
                                <i class="bi bi-shield-check me-1"></i>
                                Phản hồi từ ${escapeHtml(adminName)}
                            </div>
                            <p class="admin-reply-content">${replyContent}</p>
                            <small class="text-muted">${adminCreatedAt}</small>
                        </div>
                    `;
                }).join('');
            }

            // Validate review data
            const reviewId = review.id || reviewIndex;
            const userName = review.user_name || 'Người dùng';
            const userInitial = review.user_initial || userName.charAt(0).toUpperCase();
            const rating = review.rating || 0;
            const content = review.content || '';
            const createdAt = review.created_at_human || review.created_at || '';
            const isVerified = review.is_verified_buyer || false;
            const helpfulCount = review.helpful_count || 0;
            const isHelpful = review.is_helpful || false;

            // Determine button classes based on helpful status
            const helpfulButtonClass = isHelpful
                ? 'btn btn-sm btn-primary btn-helpful'
                : 'btn btn-sm btn-outline-primary btn-helpful';

            html += `
                <div class="review-item" data-review-id="${reviewId}">
                    <div class="d-flex align-items-start">
                        <div class="review-avatar me-3">
                            ${userInitial}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                                <div>
                                    <span class="review-user-name">${escapeHtml(userName)}</span>
                                    ${isVerified ? '<span class="review-verified-badge"><i class="bi bi-check-circle me-1"></i>Đã mua hàng</span>' : ''}
                                </div>
                                <small class="review-date">${escapeHtml(createdAt)}</small>
                            </div>
                            <div class="review-rating mb-2">
                                ${generateStars(rating)}
                            </div>
                            ${content || imagesHtml ? `<div class="review-content">${content ? escapeHtml(content) : ''}${imagesHtml}</div>` : ''}
                            ${adminReplyHtml}
                            <div class="review-actions">
                                <button class="${helpfulButtonClass}" onclick="markHelpful(${reviewId}, this)" data-review-id="${reviewId}" data-is-helpful="${isHelpful ? '1' : '0'}" type="button">
                                    <i class="bi bi-hand-thumbs-up${isHelpful ? '-fill' : ''}"></i>
                                    <span class="helpful-text">${isHelpful ? 'Đã đánh dấu' : 'Hữu ích'}</span>
                                    <span class="helpful-count ms-1">(${helpfulCount})</span>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-report" onclick="openReportModal(${reviewId})" type="button">
                                    <i class="bi bi-flag"></i> Báo cáo
                                </button>
                                <button class="btn btn-sm btn-outline-secondary btn-share" onclick="shareReview(${reviewId})" type="button">
                                    <i class="bi bi-share"></i> Chia sẻ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            } catch (error) {
                console.error(`Error rendering review ${reviewIndex}:`, error, review);
                // Skip this review if there's an error
            }
        });
        return html;
    }

    // Force resize all review images to 90px
    function forceResizeReviewImages() {
        try {
            const imageWrappers = document.querySelectorAll('.review-image-item .image-wrapper');
            const images = document.querySelectorAll('.review-image-item img');

            if (imageWrappers.length === 0 && images.length === 0) {
                return; // No images to resize
            }

            imageWrappers.forEach(wrapper => {
                if (wrapper) {
                    wrapper.style.width = '90px';
                    wrapper.style.height = '90px';
                    wrapper.style.minWidth = '90px';
                    wrapper.style.maxWidth = '90px';
                    wrapper.style.minHeight = '90px';
                    wrapper.style.maxHeight = '90px';
                }
            });

            images.forEach(img => {
                if (img) {
                    img.style.width = '90px';
                    img.style.height = '90px';
                    img.style.minWidth = '90px';
                    img.style.maxWidth = '90px';
                    img.style.minHeight = '90px';
                    img.style.maxHeight = '90px';
                    img.style.objectFit = 'contain';
                }
            });
        } catch (error) {
            console.warn('Error in forceResizeReviewImages:', error);
        }
    }

    // Render reviews
    function renderReviews(reviews) {
        const reviewsList = document.getElementById('reviews-list');

        if (!reviewsList) {
            console.error('reviews-list element not found');
            return;
        }

        if (!reviews || reviews.length === 0) {
            reviewsList.innerHTML = `
                <div class="empty-reviews">
                    <i class="bi bi-chat-left-text"></i>
                    <p class="mb-0">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                </div>
            `;
            return;
        }

        try {
            // Add fade-in animation
            reviewsList.style.opacity = '0';
            reviewsList.innerHTML = renderReviewsHTML(reviews);

            // Force resize images to 90px
            setTimeout(() => {
                try {
                    forceResizeReviewImages();
                } catch (e) {
                    console.warn('Error resizing images:', e);
                }
            }, 100);

            // Trigger animation
            setTimeout(() => {
                reviewsList.style.transition = 'opacity 0.3s ease-in';
                reviewsList.style.opacity = '1';
            }, 10);
        } catch (error) {
            console.error('Error in renderReviews:', error);
            reviewsList.innerHTML = `
                <div class="empty-reviews">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Có lỗi xảy ra khi hiển thị đánh giá.</p>
                </div>
            `;
        }
    }

    // Generate stars HTML
    function generateStars(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                html += '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                html += '<i class="bi bi-star text-muted"></i>';
            }
        }
        return html;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Render pagination
    function renderPagination(pagination) {
        const paginationEl = document.getElementById('reviews-pagination');

        if (pagination.last_page <= 1) {
            paginationEl.classList.add('d-none');
            return;
        }

        paginationEl.classList.remove('d-none');

        let html = '<nav><ul class="pagination justify-content-center">';

        // Previous button
        if (pagination.current_page > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadReviews(${pagination.current_page - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                html += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadReviews(${i}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadReviews(${pagination.current_page + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
        }

        html += '</ul></nav>';
        paginationEl.innerHTML = html;
    }

    // Update summary (if needed)
    function updateSummary(data) {
        // Could update summary stats here if needed
    }

    // Filter buttons
    document.querySelectorAll('.review-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.review-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Reset về all rating và empty filter khi chọn sort
            const sortValue = this.dataset.sort;
            if (sortValue) {
                // Nếu là sort button (newest hoặc helpful), reset filter và rating
                currentRating = 'all';
                currentFilter = '';
                currentSort = sortValue;
            } else {
                // Nếu là filter button, giữ sort hiện tại hoặc reset về newest
            currentRating = this.dataset.rating || 'all';
            currentFilter = this.dataset.filter || '';
                // Chỉ reset sort nếu không có sort được set
                if (!this.dataset.sort) {
                    currentSort = 'newest';
                }
            }

            loadReviews(1);
        });
    });

    // Write review button
    document.getElementById('btnWriteReview')?.addEventListener('click', function() {
        const modalElement = document.getElementById('writeReviewModal');
        if (!modalElement) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

        // Khởi tạo form khi modal được mở (nếu chưa khởi tạo)
        modalElement.addEventListener('shown.bs.modal', function() {
            initReviewForm();
        }, { once: true });

        modal.show();
    });

    // Flag để tránh duplicate event listeners
    let formInitialized = false;

    // Initialize review form
    function initReviewForm() {
        const form = document.getElementById('review-form');
        if (!form) return;

        // Chỉ khởi tạo một lần
        if (formInitialized) return;
        formInitialized = true;

        const textarea = form.querySelector('textarea[name="comment"]');
        const charCount = document.getElementById('charCount');
        const imageInput = document.getElementById('reviewImages');
        const uploadArea = document.getElementById('uploadArea');

        // Character count with progress bar and validation
        const charCountBar = document.getElementById('charCountBar');
        const minCharWarning = document.getElementById('minCharWarning');

        if (textarea && charCount) {
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                const maxLength = 1000;
                const minLength = 20;

                // Update count
                charCount.textContent = length;

                // Update progress bar
                if (charCountBar) {
                    const percentage = Math.min((length / maxLength) * 100, 100);
                    charCountBar.style.width = percentage + '%';

                    // Change color based on percentage
                    if (percentage >= 90) {
                        charCountBar.style.background = 'linear-gradient(90deg, #dc3545 0%, #c82333 100%)';
                    } else if (percentage >= 70) {
                        charCountBar.style.background = 'linear-gradient(90deg, #ffc107 0%, #ff9800 100%)';
                    } else if (length >= minLength) {
                        charCountBar.style.background = 'linear-gradient(90deg, #28a745 0%, #20c997 100%)';
                    } else {
                        charCountBar.style.background = 'linear-gradient(90deg, #212529 0%, #343a40 100%)';
                    }
                }

                // Show/hide min character warning
                if (minCharWarning) {
                    if (length > 0 && length < minLength) {
                        minCharWarning.style.display = 'inline';
                        charCount.style.color = '#dc3545';
                    } else {
                        minCharWarning.style.display = 'none';
                        if (length > maxLength) {
                            charCount.style.color = '#dc3545';
                        } else if (length >= minLength) {
                            charCount.style.color = '#28a745';
                        } else {
                            charCount.style.color = '#6c757d';
                        }
                    }
                }

                // Update textarea border color
                if (length >= minLength && length <= maxLength) {
                    textarea.classList.remove('is-invalid');
                    textarea.classList.add('is-valid');
                } else if (length > 0 && length < minLength) {
                    textarea.classList.remove('is-valid');
                    textarea.classList.add('is-invalid');
                } else {
                    textarea.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        // Rating text update
        const ratingText = document.getElementById('ratingText');
        const ratingInputs = form.querySelectorAll('input[name="rating"]');

        if (ratingInputs && ratingText) {
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const rating = parseInt(this.value);
                    const ratingLabels = {
                        1: '⭐ Rất không hài lòng',
                        2: '⭐⭐ Không hài lòng',
                        3: '⭐⭐⭐ Bình thường',
                        4: '⭐⭐⭐⭐ Hài lòng',
                        5: '⭐⭐⭐⭐⭐ Rất hài lòng'
                    };
                    ratingText.textContent = ratingLabels[rating] || 'Chọn số sao từ 1 đến 5';
                    ratingText.style.color = '#212529';
                    ratingText.style.fontWeight = '600';
                });
            });
        }

        // Image upload
        if (imageInput && uploadArea) {
            // Click to upload
            uploadArea.addEventListener('click', function(e) {
                if (e.target !== imageInput && !e.target.closest('.preview-remove')) {
                    imageInput.click();
                }
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleImageFiles(files);
                }
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleImageFiles(this.files);
                }
            });
        }

        // Handle image files
        function handleImageFiles(files) {
            // Lấy lại các element mỗi lần để đảm bảo chúng tồn tại
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');

            if (!previewContainer || !imagePreview) {
                console.error('Preview container không tìm thấy. previewContainer:', previewContainer, 'imagePreview:', imagePreview);
                alert('Không thể hiển thị preview ảnh. Vui lòng thử lại.');
                return;
            }

            console.log('Handling image files:', files.length, 'files');

            const maxImages = parseInt(imageInput.dataset.max) || 5;
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

            let validFiles = [];
            let currentCount = previewContainer.children.length;

            for (let file of files) {
                if (validFiles.length + currentCount >= maxImages) {
                    alert(`Bạn chỉ có thể tải lên tối đa ${maxImages} ảnh`);
                    break;
                }

                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} không đúng định dạng. Chỉ chấp nhận JPG, PNG, WEBP.`);
                    continue;
                }

                if (file.size > maxSize) {
                    alert(`File ${file.name} vượt quá 2MB.`);
                    continue;
                }

                validFiles.push(file);
            }

            // Show upload progress
            if (validFiles.length > 0) {
                simulateUploadProgress();
            }

            validFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('File loaded:', file.name, 'Size:', file.size);
                    const div = document.createElement('div');
                    div.style.width = '50px';
                    div.style.height = '50px';
                    div.style.minWidth = '50px';
                    div.style.minHeight = '50px';
                    div.style.maxWidth = '50px';
                    div.style.maxHeight = '50px';
                    div.style.flexShrink = '0';
                    div.style.flexGrow = '0';
                    div.innerHTML = `
                        <div class="preview-item" style="width: 50px !important; height: 50px !important; min-width: 50px !important; min-height: 50px !important; max-width: 50px !important; max-height: 50px !important; flex-shrink: 0 !important; flex-grow: 0 !important;">
                            <img src="${e.target.result}" alt="Preview ${index + 1}" style="width: 50px !important; height: 50px !important; min-width: 50px !important; min-height: 50px !important; max-width: 50px !important; max-height: 50px !important; object-fit: cover !important; display: block !important;">
                            <button type="button" class="preview-remove" onclick="removePreview(this)" title="Xóa ảnh">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                    previewContainer.appendChild(div);

                    // Force resize immediately and after append
                    const img = div.querySelector('img');
                    const previewItem = div.querySelector('.preview-item');
                    if (img) {
                        img.setAttribute('style', 'width: 50px !important; height: 50px !important; min-width: 50px !important; min-height: 50px !important; max-width: 50px !important; max-height: 50px !important; object-fit: cover !important; display: block !important;');
                    }
                    if (previewItem) {
                        previewItem.setAttribute('style', 'width: 50px !important; height: 50px !important; min-width: 50px !important; min-height: 50px !important; max-width: 50px !important; max-height: 50px !important; flex-shrink: 0 !important; flex-grow: 0 !important; position: relative; border-radius: 10px; overflow: hidden;');
                    }

                    // Force resize after append
                    setTimeout(() => {
                        forceResizePreviewImages();
                    }, 10);
                    imagePreview.classList.remove('d-none');

                    // Update image count
                    const imageCount = document.getElementById('imageCount');
                    if (imageCount) {
                        imageCount.textContent = previewContainer.children.length;
                    }

                    console.log('Preview added. Total previews:', previewContainer.children.length);
                };
                reader.onerror = function(error) {
                    console.error('Lỗi khi đọc file:', file.name, error);
                    alert('Không thể đọc file ' + file.name);
                };
                reader.readAsDataURL(file);
            });
        }

        // Form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const rating = form.querySelector('input[name="rating"]:checked');
            const comment = form.querySelector('textarea[name="comment"]').value.trim();
            const ratingInputs = form.querySelectorAll('input[name="rating"]');
            const textarea = form.querySelector('textarea[name="comment"]');

            // Validation với visual feedback
            let hasError = false;

            // Validate rating
            if (!rating) {
                hasError = true;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thiếu thông tin',
                        text: 'Vui lòng chọn đánh giá sao',
                        confirmButtonText: 'Đã hiểu'
                    });
                } else {
                alert('Vui lòng chọn đánh giá sao');
                }

                // Highlight rating input
                ratingInputs.forEach(input => {
                    input.closest('.rating-input-wrapper')?.classList.add('is-invalid');
                });
                return;
            } else {
                ratingInputs.forEach(input => {
                    input.closest('.rating-input-wrapper')?.classList.remove('is-invalid');
                });
            }

            // Validate comment
            if (!comment || comment.length < 20) {
                hasError = true;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nhận xét quá ngắn',
                        text: 'Vui lòng viết tối thiểu 20 ký tự để đánh giá có ý nghĩa',
                        confirmButtonText: 'Đã hiểu'
                    });
                } else {
                alert('Vui lòng viết tối thiểu 20 ký tự');
                }

                // Highlight textarea
                if (textarea) {
                    textarea.classList.add('is-invalid');
                    textarea.focus();
                }
                return;
            } else {
                if (textarea) {
                    textarea.classList.remove('is-invalid');
                }
            }

            if (hasError) return;

            // Disable submit button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang gửi...';
            }

            const formData = new FormData(form);

            fetch(reviewStoreUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => {
                        throw new Error(data.message || 'HTTP error! status: ' + res.status);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('writeReviewModal'));

                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message || 'Cảm ơn bạn đã đánh giá!',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        }).then(() => {
                            if (modal) modal.hide();
                        });
                    } else {
                        alert(data.message || 'Cảm ơn bạn đã đánh giá!');
                        if (modal) modal.hide();
                    }

                    // Reset form
                    resetReviewForm();

                    // Reload reviews after a delay
                    setTimeout(() => {
                        loadReviews(1);
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(err => {
                console.error('Error submitting review:', err);

                // Show error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: err.message || 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại sau.',
                        confirmButtonText: 'Đã hiểu'
                    });
                } else {
                    alert(err.message || 'Có lỗi xảy ra khi gửi đánh giá');
                }
            })
            .finally(() => {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send me-1"></i>Gửi đánh giá';
                }
            });
        });

        // Reset review form function
        function resetReviewForm() {
            const form = document.getElementById('review-form');
            if (!form) return;

            form.reset();

            // Reset rating
            const ratingInputs = form.querySelectorAll('input[name="rating"]');
            ratingInputs.forEach(input => {
                input.checked = false;
                input.closest('.rating-input-wrapper')?.classList.remove('is-invalid');
            });

            // Reset rating text
            const ratingText = document.getElementById('ratingText');
            if (ratingText) {
                ratingText.textContent = 'Chọn số sao từ 1 đến 5';
                ratingText.style.color = '';
                ratingText.style.fontWeight = '';
            }

            // Reset textarea
            const textarea = form.querySelector('textarea[name="comment"]');
            if (textarea) {
                textarea.value = '';
                textarea.classList.remove('is-valid', 'is-invalid');
            }

            // Reset character count
            const charCount = document.getElementById('charCount');
            const charCountBar = document.getElementById('charCountBar');
            const minCharWarning = document.getElementById('minCharWarning');

            if (charCount) charCount.textContent = '0';
            if (charCountBar) charCountBar.style.width = '0%';
            if (minCharWarning) minCharWarning.style.display = 'none';

            // Reset images
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const imageInput = document.getElementById('reviewImages');
            const imageCount = document.getElementById('imageCount');

            if (previewContainer) previewContainer.innerHTML = '';
            if (imagePreview) imagePreview.classList.add('d-none');
            if (imageInput) imageInput.value = '';
            if (imageCount) imageCount.textContent = '0';
        }
    }

    // Remove preview image
    function removePreview(btn) {
        btn.closest('div').remove();
        const previewContainer = document.getElementById('previewContainer');
        const imageCount = document.getElementById('imageCount');

        if (imageCount) {
            imageCount.textContent = previewContainer ? previewContainer.children.length : 0;
        }

        if (previewContainer && previewContainer.children.length === 0) {
            document.getElementById('imagePreview').classList.add('d-none');
        }
    }

    // Clear all previews
    function clearAllPreviews() {
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const imageInput = document.getElementById('reviewImages');
        const imageCount = document.getElementById('imageCount');

        if (previewContainer) {
            previewContainer.innerHTML = '';
        }

        if (imagePreview) {
            imagePreview.classList.add('d-none');
        }

        if (imageInput) {
            imageInput.value = '';
        }

        if (imageCount) {
            imageCount.textContent = '0';
        }
    }

    // Clear all previews
    function clearAllPreviews() {
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const imageInput = document.getElementById('reviewImages');
        const imageCount = document.getElementById('imageCount');

        if (previewContainer) {
            previewContainer.innerHTML = '';
        }

        if (imagePreview) {
            imagePreview.classList.add('d-none');
        }

        if (imageInput) {
            imageInput.value = '';
        }

        if (imageCount) {
            imageCount.textContent = '0';
        }
    }

    // Mark review as helpful (toggle - like/unlike)
    function markHelpful(reviewId, button) {
        // Disable button during request to prevent double clicks
        const originalDisabled = button.disabled;
        button.disabled = true;

        fetch(`/reviews/${reviewId}/helpful`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Update button state based on is_helpful
                const isHelpful = data.is_helpful === true || data.is_helpful === 1;
                const icon = button.querySelector('i');
                const textSpan = button.querySelector('.helpful-text');
                const countSpan = button.querySelector('.helpful-count');

                if (isHelpful) {
                    // User just liked - show active state
                button.classList.add('btn-primary');
                button.classList.remove('btn-outline-primary');
                    button.setAttribute('data-is-helpful', '1');
                    if (icon) {
                        icon.className = 'bi bi-hand-thumbs-up-fill';
                    }
                    if (textSpan) {
                        textSpan.textContent = 'Đã đánh dấu';
                }
            } else {
                    // User just unliked - show inactive state
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-outline-primary');
                    button.setAttribute('data-is-helpful', '0');
                    if (icon) {
                        icon.className = 'bi bi-hand-thumbs-up';
                    }
                    if (textSpan) {
                        textSpan.textContent = 'Hữu ích';
                    }
                }

                // Update count
                if (countSpan) {
                    countSpan.textContent = `(${data.helpful_count || 0})`;
                }

                // Show toast notification
                showToast(data.message, 'success');
            } else {
                // Re-enable button on error
                button.disabled = originalDisabled;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            console.error(err);
            // Re-enable button on error
            button.disabled = originalDisabled;
            showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        })
        .finally(() => {
            // Re-enable button after a short delay to prevent rapid clicking
            setTimeout(() => {
                button.disabled = false;
            }, 500);
        });
    }

    // Open report modal
    function openReportModal(reviewId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Báo cáo đánh giá',
                html: `
                    <form id="reportForm">
                        <div class="mb-3">
                            <label class="form-label">Lý do báo cáo *</label>
                            <select class="form-select" id="reportReason" required>
                                <option value="">Chọn lý do...</option>
                                <option value="spam">Spam</option>
                                <option value="inappropriate">Nội dung không phù hợp</option>
                                <option value="offensive">Xúc phạm</option>
                                <option value="false_info">Thông tin sai sự thật</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả chi tiết (tùy chọn)</label>
                            <textarea class="form-control" id="reportDescription" rows="3" placeholder="Mô tả thêm về vấn đề..."></textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Gửi báo cáo',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const reason = document.getElementById('reportReason').value;
                    const description = document.getElementById('reportDescription').value;
                    if (!reason) {
                        Swal.showValidationMessage('Vui lòng chọn lý do báo cáo');
                        return false;
                    }
                    return { reason, description };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/reviews/${reviewId}/report`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cảm ơn!',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra'
                        });
                    });
                }
            });
        } else {
            const reason = prompt('Lý do báo cáo:');
            if (reason) {
                fetch(`/reviews/${reviewId}/report`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason, description: '' })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message || 'Cảm ơn bạn đã báo cáo!');
                });
            }
        }
    }

    // Share review
    function shareReview(reviewId) {
        const url = `${window.location.origin}${window.location.pathname}#review-${reviewId}`;

        // Try native share API first (mobile/desktop with share support)
        if (navigator.share) {
            navigator.share({
                title: 'Đánh giá sản phẩm',
                text: 'Xem đánh giá này',
                url: url
            })
            .then(() => {
                showToast('Đã chia sẻ thành công!', 'success');
            })
            .catch((error) => {
                // User cancelled or error occurred
                if (error.name !== 'AbortError') {
                    console.error('Share error:', error);
                    // Fallback to clipboard
                    copyToClipboard(url);
                }
            });
        } else {
            // Fallback to clipboard
            copyToClipboard(url);
        }
    }

    // Helper function to copy to clipboard
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showToast('Link đã được sao chép vào clipboard!', 'success');
                })
                .catch((err) => {
                    console.error('Clipboard error:', err);
                    // Fallback to old method
                    fallbackCopyToClipboard(text);
                    });
                } else {
            // Fallback for older browsers
            fallbackCopyToClipboard(text);
        }
    }

    // Fallback copy method for older browsers
    function fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showToast('Link đã được sao chép vào clipboard!', 'success');
            } else {
                showToast('Không thể sao chép. Vui lòng sao chép thủ công: ' + text, 'info');
            }
        } catch (err) {
            console.error('Fallback copy error:', err);
            showToast('Không thể sao chép. Link: ' + text, 'info');
        } finally {
            document.body.removeChild(textArea);
        }
    }

    // Clear all filters
    let activeFilters = 0;
    function clearAllFilters() {
        document.querySelectorAll('.review-filter').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelectorAll('.review-filter[data-rating="all"]').forEach(btn => {
            if (!btn.dataset.filter && !btn.dataset.sort) {
                btn.classList.add('active');
            }
        });
        activeFilters = 0;
        loadReviews(1);
    }


    // Update filter count when filters change
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.review-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(() => {
                    activeFilters = document.querySelectorAll('.review-filter.active').length;
                }, 100);
            });
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('review-form');
        if (form) {
            const modal = document.getElementById('writeReviewModal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    // Force resize preview images when modal opens
                    setTimeout(() => {
                        forceResizePreviewImages();
                    }, 100);
                });

                // Add MutationObserver to auto-resize when preview container changes
                const previewContainer = document.getElementById('previewContainer');
                if (previewContainer) {
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes.length > 0) {
                                setTimeout(() => {
                                    forceResizePreviewImages();
                                }, 10);
                            }
                        });
                    });

                    observer.observe(previewContainer, {
                        childList: true,
                        subtree: true
                    });
                }
            }
        }
    });

    // Upload progress (simulated - real implementation needs FormData with progress event)
    function simulateUploadProgress() {
        const progressBar = document.querySelector('#uploadProgress .progress-bar');
        const progressContainer = document.getElementById('uploadProgress');

        if (!progressBar || !progressContainer) return;

        progressContainer.style.display = 'block';
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressBar.style.width = progress + '%';
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    progressContainer.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 500);
            }
        }, 200);
    }

    // Handle image error
    function handleImageError(imgElement, originalSrc) {
        console.warn('Image failed to load:', originalSrc);
        console.warn('Image element:', imgElement);

        // Thử lại với URL khác nếu có thể
        if (originalSrc && originalSrc.includes('/storage/')) {
            // Thử với base URL khác hoặc retry
            const retrySrc = originalSrc.replace('http://127.0.0.1:8000', window.location.origin);
            if (retrySrc !== originalSrc) {
                console.log('Retrying with:', retrySrc);
                imgElement.src = retrySrc;
                return;
            }
        }

        imgElement.onerror = null; // Prevent infinite loop
        // Hiển thị placeholder nhưng vẫn giữ URL gốc để user có thể thấy
        imgElement.classList.add('image-error');
        imgElement.style.objectFit = 'contain';
        imgElement.style.backgroundColor = '#f8f9fa';
        imgElement.alt = 'Hình ảnh không tải được: ' + originalSrc;

        // Thêm tooltip để user biết URL
        imgElement.title = 'Không thể tải hình ảnh. URL: ' + originalSrc;
    }

    // Open lightbox with image gallery (reviews only, tách biệt với lightbox sản phẩm)
    let currentImageIndex = 0;
    let currentImageList = [];

    function openReviewLightbox(element, imageSrc, index = 0) {
        const lightbox = document.getElementById('reviewImageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');

        // Lấy tất cả images từ review hiện tại
        const reviewItem = element.closest('.review-item');
        if (reviewItem) {
            const imageItems = reviewItem.querySelectorAll('.review-image-item');
            currentImageList = Array.from(imageItems).map(item => {
                const img = item.querySelector('img');
                return img ? img.src : null;
            }).filter(Boolean);
            currentImageIndex = index;
        } else {
            currentImageList = [imageSrc];
            currentImageIndex = 0;
        }

        // Hiển thị image
        showLightboxImage(currentImageIndex);

        const modal = new bootstrap.Modal(lightbox);
        modal.show();
    }

    function showLightboxImage(index) {
        if (currentImageList.length === 0) return;

        const lightboxImage = document.getElementById('lightboxImage');
        const imageSrc = currentImageList[index];

        // Show loading
        lightboxImage.style.opacity = '0.5';
        lightboxImage.onload = function() {
            this.style.opacity = '1';
        };
        lightboxImage.onerror = function() {
            this.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Ccircle cx='8.5' cy='8.5' r='1.5'%3E%3C/circle%3E%3Cpolyline points='21 15 16 10 5 21'%3E%3C/polyline%3E%3C/svg%3E";
            this.style.opacity = '1';
            this.style.padding = '50px';
            this.style.background = '#f8f9fa';
        };

        // Convert relative path to full URL if needed
        if (!imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
            lightboxImage.src = '/' + imageSrc;
        } else {
            lightboxImage.src = imageSrc;
        }

        // Update navigation buttons
        updateLightboxNavigation();
    }

    function updateLightboxNavigation() {
        const prevBtn = document.getElementById('lightboxPrev');
        const nextBtn = document.getElementById('lightboxNext');
        const counter = document.getElementById('lightboxCounter');

        if (prevBtn) {
            prevBtn.style.display = currentImageList.length > 1 ? 'flex' : 'none';
            prevBtn.disabled = currentImageIndex === 0;
        }

        if (nextBtn) {
            nextBtn.style.display = currentImageList.length > 1 ? 'flex' : 'none';
            nextBtn.disabled = currentImageIndex === currentImageList.length - 1;
        }

        if (counter && currentImageList.length > 1) {
            counter.textContent = `${currentImageIndex + 1} / ${currentImageList.length}`;
            counter.style.display = 'block';
        } else if (counter) {
            counter.style.display = 'none';
        }
    }

    function lightboxPrev() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            showLightboxImage(currentImageIndex);
        }
    }

    function lightboxNext() {
        if (currentImageIndex < currentImageList.length - 1) {
            currentImageIndex++;
            showLightboxImage(currentImageIndex);
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('reviewImageLightbox');
        if (lightbox && lightbox.classList.contains('show')) {
            if (e.key === 'ArrowLeft') lightboxPrev();
            if (e.key === 'ArrowRight') lightboxNext();
            if (e.key === 'Escape') {
                const modal = bootstrap.Modal.getInstance(lightbox);
                if (modal) modal.hide();
            }
        }
    });
</script>
@endpush
