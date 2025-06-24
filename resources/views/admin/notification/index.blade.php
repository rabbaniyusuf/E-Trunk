@extends('layouts.main')
@section('title', 'Notifikasi - E-TRANK')

@push('styles')
    <style>
        .notification-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .notification-header {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .notification-item {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            cursor: pointer;
        }

        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .notification-item.unread {
            border-left-color: var(--primary-color);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 8px;
            height: 8px;
            background: var(--primary-color);
            border-radius: 50%;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .notification-icon.approval {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .notification-icon.warning {
            background-color: #fef3c7;
            color: #d97706;
        }

        .notification-icon.success {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .notification-icon.info {
            background-color: #e0f2fe;
            color: #0891b2;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .notification-message {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 0.75rem;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-approve {
            background-color: var(--success-color);
            color: white;
        }

        .btn-approve:hover:not(:disabled) {
            background-color: #059669;
        }

        .btn-reject {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-reject:hover:not(:disabled) {
            background-color: #dc2626;
        }

        .btn-mark-read {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-mark-read:hover:not(:disabled) {
            background-color: #475569;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        /* Mobile Responsive */
        @media (max-width: 575.98px) {
            .notification-header {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .notification-item {
                padding: 1rem;
            }

            .notification-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                margin-right: 0.75rem;
            }

            .notification-title {
                font-size: 0.9rem;
            }

            .notification-message {
                font-size: 0.85rem;
            }

            .notification-meta {
                flex-direction: column;
                align-items: stretch;
                gap: 0.25rem;
            }

            .notification-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .notification-actions {
                flex-wrap: wrap;
            }

            .btn-action {
                flex: 1;
                min-width: 120px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3">
        <div class="notification-container">
            <!-- Header -->
            <div class="notification-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">
                            <i class="bi bi-bell me-2"></i>Notifikasi
                        </h4>
                        <p class="text-muted mb-0">
                            Kelola semua notifikasi dan permintaan yang masuk
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @if ($unreadCount > 0)
                            <button type="button" class="btn btn-outline-primary btn-sm" id="markAllRead">
                                <i class="bi bi-check-all me-1"></i>
                                Tandai Semua Dibaca ({{ $unreadCount }})
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            @if ($notifications->count() > 0)
                @foreach ($notifications as $notification)
                    <div class="notification-item {{ !$notification->isRead() ? 'unread' : '' }}"
                        data-notification-id="{{ $notification->id }}">
                        <div class="d-flex">
                            <div class="notification-icon {{ getNotificationIconClass($notification->type) }}">
                                <i class="bi {{ getNotificationIcon($notification->type) }}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">
                                    {{ $notification->title }}
                                </div>
                                <div class="notification-message">
                                    {{ $notification->message }}
                                </div>
                                <div class="notification-meta">
                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if ($notification->isRead())
                                        <span class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>Dibaca
                                        </span>
                                    @else
                                        <span class="text-primary">
                                            <i class="bi bi-circle me-1"></i>Belum dibaca
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons for specific notification types -->
                                @if ($notification->type === 'point_exchange_request' && !$notification->isRead())
                                    <div class="notification-actions">
                                        <button type="button" class="btn-action btn-approve" data-action="approve"
                                            data-id="{{ $notification->data['request_id'] ?? '' }}">
                                            <i class="bi bi-check-lg me-1"></i>Setujui
                                        </button>
                                        <button type="button" class="btn-action btn-reject" data-action="reject"
                                            data-id="{{ $notification->data['request_id'] ?? '' }}">
                                            <i class="bi bi-x-lg me-1"></i>Tolak
                                        </button>
                                    </div>
                                @elseif(!$notification->isRead())
                                    <div class="notification-actions">
                                        <button type="button" class="btn-action btn-mark-read" data-action="mark-read"
                                            data-id="{{ $notification->id }}">
                                            <i class="bi bi-check me-1"></i>Tandai Dibaca
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-bell-slash"></i>
                    <h5>Tidak ada notifikasi</h5>
                    <p>Semua notifikasi akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mark all notifications as read
            const markAllReadBtn = document.getElementById('markAllRead');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    if (confirm('Tandai semua notifikasi sebagai dibaca?')) {
                        markAllAsRead();
                    }
                });
            }

            // Handle notification actions
            document.querySelectorAll('.btn-action').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const id = this.getAttribute('data-id');
                    const notificationItem = this.closest('.notification-item');
                    const notificationId = notificationItem.getAttribute('data-notification-id');

                    // Disable button to prevent double clicks
                    disableButton(this);

                    if (action === 'mark-read') {
                        markAsRead(notificationId, notificationItem, this);
                    } else if (action === 'approve' || action === 'reject') {
                        handleApprovalAction(action, id, notificationId, notificationItem, this);
                    }
                });
            });

            function markAllAsRead() {
                showLoading('Menandai semua notifikasi...');

                fetch('/admin/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken()
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast(data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
                    });
            }

            function markAsRead(notificationId, notificationItem, button) {
                fetch(`/admin/notifications/${notificationId}/read`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken()
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI
                            updateNotificationUI(notificationItem);
                            updateUnreadCount();
                            showToast(data.message, 'success');
                        } else {
                            showToast(data.message || 'Terjadi kesalahan', 'error');
                            enableButton(button, 'mark-read');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
                        enableButton(button, 'mark-read');
                    });
            }

            function handleApprovalAction(action, requestId, notificationId, notificationItem, button) {
                const endpoint = action === 'approve' ? '/admin/approve-request' : '/admin/reject-request';

                fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRFToken()
                        },
                        body: JSON.stringify({
                            request_id: requestId,
                            notification_id: notificationId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update notification UI
                            updateNotificationUI(notificationItem);
                            updateUnreadCount();
                            showToast(data.message || (action === 'approve' ? 'Permintaan disetujui' :
                                'Permintaan ditolak'), 'success');
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
                        enableApprovalButtons(notificationItem);
                    });
            }

            function updateNotificationUI(notificationItem) {
                // Remove unread class
                notificationItem.classList.remove('unread');

                // Update status
                const metaElement = notificationItem.querySelector('.notification-meta span:last-child');
                if (metaElement) {
                    metaElement.innerHTML = '<i class="bi bi-check-circle me-1"></i>Dibaca';
                    metaElement.className = 'text-success';
                }

                // Remove action buttons
                const actionsElement = notificationItem.querySelector('.notification-actions');
                if (actionsElement) {
                    actionsElement.remove();
                }
            }

            function updateUnreadCount() {
                fetch('/admin/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const badges = document.querySelectorAll('.notification-badge');
                            badges.forEach(badge => {
                                if (data.count > 0) {
                                    badge.textContent = data.count > 9 ? '9+' : data.count;
                                    badge.style.display = 'inline';
                                } else {
                                    badge.style.display = 'none';
                                }
                            });

                            // Update mark all button
                            const markAllBtn = document.getElementById('markAllRead');
                            if (markAllBtn) {
                                if (data.count > 0) {
                                    markAllBtn.innerHTML =
                                        `<i class="bi bi-check-all me-1"></i>Tandai Semua Dibaca (${data.count})`;
                                } else {
                                    markAllBtn.style.display = 'none';
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error updating unread count:', error);
                    });
            }

            function disableButton(button) {
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
            }

            function enableButton(button, action) {
                button.disabled = false;
                const buttonTexts = {
                    'mark-read': '<i class="bi bi-check me-1"></i>Tandai Dibaca',
                    'approve': '<i class="bi bi-check-lg me-1"></i>Setujui',
                    'reject': '<i class="bi bi-x-lg me-1"></i>Tolak'
                };
                button.innerHTML = buttonTexts[action] || 'Action';
            }

            function enableApprovalButtons(notificationItem) {
                const approveBtn = notificationItem.querySelector('[data-action="approve"]');
                const rejectBtn = notificationItem.querySelector('[data-action="reject"]');

                if (approveBtn) enableButton(approveBtn, 'approve');
                if (rejectBtn) enableButton(rejectBtn, 'reject');
            }

            function getCSRFToken() {
                const token = document.querySelector('meta[name="csrf-token"]');
                return token ? token.getAttribute('content') : '';
            }

            function showToast(message, type = 'info') {
                // Remove existing toasts
                document.querySelectorAll('.toast-notification').forEach(toast => {
                    toast.remove();
                });

                const toast = document.createElement('div');
                toast.className =
                    `toast-notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

                document.body.appendChild(toast);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }

            function showLoading(message = 'Memproses...') {
                const loading = document.createElement('div');
                loading.id = 'loading-overlay';
                loading.className =
                    'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
                loading.style.cssText = 'background: rgba(0,0,0,0.5); z-index: 9999;';
                loading.innerHTML = `
            <div class="text-center text-white">
                <div class="spinner-border mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>${message}</div>
            </div>
        `;
                document.body.appendChild(loading);
            }

            function hideLoading() {
                const loading = document.getElementById('loading-overlay');
                if (loading) {
                    loading.remove();
                }
            }
        });
    </script>
@endpush

@php
    // Helper functions that would typically be in a Helper class or Service
    function getNotificationIconClass($type)
    {
        return match ($type) {
            'point_exchange_request' => 'approval',
            'warning' => 'warning',
            'success' => 'success',
            default => 'info',
        };
    }

    function getNotificationIcon($type)
    {
        return match ($type) {
            'point_exchange_request' => 'bi-currency-exchange',
            'warning' => 'bi-exclamation-triangle',
            'success' => 'bi-check-circle',
            'info' => 'bi-info-circle',
            default => 'bi-bell',
        };
    }
@endphp
