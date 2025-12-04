/**
 * Helpers & shared logic for admin comments pages (index, pending, reported)
 * Dùng dạng script thường, không cần Vite/ES module.
 */

(function () {
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

    function showToast(type, message, title) {
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
                toast.addEventListener('mouseenter', SwalLib.stopTimer);
                toast.addEventListener('mouseleave', SwalLib.resumeTimer);
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

    function quickView(reviewId) {
        const id = parseInt(reviewId, 10);
        if (!id || Number.isNaN(id)) return;

        const modalEl = document.getElementById('quickViewModal');
        const content = document.getElementById('quickViewContent');
        const detailLink = document.getElementById('viewDetailLink');
        if (!modalEl || !content) return;

        if (detailLink) {
            detailLink.href = '/admin/comments/' + id;
        }

        const bootstrapLib = window.bootstrap || window.Bootstrap;
        const modal = bootstrapLib ? new bootstrapLib.Modal(modalEl) : null;
        if (modal) {
            content.innerHTML =
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
            modal.show();
        }

        fetch('/admin/comments/' + id + '/quick-view', {
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
            .catch(function () {
                content.innerHTML =
                    '<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu</div>';
            });
    }

    function postAction(url, options) {
        options = options || {};
        var confirmConfig = options.confirmConfig;
        var onSuccess = options.onSuccess;

        function doRequest() {
            showLoading();
            fetch(url, {
                method: options.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: options.body ? JSON.stringify(options.body) : null,
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (data) {
                    hideLoading();
                    if (data.status === 'success') {
                        showToast('success', data.message || 'Thao tác thành công');
                        if (onSuccess) onSuccess(data);
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(function () {
                    hideLoading();
                    showToast('error', 'Có lỗi xảy ra');
                });
        }

        if (confirmConfig) {
            const SwalLib = getSwal();
            if (!SwalLib) {
                if (window.confirm(confirmConfig.text || 'Xác nhận thao tác?')) {
                    doRequest();
                }
                return;
            }

            SwalLib.fire(confirmConfig).then(function (result) {
                if (result.isConfirmed) doRequest();
            });
        } else {
            doRequest();
        }
    }

    function approveReview(id) {
        postAction('/admin/comments/' + id + '/approve', {
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
            onSuccess: function () {
                var row = document.querySelector('tr[data-review-id="' + id + '"]');
                if (row) row.remove();
            },
        });
    }

    function rejectReview(id) {
        postAction('/admin/comments/' + id + '/reject', {
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
            onSuccess: function () {
                var row = document.querySelector('tr[data-review-id="' + id + '"]');
                if (row) row.remove();
            },
        });
    }

    function hideReview(id) {
        postAction('/admin/comments/' + id + '/hide', {
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
            onSuccess: function () {
                var row = document.querySelector('tr[data-review-id="' + id + '"]');
                if (row) row.remove();
            },
        });
    }

    function showReview(id) {
        postAction('/admin/comments/' + id + '/show', {
            onSuccess: function () {
                window.location.reload();
            },
        });
    }

    function deleteReview(id) {
        postAction('/admin/comments/' + id, {
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
            onSuccess: function () {
                var row = document.querySelector('tr[data-review-id="' + id + '"]');
                if (row) row.remove();
            },
        });
    }

    function initIndexPage() {
        var table = document.querySelector('table');
        if (!table || !document.getElementById('batchActionsBar')) return;

        var selectedCountEl = document.getElementById('selectedCount');
        var selectAll = document.getElementById('selectAll');

        function updateSelectedCount() {
            var count = state.selectedReviews.size;
            if (selectedCountEl) selectedCountEl.textContent = String(count);
            var bar = document.getElementById('batchActionsBar');
            if (bar) bar.style.display = count > 0 ? 'block' : 'none';
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                var checked = this.checked;
                document.querySelectorAll('.review-checkbox').forEach(function (cb) {
                    cb.checked = checked;
                    if (checked) {
                        state.selectedReviews.add(cb.value);
                    } else {
                        state.selectedReviews.delete(cb.value);
                    }
                });
                updateSelectedCount();
            });
        }

        document.querySelectorAll('.review-checkbox').forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    state.selectedReviews.add(this.value);
                } else {
                    state.selectedReviews.delete(this.value);
                }
                updateSelectedCount();
                if (selectAll) {
                    var allChecked =
                        document.querySelectorAll('.review-checkbox:checked').length ===
                        document.querySelectorAll('.review-checkbox').length;
                    selectAll.checked = allChecked;
                }
            });
        });

        function batchAction(action) {
            var ids = Array.from(state.selectedReviews);
            if (!ids.length) {
                showToast('warning', 'Vui lòng chọn ít nhất một bình luận');
                return;
            }
            var actions = {
                approve: {
                    title: 'Phê duyệt',
                    text: 'Phê duyệt ' + ids.length + ' bình luận?',
                    endpoint: 'bulk-approve',
                },
                reject: {
                    title: 'Từ chối',
                    text: 'Từ chối ' + ids.length + ' bình luận?',
                    endpoint: 'bulk-reject',
                },
                hide: {
                    title: 'Ẩn',
                    text: 'Ẩn ' + ids.length + ' bình luận?',
                    endpoint: 'bulk-hide',
                },
                delete: {
                    title: 'Xóa',
                    text:
                        'Xóa vĩnh viễn ' +
                        ids.length +
                        ' bình luận? Hành động này không thể hoàn tác!',
                    endpoint: 'bulk-delete',
                    icon: 'error',
                },
            };
            var cfg = actions[action];
            if (!cfg) return;

            postAction('/admin/comments/' + cfg.endpoint, {
                body: { ids: ids },
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
                onSuccess: function () {
                    if (action === 'delete') {
                        ids.forEach(function (id) {
                            var row = document.querySelector('tr[data-review-id="' + id + '"]');
                            if (row) row.remove();
                        });
                    } else {
                        window.location.reload();
                    }
                },
            });
        }

        window.AdminComments = window.AdminComments || {};
        window.AdminComments.batchAction = batchAction;
    }

    function initQuickViewDelegation() {
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-quick-view-id]');
            if (!trigger) return;
            var id = trigger.getAttribute('data-quick-view-id');
            if (!id) return;
            e.preventDefault();
            e.stopPropagation();
            quickView(id);
        });
    }

    function init() {
        initQuickViewDelegation();
        initIndexPage();

        window.AdminComments = window.AdminComments || {};
        window.AdminComments.showToast = showToast;
        window.AdminComments.showLoading = showLoading;
        window.AdminComments.hideLoading = hideLoading;
        window.AdminComments.quickView = quickView;
        window.AdminComments.approveReview = approveReview;
        window.AdminComments.rejectReview = rejectReview;
        window.AdminComments.hideReview = hideReview;
        window.AdminComments.showReview = showReview;
        window.AdminComments.deleteReview = deleteReview;

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
})();



