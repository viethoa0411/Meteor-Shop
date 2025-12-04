{{-- Quick View Modal --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content quick-view-modal-content">
            <style>
                /* Modal Styles */
                .quick-view-modal-content {
                    border: none;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                }
                
                .quick-view-modal-header {
                    background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);
                    color: white;
                    padding: 1.5rem 1.75rem;
                    border-bottom: none;
                    position: relative;
                    overflow: hidden;
                }
                
                .quick-view-modal-header::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    right: -50%;
                    width: 200%;
                    height: 200%;
                    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                    animation: pulse 3s ease-in-out infinite;
                }
                
                @keyframes pulse {
                    0%, 100% {
                        transform: scale(1);
                        opacity: 0.5;
                    }
                    50% {
                        transform: scale(1.1);
                        opacity: 0.8;
                    }
                }
                
                .quick-view-modal-header .modal-title {
                    color: white;
                    font-weight: 700;
                    font-size: 1.25rem;
                    margin: 0;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    position: relative;
                    z-index: 1;
                }
                
                .quick-view-modal-header .modal-title i {
                    font-size: 1.5rem;
                    background: rgba(255, 255, 255, 0.2);
                    padding: 0.5rem;
                    border-radius: 10px;
                    backdrop-filter: blur(10px);
                }
                
                .quick-view-modal-header .btn-close {
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 50%;
                    width: 36px;
                    height: 36px;
                    opacity: 1;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                    backdrop-filter: blur(10px);
                }
                
                .quick-view-modal-header .btn-close:hover {
                    background: rgba(255, 255, 255, 0.3);
                    transform: rotate(90deg);
                }
                
                .quick-view-modal-header .btn-close::before {
                    content: '×';
                    font-size: 1.5rem;
                    color: white;
                    font-weight: 300;
                }
                
                .quick-view-modal-body {
                    padding: 0;
                    background: #f8f9fa;
                    min-height: 400px;
                    max-height: 70vh;
                    overflow-y: auto;
                }
                
                .quick-view-modal-body::-webkit-scrollbar {
                    width: 8px;
                }
                
                .quick-view-modal-body::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                
                .quick-view-modal-body::-webkit-scrollbar-thumb {
                    background: #0d6efd;
                    border-radius: 4px;
                }
                
                .quick-view-modal-body::-webkit-scrollbar-thumb:hover {
                    background: #084298;
                }
                
                /* Loading State */
                .quick-view-loading {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 4rem 2rem;
                    min-height: 400px;
                }
                
                .quick-view-loading .spinner-wrapper {
                    position: relative;
                    margin-bottom: 1.5rem;
                }
                
                .quick-view-loading .spinner-border {
                    width: 4rem;
                    height: 4rem;
                    border-width: 0.4rem;
                    color: #0d6efd;
                }
                
                .quick-view-loading .loading-text {
                    color: #6c757d;
                    font-weight: 500;
                    font-size: 1rem;
                    margin-top: 1rem;
                }
                
                .quick-view-loading .loading-dots {
                    display: inline-flex;
                    gap: 0.5rem;
                    margin-top: 0.5rem;
                }
                
                .quick-view-loading .loading-dots span {
                    width: 8px;
                    height: 8px;
                    background: #0d6efd;
                    border-radius: 50%;
                    animation: bounce 1.4s ease-in-out infinite both;
                }
                
                .quick-view-loading .loading-dots span:nth-child(1) {
                    animation-delay: -0.32s;
                }
                
                .quick-view-loading .loading-dots span:nth-child(2) {
                    animation-delay: -0.16s;
                }
                
                @keyframes bounce {
                    0%, 80%, 100% {
                        transform: scale(0);
                    }
                    40% {
                        transform: scale(1);
                    }
                }
                
                /* Modal Footer */
                .quick-view-modal-footer {
                    background: white;
                    border-top: 2px solid #e9ecef;
                    padding: 1.25rem 1.75rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 1rem;
                }
                
                .quick-view-modal-footer .btn {
                    padding: 0.625rem 1.5rem;
                    font-weight: 600;
                    border-radius: 8px;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    border: none;
                }
                
                .quick-view-modal-footer .btn-secondary {
                    background: #6c757d;
                    color: white;
                }
                
                .quick-view-modal-footer .btn-secondary:hover {
                    background: #5a6268;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
                }
                
                .quick-view-modal-footer .btn-primary {
                    background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);
                    color: white;
                    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
                }
                
                .quick-view-modal-footer .btn-primary:hover {
                    background: linear-gradient(135deg, #084298 0%, #0d6efd 100%);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(13, 110, 253, 0.4);
                }
                
                .quick-view-modal-footer .btn i {
                    font-size: 1rem;
                }
                
                /* Quick View Content Styles */
                .quick-view-container {
                    padding: 0.5rem;
                }
                
                .quick-view-card {
                    background: #ffffff;
                    border: 1px solid #e9ecef;
                    border-radius: 12px;
                    padding: 1.25rem;
                    margin-bottom: 1rem;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                    transition: all 0.3s ease;
                }
                
                .quick-view-card:hover {
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
                    transform: translateY(-2px);
                }
                
                .quick-view-card-header {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    margin-bottom: 0.75rem;
                    padding-bottom: 0.75rem;
                    border-bottom: 2px solid #f8f9fa;
                }
                
                .quick-view-card-header i {
                    font-size: 1.1rem;
                    color: #0d6efd;
                }
                
                .quick-view-card-title {
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: #6c757d;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin: 0;
                }
                
                .product-card {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    border: 1px solid #e9ecef;
                    border-radius: 12px;
                    padding: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    transition: all 0.3s ease;
                }
                
                .product-card:hover {
                    border-color: #0d6efd;
                    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
                }
                
                .product-image {
                    width: 90px;
                    height: 90px;
                    object-fit: cover;
                    border-radius: 10px;
                    border: 2px solid #e9ecef;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                }
                
                .product-info h6 {
                    font-size: 1rem;
                    font-weight: 600;
                    color: #212529;
                    margin-bottom: 0.5rem;
                }
                
                .product-info h6 a {
                    color: #212529;
                    text-decoration: none;
                    transition: color 0.2s;
                }
                
                .product-info h6 a:hover {
                    color: #0d6efd;
                }
                
                .product-info small {
                    font-size: 0.8rem;
                    color: #6c757d;
                }
                
                .user-card {
                    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                    border: 1px solid #e9ecef;
                    border-radius: 12px;
                    padding: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }
                
                .user-avatar {
                    width: 70px;
                    height: 70px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 1.75rem;
                    font-weight: 700;
                    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
                    border: 3px solid white;
                }
                
                .user-info h6 {
                    font-size: 1rem;
                    font-weight: 600;
                    color: #212529;
                    margin-bottom: 0.25rem;
                }
                
                .user-info small {
                    font-size: 0.85rem;
                    color: #6c757d;
                    display: block;
                }
                
                .rating-display {
                    text-align: center;
                    padding: 1.5rem;
                    background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
                    border-radius: 12px;
                    border: 1px solid #ffc107;
                }
                
                .rating-stars {
                    font-size: 2rem;
                    color: #ffc107;
                    letter-spacing: 4px;
                    margin-bottom: 0.75rem;
                    text-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
                }
                
                .rating-badge {
                    display: inline-block;
                    padding: 0.5rem 1rem;
                    background: linear-gradient(135deg, #0d6efd 0%, #084298 100%);
                    color: white;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 0.95rem;
                    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
                }
                
                .content-box {
                    background: #f8f9fa;
                    border-left: 4px solid #0d6efd;
                    border-radius: 8px;
                    padding: 1.25rem;
                    min-height: 80px;
                }
                
                .content-box p {
                    color: #212529;
                    font-size: 0.95rem;
                    line-height: 1.6;
                    margin: 0;
                }
                
                .images-gallery {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                    gap: 0.75rem;
                }
                
                .image-item {
                    position: relative;
                    border-radius: 10px;
                    overflow: hidden;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: 2px solid #e9ecef;
                }
                
                .image-item:hover {
                    transform: scale(1.05);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    border-color: #0d6efd;
                }
                
                .image-item img {
                    width: 100%;
                    height: 120px;
                    object-fit: cover;
                    display: block;
                }
                
                .image-item::after {
                    content: '\F4CE';
                    font-family: 'bootstrap-icons';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0;
                    transition: opacity 0.3s;
                    color: white;
                    font-size: 1.5rem;
                    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
                }
                
                .image-item:hover::after {
                    opacity: 1;
                }
                
                .image-item:hover img {
                    filter: brightness(0.7);
                }
                
                .info-badges {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    margin-bottom: 1rem;
                }
                
                .info-badge {
                    padding: 0.5rem 0.75rem;
                    border-radius: 20px;
                    font-size: 0.85rem;
                    font-weight: 600;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .date-info {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem;
                    background: #f8f9fa;
                    border-radius: 8px;
                    margin-top: 0.75rem;
                }
                
                .date-info i {
                    color: #0d6efd;
                    font-size: 1.1rem;
                }
                
                .date-info small {
                    color: #495057;
                    font-weight: 500;
                }
                
                /* Loading Overlay */
                #loadingOverlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 9999;
                    display: none;
                    backdrop-filter: blur(4px);
                }
                
                #loadingOverlay.show {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                
                #loadingOverlay .loading-content {
                    text-align: center;
                    color: white;
                }
                
                #loadingOverlay .spinner-border {
                    width: 3rem;
                    height: 3rem;
                    border-width: 0.3rem;
                    color: white;
                    margin-bottom: 1rem;
                }
                
                #loadingOverlay p {
                    font-size: 1.25rem;
                    font-weight: 500;
                    margin: 0;
                }
                
                /* Responsive */
                @media (max-width: 768px) {
                    .quick-view-modal-header {
                        padding: 1.25rem 1.5rem;
                    }
                    
                    .quick-view-modal-header .modal-title {
                        font-size: 1.1rem;
                    }
                    
                    .quick-view-modal-footer {
                        flex-direction: column;
                        padding: 1rem;
                    }
                    
                    .quick-view-modal-footer .btn {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
            
            {{-- Modal Header --}}
            <div class="modal-header quick-view-modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">
                    <i class="bi bi-eye"></i>
                    <span>Xem nhanh bình luận</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Modal Body --}}
            <div class="modal-body quick-view-modal-body" id="quickViewContent">
                <div class="quick-view-loading">
                    <div class="spinner-wrapper">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="loading-text">Đang tải thông tin bình luận</div>
                    <div class="loading-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="modal-footer quick-view-modal-footer">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="prevReviewBtn" onclick="navigateReview('prev')" style="display: none;">
                        <i class="bi bi-chevron-left"></i>
                        <span>Trước</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="nextReviewBtn" onclick="navigateReview('next')" style="display: none;">
                        <span>Sau</span>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>Đóng</span>
                    </button>
                    <a href="#" id="viewDetailLink" class="btn btn-primary">
                        <i class="bi bi-info-circle"></i>
                        <span>Xem chi tiết</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Lightbox Modal --}}
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxImageTitle">Hình ảnh 1 / 5</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="lightboxImage" src="" alt="Review image" class="img-fluid" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer border-0 justify-content-between">
                <button type="button" class="btn btn-outline-light" id="prevImageBtn" onclick="changeLightboxImage(-1)">
                    <i class="bi bi-chevron-left"></i> Trước
                </button>
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-outline-light" id="nextImageBtn" onclick="changeLightboxImage(1)">
                    Sau <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="loading-content">
            <div class="spinner-border text-light mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="fs-5">Đang xử lý...</p>
        </div>
    </div>
</div>

