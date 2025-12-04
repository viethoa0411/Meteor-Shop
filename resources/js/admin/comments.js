/**
 * Helpers & shared logic for admin comments pages:
 * - index (list + batch actions + quick view)
 * - pending
 * - reported
 *
 * File được load trên layout admin, nhưng mọi function đều tự kiểm tra DOM trước khi chạy
 * để tránh lỗi ở các trang không liên quan.
 */

const AdminComments = (() => {
    const state = {
        selectedReviews: new Set(),
    };

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : window.csrfToken;

    function getSwal() {
        if (window.Swal) {
            return window.Swal;
        }
        console.warn('SweetAlert2 (Swal) is not available; falling back to alert().');
        return null;
    }

    function showToast(type, message, title = '') {
        const SwalLib = getSwal();
        if (!SwalLib) {
            alert(message);
            return;
        }

        const Toast = SwalLib.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: toast => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });

        const icons = { success: 'success', error: 'error', warning: 'warning', info: 'info' };

        Toast.fire({
            icon: icons[type] || 'info',
            title: title || message,
            text: title ? message : '',
        });
    }

    function showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('d-none');
            return;
        }
        // fallback: tạo overlay tạm
        const fallback = document.createElement('div');
        fallback.id = 'loadingOverlay';
        fallback.className =
            'position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center';
        fallback.style.zIndex = '9999';
        fallback.innerHTML =
            '<div class="text-center text-white"><div class="spinner-border text-light mb-3" style="width:3rem;height:3rem;"></div><p class="fs-5">Đang xử lý...</p></div>';
        document.body.appendChild(fallback);
    }

    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('d-none');
        }
    }

    /** Quick view (sử dụng cho index + pending + reported) */
    function quickView(reviewId) {
        const id = parseInt(reviewId, 10);
        if (!id || Number.isNaN(id)) return;

        const modalEl = document.getElementById('quickViewModal');
        const content = document.getElementById('quickViewContent');
        const detailLink = document.getElementById('viewDetailLink');
        if (!modalEl || !content) return;

        if (detailLink) {
            detailLink.href = `/admin/comments/${id}`;
        }

        const modal = window.bootstrap ? new window.bootstrap.Modal(modalEl) : null;
        if (modal) {
            content.innerHTML =
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
            modal.show();
        }

        fetch(`/admin/comments/${id}/quick-view`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(res => res.json())
            .then(data => {
                content.innerHTML =
                    data && data.html
                        ? data.html
                        : '<div class="alert alert-danger">Không thể tải dữ liệu</div>';
            })
            .catch(() => {
                content.innerHTML =
                    '<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
    }

    /** Hành động đơn lẻ: approve / reject / hide / show / delete */
    function postAction(url, options = {}) {
        const { confirmConfig, onSuccess } = options;
        const doRequest = () => {
            showLoading();
            fetch(url, {
                method: options.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: options.body ? JSON.stringify(options.body) : null,
            })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message || 'Thao tác thành công');
                        if (onSuccess) onSuccess(data);
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(() => {
                    hideLoading();
                    showToast('error', 'Có lỗi xảy ra');
                });
        };

        if (confirmConfig) {
            const SwalLib = getSwal();
            if (!SwalLib) {
                if (window.confirm(confirmConfig.text || 'Xác nhận thao tác?')) {
                    doRequest();
                }
                return;
            }

            SwalLib.fire(confirmConfig).then(result => {
                if (result.isConfirmed) doRequest();
            });
        } else {
            doRequest();
        }
    }

    function approveReview(id) {
        postAction(`/admin/comments/${id}/approve`, {
            confirmConfig: {
                title: 'Xác nhận',
                text: 'Bạn có chắc muốn phê duyệt bình luận này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Phê duyệt',
                cancelButtonText: 'Hủy',
            },
            onSuccess: () => {
                const row = document.querySelector(`tr[data-review-id="${id}"]`);
                if (row) row.remove();
            },
        });
    }

    function rejectReview(id) {
        postAction(`/admin/comments/${id}/reject`, {
            confirmConfig: {
                title: 'Xác nhận',
                text: 'Bạn có chắc muốn từ chối bình luận này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Từ chối',
                cancelButtonText: 'Hủy',
            },
            onSuccess: () => {
                const row = document.querySelector(`tr[data-review-id="${id}"]`);
                if (row) row.remove();
            },
        });
    }

    function hideReview(id) {
        postAction(`/admin/comments/${id}/hide`, {
            confirmConfig: {
                title: 'Xác nhận',
                text: 'Ẩn bình luận này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ẩn',
                cancelButtonText: 'Hủy',
            },
            onSuccess: () => {
                const row = document.querySelector(`tr[data-review-id="${id}"]`);
                if (row) row.remove();
            },
        });
    }

    function showReview(id) {
        postAction(`/admin/comments/${id}/show`, {
            onSuccess: () => {
                window.location.reload();
            },
        });
    }

    function deleteReview(id) {
        postAction(`/admin/comments/${id}`, {
            method: 'DELETE',
            confirmConfig: {
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc muốn xóa vĩnh viễn bình luận này? Hành động này không thể hoàn tác!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            },
            onSuccess: () => {
                const row = document.querySelector(`tr[data-review-id="${id}"]`);
                if (row) row.remove();
            },
        });
    }

    /** Batch actions trên trang index */
    function initIndexPage() {
        const table = document.querySelector('table');
        if (!table || !document.getElementById('batchActionsBar')) return;

        const selectedCountEl = document.getElementById('selectedCount');
        const selectAll = document.getElementById('selectAll');

        function updateSelectedCount() {
            const count = state.selectedReviews.size;
            if (selectedCountEl) selectedCountEl.textContent = String(count);
            const bar = document.getElementById('batchActionsBar');
            if (bar) bar.style.display = count > 0 ? 'block' : 'none';
        }

        selectAll?.addEventListener('change', function () {
            const checked = this.checked;
            document.querySelectorAll('.review-checkbox').forEach(cb => {
                cb.checked = checked;
                if (checked) {
                    state.selectedReviews.add(cb.value);
                } else {
                    state.selectedReviews.delete(cb.value);
                }
            });
            updateSelectedCount();
        });

        document.querySelectorAll('.review-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    state.selectedReviews.add(this.value);
                } else {
                    state.selectedReviews.delete(this.value);
                }
                updateSelectedCount();
                if (selectAll) {
                    const allChecked =
                        document.querySelectorAll('.review-checkbox:checked').length ===
                        document.querySelectorAll('.review-checkbox').length;
                    selectAll.checked = allChecked;
                }
            });
        });

        // Batch action buttons
        const batchAction = action => {
            const ids = Array.from(state.selectedReviews);
            if (!ids.length) {
                showToast('warning', 'Vui lòng chọn ít nhất một bình luận');
                return;
            }
            const configMap = {
                approve: {
                    title: 'Phê duyệt',
                    text: `Phê duyệt ${ids.length} bình luận?`,
                    endpoint: 'bulk-approve',
                },
                reject: {
                    title: 'Từ chối',
                    text: `Từ chối ${ids.length} bình luận?`,
                    endpoint: 'bulk-reject',
                },
                hide: {
                    title: 'Ẩn',
                    text: `Ẩn ${ids.length} bình luận?`,
                    endpoint: 'bulk-hide',
                },
                delete: {
                    title: 'Xóa',
                    text: `Xóa vĩnh viễn ${ids.length} bình luận?`,
                    endpoint: 'bulk-delete',
                    icon: 'error',
                },
            };
            const cfg = configMap[action];
            if (!cfg) return;

            postAction(`/admin/comments/${cfg.endpoint}`, {
                body: { ids },
                confirmConfig: {
                    title: cfg.title,
                    text: cfg.text,
                    icon: cfg.icon || 'question',
                    showCancelButton: true,
                    confirmButtonColor: action === 'delete' ? '#ef4444' : '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: cfg.title,
                    cancelButtonText: 'Hủy',
                },
                onSuccess: () => {
                    if (action === 'delete') {
                        ids.forEach(id => {
                            document
                                .querySelector(`tr[data-review-id="${id}"]`)
                                ?.remove();
                        });
                    } else {
                        window.location.reload();
                    }
                },
            });
        };

        // expose cho onclick trong blade (nếu còn dùng)
        window.AdminComments = {
            ...(window.AdminComments || {}),
            batchAction,
        };
    }

    /** Khởi tạo quick-view click handler cho tất cả trang comments */
    function initQuickViewDelegation() {
        document.addEventListener('click', e => {
            const trigger = e.target.closest('[data-quick-view-id]');
            if (!trigger) return;
            const id = trigger.getAttribute('data-quick-view-id');
            if (!id) return;
            e.preventDefault();
            e.stopPropagation();
            quickView(id);
        });
    }

    function init() {
        if (!document.querySelector('body')) return;
        initQuickViewDelegation();
        initIndexPage();

        // Gắn các hàm lên window để tương thích với các Blade cũ
        window.AdminComments = {
            ...(window.AdminComments || {}),
            showToast,
            showLoading,
            hideLoading,
            quickView,
            approveReview,
            rejectReview,
            hideReview,
            showReview,
            deleteReview,
        };

        // Giữ alias cũ (để không phải sửa toàn bộ onClick trong Blade cùng lúc)
        window.showToast = window.showToast || showToast;
        window.showLoading = window.showLoading || showLoading;
        window.hideLoading = window.hideLoading || hideLoading;
        window.quickView = window.quickView || quickView;
        window.approveReview = window.approveReview || approveReview;
        window.rejectReview = window.rejectReview || rejectReview;
        window.hideReview = window.hideReview || hideReview;
        window.showReview = window.showReview || showReview;
        window.deleteReview = window.deleteReview || deleteReview;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        init,
    };
})();

export default AdminComments;




