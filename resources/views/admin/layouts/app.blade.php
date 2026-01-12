<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Meteor Admin</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            transition: background 0.3s, color 0.3s;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Hide scrollbar for all browsers but keep functionality */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: transparent;
        }

        /* Firefox */
        * {
            scrollbar-width: none;
        }

        /* IE and Edge */
        * {
            -ms-overflow-style: none;
        }

        .admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 80px - 60px);
        }

        /* Header Styles */
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .admin-header .dropdown {
            overflow: visible;
            /* allow badge/bubble to overflow */
        }

        .admin-header.scrolled {
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
        }

        .admin-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .admin-logo:hover {
            transform: scale(1.05);
        }

        .admin-header-btn {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: white;
            transition: all 0.25s ease;
            border-radius: 12px;
            padding: 10px 16px;
            overflow: visible;
            /* ensure badge is visible outside button */
            position: relative;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .admin-header-btn.notification-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .admin-header-btn:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
        }

        .admin-header-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) inset;
        }

        .admin-header-btn:focus-visible {
            outline: 2px solid rgba(255, 255, 255, 0.7);
            outline-offset: 2px;
        }

        .admin-user-dropdown {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 12px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .admin-user-dropdown:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Notification badge visibility */
        .notification-badge {
            top: -12px;
            right: -6px;
            left: auto !important;
            transform: none !important;
            z-index: 20;
            min-width: 26px;
            padding: 4px 9px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.5);
            line-height: 1.1;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .notification-badge::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 999px;
            box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.18);
            animation: badgePulse 1.8s ease-out infinite;
            z-index: -1;
        }

        @keyframes badgePulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            70% {
                transform: scale(1.25);
                opacity: 0;
            }

            100% {
                transform: scale(1.25);
                opacity: 0;
            }
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            color: #333;
            flex-shrink: 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
            overflow-x: hidden;
            position: sticky;
            top: 80px;
            transition: all 0.3s ease;
            z-index: 100;
            /* Hide scrollbar but keep scroll functionality */
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        .admin-sidebar::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .admin-sidebar-title {
            padding: 20px 24px;
            font-size: 1.1rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.2);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .admin-sidebar-link {
            color: #4a5568;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        .admin-sidebar-link i {
            width: 24px;
            min-width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .admin-sidebar-link:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            color: #667eea;
            border-left-color: #667eea;
            transform: translateX(5px);
        }

        .admin-sidebar-link:hover i {
            transform: scale(1.2);
        }

        .admin-sidebar-link.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, transparent 100%);
            color: #667eea;
            border-left-color: #667eea;
            font-weight: 600;
        }

        .admin-sidebar-link.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px 0 0 4px;
        }

        .admin-sidebar-badge {
            margin-left: auto;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes badgePulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0);
            }
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 10000;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .toast-notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-content {
            display: flex;
            align-items: center;
            flex: 1;
            font-size: 0.9rem;
            color: #334155;
        }

        .toast-close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 8px;
            margin-left: 12px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            background: #f1f5f9;
            color: #64748b;
        }

        .toast-info {
            border-left-color: #3b82f6;
        }

        .toast-success {
            border-left-color: #10b981;
        }

        .toast-warning {
            border-left-color: #f59e0b;
        }

        .toast-danger {
            border-left-color: #ef4444;
        }

        body.dark .toast-notification {
            background: #1e293b;
            color: #e2e8f0;
        }

        body.dark .toast-close {
            color: #94a3b8;
        }

        body.dark .toast-close:hover {
            background: #334155;
            color: #cbd5e1;
        }

        /* Submenu Styles */
        .admin-dropdown-item {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .admin-dropdown-item>.admin-sidebar-link .bi-chevron-right {
            margin-left: auto;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .admin-dropdown-item.active>.admin-sidebar-link .bi-chevron-right {
            transform: rotate(90deg);
        }

        .admin-submenu {
            max-height: 0;
            overflow: hidden;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            border-left: 3px solid #e2e8f0;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }

        .admin-dropdown-item.active .admin-submenu {
            max-height: 500px;
            opacity: 1;
            display: block;
        }

        .admin-submenu-link {
            padding: 12px 24px 12px 60px;
            color: #64748b;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }

        .admin-submenu-link i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .admin-submenu-link:hover {
            background: rgba(102, 126, 234, 0.08);
            color: #667eea;
            padding-left: 65px;
        }

        .admin-submenu-link.active {
            background: rgba(102, 126, 234, 0.12);
            color: #667eea;
            font-weight: 600;
            border-left: 3px solid #667eea;
        }

        /* Main Content */
        main {
            flex: 1;
            padding: clamp(16px, 2vw, 24px);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            transition: background 0.3s, color 0.3s;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Footer Styles */
        .admin-footer {
            height: 60px;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .admin-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.2), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        .admin-footer-content {
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Notification Dropdown Styles - Professional Design */
        .notification-dropdown {
            border-radius: 12px;
            padding: 0;
            margin-top: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .notification-header {
            padding: 14px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0;
        }

        .notification-header h6 {
            color: white;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .notification-header button {
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }

        .notification-header button:hover {
            color: white;
            transform: translateY(-1px);
        }

        .notification-footer {
            background: #f8f9fa;
            padding: 0;
        }

        .notification-footer a {
            transition: all 0.2s ease;
        }

        .notification-footer a:hover {
            background: #e9ecef;
            color: #667eea !important;
        }

        .notification-item {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            background: white;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        .notification-item.read {
            opacity: 0.65;
            background: #fafbfc;
        }

        .notification-item.read:hover {
            background: #f1f5f9;
        }

        .notification-item .unread-dot {
            position: absolute;
            top: 18px;
            right: 16px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .notification-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .notification-item:hover .notification-icon {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Notification level colors */
        .notification-item[data-level="info"] .notification-icon {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .notification-item[data-level="warning"] .notification-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .notification-item[data-level="danger"] .notification-icon {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }

        .notification-item[data-level="success"] .notification-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        .notification-badge.hidden {
            display: none !important;
        }

        /* Dark mode for notifications */
        body.dark .notification-dropdown {
            background-color: #1e293b;
            border-color: #334155;
        }

        body.dark .notification-item:hover {
            background-color: #334155;
        }

        body.dark .notification-item .text-muted {
            color: #94a3b8 !important;
        }

        body.dark .notification-dropdown .dropdown-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }

        /* DARK MODE */
        body.dark {
            background-color: #1e1e1e;
            color: #e8e8e8;
        }

        /* Dark Mode Styles */
        body.dark {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        body.dark .admin-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        body.dark .admin-sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.5);
        }

        body.dark .admin-sidebar-title {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        body.dark .admin-sidebar-link {
            color: #cbd5e1;
        }

        body.dark .admin-sidebar-link:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.2) 0%, transparent 100%);
            color: #818cf8;
        }

        body.dark .admin-sidebar-link.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.25) 0%, transparent 100%);
            color: #818cf8;
        }

        body.dark .admin-submenu {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-left-color: #334155;
        }

        body.dark .admin-submenu-link {
            color: #94a3b8;
        }

        body.dark .admin-submenu-link:hover {
            background: rgba(102, 126, 234, 0.15);
            color: #818cf8;
        }

        body.dark .admin-submenu-link.active {
            background: rgba(102, 126, 234, 0.2);
            color: #818cf8;
            border-left-color: #818cf8;
        }

        body.dark main {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
            color: #e2e8f0;
        }

        body.dark .admin-footer {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            width: 100%;
            height: calc(100vh - 80px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .mobile-overlay.show {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                position: fixed;
                left: -280px;
                top: 80px;
                height: calc(100vh - 80px);
                z-index: 999;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            }

            .admin-sidebar.mobile-open {
                left: 0;
            }

            .admin-container {
                min-height: calc(100vh - 80px - 60px);
            }

            .admin-sidebar-link {
                padding: 16px 24px;
            }

            .admin-submenu-link {
                padding: 14px 24px 14px 60px;
            }
        }

        @media (max-width: 767.98px) {
            main {
                padding: clamp(8px, 2vw, 16px);
            }
        }

        @media (min-width: 1400px) {
            main {
                padding: clamp(20px, 2.5vw, 28px);
            }
        }

        /* Dark mode helpers for components */
        body.dark .card {
            background-color: #1f1f1f;
            color: #f5f5f5;
            border-color: #2f2f2f;
        }

        body.dark .card-header {
            background-color: #242424 !important;
            border-color: #2f2f2f !important;
            color: #f5f5f5;
        }

        .table tbody tr {
            background-color: #ffffff;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        body.dark .table {
            background-color: #1b1b1b;
            color: #f0f0f0;
        }

        body.dark .table thead,
        body.dark .table-light {
            background-color: #151515 !important;
            color: #ffffff !important;
        }

        body.dark .table thead th {
            border-color: #2f2f2f !important;
            background-color: #151515 !important;
            color: #ffffff !important;
        }

        body.dark .table td,
        body.dark .table th {
            border-color: #2f2f2f !important;
            color: #ffffff !important;
        }


        body.dark .table-bordered> :not(caption)>*>* {
            border-color: #2f2f2f;
        }

        body.dark table.table tbody tr,
        body.dark table.table tbody tr td {
            background-color: #151515 !important;
        }

        body.dark table.table tbody tr:nth-of-type(odd),
        body.dark table.table tbody tr:nth-of-type(odd) td,
        body.dark .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #1c1c1c !important;
        }

        body.dark .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: #1c1c1c !important;
        }

        body.dark .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
        }

        body.dark .list-group-item {
            background-color: #1f1f1f;
            color: #f5f5f5;
            border-color: #2f2f2f;
        }

        body.dark .alert {
            background-color: #2b3b4b;
            color: #f8f9fa;
            border-color: #1f2a35;
        }

        body.dark .form-control,
        body.dark .form-select {
            background-color: #1c1c1c;
            color: #f5f5f5;
            border-color: #444;
        }

        body.dark .form-control::placeholder,
        body.dark .form-select::placeholder {
            color: #d0d0d0;
        }

        body.dark .form-control:focus,
        body.dark .form-select:focus {
            background-color: #1c1c1c;
            color: #fff;
            border-color: #4a90e2;
            box-shadow: none;
        }

        body.dark .bg-light {
            background-color: #1b1b1b !important;
        }

        body.dark .bg-white {
            background-color: #1f1f1f !important;
            color: #f5f5f5 !important;
        }

        body.dark .bg-body {
            background-color: #1f1f1f !important;
            color: #f5f5f5 !important;
        }

        body.dark .border-start,
        body.dark .border-top,
        body.dark .border {
            border-color: #2f2f2f !important;
        }

        body.dark .text-muted {
            color: #b5bfd2 !important;
        }

        body.dark .text-dark {
            color: #ffffff !important;
        }

        body.dark .shadow-sm {
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.65) !important;
        }
    </style>

    @stack('styles')

</head>

<body>

    <!-- Header -->
    <header class="admin-header">
        <nav class="navbar navbar-expand-lg px-4 py-3">
            <div class="container-fluid d-flex align-items-center gap-4">
                <div class="d-flex align-items-center">
                    <!-- Mobile Menu Toggle -->
                    <button class="admin-header-btn d-lg-none me-3" id="mobileMenuToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <a class="admin-logo text-decoration-none" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-meteor me-2"></i>Meteor Admin
                    </a>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Nút Dark/Light -->
                    <button id="themeToggle" class="admin-header-btn" type="button" title="Toggle Theme">
                        <i id="themeIcon" class="bi bi-moon-fill"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="dropdown position-relative">

                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge"
                            id="notificationBadge" style="font-size: 0.65rem; display: none;">
                            0
                        </span>
                        <button class="admin-header-btn position-relative" type="button" id="notificationsBtn"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell-fill"></i>

                        </button>
                        <ul class="dropdown-menu notification-dropdown shadow-lg border-0"
                            id="notificationDropdown"
                            style="min-width: 380px; max-width: 420px; max-height: 500px; overflow-y: auto; padding: 0;">
                            <!-- Header -->
                            <li class="notification-header">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2">
                                    <h6 class="mb-0 fw-bold d-flex align-items-center">
                                        <i class="bi bi-bell-fill me-2"></i>
                                        <span>Thông báo</span>
                                        <span class="badge bg-primary ms-2" id="notificationHeaderCount"
                                            style="display: none; font-size: 0.7rem;">0</span>
                                    </h6>
                                    <button class="btn btn-sm btn-link text-decoration-none p-0 text-muted"
                                        id="markAllReadBtn" style="font-size: 0.8rem; white-space: nowrap;">
                                        <i class="bi bi-check-all me-1"></i>Đánh dấu tất cả
                                    </button>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-0">
                            </li>

                            <!-- Notifications List -->
                            <li id="notificationsList" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-5" id="notificationsLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="text-muted small mt-2">Đang tải thông báo...</div>
                                </div>
                                <div id="notificationsEmpty" class="text-center py-5" style="display: none;">
                                    <i class="bi bi-bell-slash fs-1 text-muted opacity-50"></i>
                                    <div class="text-muted small mt-2">Không có thông báo mới</div>
                                </div>
                            </li>

                            <!-- Footer -->
                            <li>
                                <hr class="dropdown-divider my-0">
                            </li>
                            <li class="notification-footer">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="dropdown-item text-center py-2 text-primary" id="viewAllNotifications">
                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                    <small class="fw-semibold">Xem tất cả thông báo</small>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tài khoản -->
                    <div class="dropdown">
                        <a class="admin-user-dropdown dropdown-toggle text-decoration-none text-white d-flex align-items-center"
                            data-bs-toggle="dropdown" role="button">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0"
                            style="min-width: 200px; margin-top: 10px;">
                            <li>
                                <a href="" class="dropdown-item py-2">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item py-2">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Admin Layout -->
    <div class="admin-container">

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <h5 class="admin-sidebar-title">
                <i class="bi bi-speedometer2 me-2"></i>Quản trị hệ thống
            </h5>

            <a href="{{ route('admin.dashboard') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i> Dashboard
            </a>

            <a href="{{ route('admin.categories.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder-plus"></i> Danh mục
            </a>

            <a href="{{ route('admin.products.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Sản phẩm
            </a>

            <a href="{{ route('admin.wishlist.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.wishlist.*') ? 'active' : '' }}">
                <i class="bi bi-heart-fill"></i> Sản phẩm yêu thích
            </a>

            <a href="{{ route('admin.orders.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i> Đơn hàng
            </a>

            <a href="{{ route('admin.notifications.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i> Thông báo
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                @endphp
                @if ($unreadCount > 0)
                    <span class="admin-sidebar-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.chatbox.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.chatbox.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots-fill"></i> Chatbox
                @php
                    $unreadChatCount = \App\Models\ChatSession::where('unread_count', '>', 0)->count();
                @endphp
                @if ($unreadChatCount > 0)
                    <span class="admin-sidebar-badge">{{ $unreadChatCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.promotions.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                <i class="bi bi-ticket-perforated"></i> Voucher
            </a>


            <a href="{{ route('admin.banners.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <i class="bi bi-image-fill"></i> Banner
            </a>

            <a href="{{ route('admin.contacts.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i> Thiết kế
            </a>

            <a href="{{ route('admin.blogs.list') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i>Bài viết
            </a>

            <a href="{{ route('admin.comments.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text-fill"></i>Bình luận
            </a>

            <!-- Quản lý tài khoản -->
            <div class="admin-dropdown-item {{ request()->routeIs('admin.account.*') ? 'active' : '' }}">
                <a href="#"
                    class="admin-sidebar-link {{ request()->routeIs('admin.account.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Quản lý tài khoản
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="admin-submenu">
                    <a href="{{ route('admin.account.admin.list') }}"
                        class="admin-submenu-link {{ request()->routeIs('admin.account.admin.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i> Quản lý Admin
                    </a>
                    <a href="{{ route('admin.account.users.list') }}"
                        class="admin-submenu-link {{ request()->routeIs('admin.account.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i> Quản lý User
                    </a>
                </div>
            </div>

            @php
                $pendingDeposits = \App\Models\DepositRequest::where('status', 'pending')->count();
                $pendingWithdraws = \App\Models\WithdrawRequest::whereIn('status', ['pending', 'processing'])->count();
                $totalPending = $pendingDeposits + $pendingWithdraws;
            @endphp
            <a href="{{ route('admin.wallet.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.wallet.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Quản lý Ví
                @if ($totalPending > 0)
                    <span class="admin-sidebar-badge">{{ $totalPending > 99 ? '99+' : $totalPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.shipping.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.shipping.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Cài đặt vận chuyển
            </a>

            <a href="{{ route('admin.contact-info.index') }}"
                class="admin-sidebar-link {{ request()->routeIs('admin.contact-info.*') ? 'active' : '' }}">
                <i class="bi bi-headset me-2"></i></i>Liên hệ nhanh
            </a>
        </aside>

        <!-- Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
        <div class="admin-footer-content">
            <i class="bi bi-meteor me-2"></i>
            © 2025 Meteor-Shop — Admin Panel |
            <span class="ms-2">Powered by Laravel & Tailwind CSS</span>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Dark mode
        const toggleBtn = document.getElementById("themeToggle");
        const themeIcon = document.getElementById("themeIcon");

        const setTheme = (isDark) => {
            document.body.classList.toggle("dark", isDark);
            document.documentElement.classList.toggle("dark", isDark);
            themeIcon.classList.toggle("bi-moon-fill", !isDark);
            themeIcon.classList.toggle("bi-sun-fill", isDark);
            localStorage.setItem("theme", isDark ? "dark" : "light");

            window.dispatchEvent(new CustomEvent("theme-changed", {
                detail: {
                    isDark
                }
            }));
        };

        setTheme(localStorage.getItem("theme") === "dark");

        toggleBtn.onclick = () => {
            const isDark = !document.body.classList.contains("dark");
            setTheme(isDark);
        };

        // Header scroll effect
        const adminHeader = document.querySelector('.admin-header');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                adminHeader.classList.add('scrolled');
            } else {
                adminHeader.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });

        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        let mobileOverlay = document.querySelector('.mobile-overlay');

        if (!mobileOverlay) {
            mobileOverlay = document.createElement('div');
            mobileOverlay.className = 'mobile-overlay';
            document.body.appendChild(mobileOverlay);
        }

        function toggleMobileMenu() {
            if (!adminSidebar) return;

            const isOpen = adminSidebar.classList.contains('mobile-open');

            if (isOpen) {
                adminSidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                adminSidebar.classList.add('mobile-open');
                mobileOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMobileMenu();
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', () => {
                toggleMobileMenu();
            });
        }

        // Toggle submenu with smooth animation
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize submenu state based on active route
            const activeDropdown = document.querySelector('.admin-dropdown-item.active');
            if (activeDropdown) {
                const submenu = activeDropdown.querySelector('.admin-submenu');
                if (submenu) {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                }
            }

            // Handle dropdown toggle
            document.querySelectorAll('.admin-dropdown-item > .admin-sidebar-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const parent = this.closest('.admin-dropdown-item');
                    if (!parent) return;

                    const isActive = parent.classList.contains('active');
                    const submenu = parent.querySelector('.admin-submenu');

                    // Close all other dropdowns
                    document.querySelectorAll('.admin-dropdown-item').forEach(dropdown => {
                        if (dropdown !== parent) {
                            dropdown.classList.remove('active');
                            const otherSubmenu = dropdown.querySelector('.admin-submenu');
                            if (otherSubmenu) {
                                otherSubmenu.style.maxHeight = '0';
                            }
                        }
                    });

                    // Toggle current dropdown
                    if (isActive) {
                        parent.classList.remove('active');
                        if (submenu) {
                            submenu.style.maxHeight = '0';
                        }
                    } else {
                        parent.classList.add('active');
                        if (submenu) {
                            submenu.style.maxHeight = submenu.scrollHeight + 'px';
                        }
                    }
                });
            });

            // Add smooth scroll to sidebar links
            document.querySelectorAll(
                    '.admin-sidebar-link:not(.admin-dropdown-item > .admin-sidebar-link), .admin-submenu-link')
                .forEach(link => {
                    link.addEventListener('click', function() {
                        // Close mobile menu if open
                        if (window.innerWidth <= 768 && adminSidebar) {
                            toggleMobileMenu();
                        }
                    });
                });

            // Add ripple effect to buttons
            document.querySelectorAll('.admin-header-btn, .admin-user-dropdown').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.3);
                        left: ${x}px;
                        top: ${y}px;
                        pointer-events: none;
                        animation: ripple 0.6s ease-out;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Close mobile menu on window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth > 768 && adminSidebar && adminSidebar.classList.contains(
                        'mobile-open')) {
                    toggleMobileMenu();
                }
            }, 250);
        });

        // Close mobile menu on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && adminSidebar && adminSidebar.classList.contains('mobile-open')) {
                toggleMobileMenu();
            }
        });

        // Notifications functionality - Professional Implementation
        class NotificationManager {
            constructor() {
                this.elements = {
                    btn: document.getElementById('notificationsBtn'),
                    dropdown: document.getElementById('notificationDropdown'),
                    badge: document.getElementById('notificationBadge'),
                    list: document.getElementById('notificationsList'),
                    loading: document.getElementById('notificationsLoading'),
                    empty: document.getElementById('notificationsEmpty'),
                    markAllReadBtn: document.getElementById('markAllReadBtn'),
                    viewAllBtn: document.getElementById('viewAllNotifications')
                };

                this.state = {
                    notifications: [],
                    unreadCount: 0, 
                    isLoading: false,
                    lastFetchTime: null,
                    refreshInterval: null,
                    cacheKey: 'admin_notifications_cache',
                    cacheExpiry: 30000 // 30 seconds
                };

                this.init();
            }

            init() {
                if (!this.elements.btn) return;

                // Load badge count immediately on page load
                this.loadBadgeCount();

                // Load full notifications when dropdown opens
                this.elements.btn.addEventListener('shown.bs.dropdown', () => {
                    this.loadNotifications(true);
                });

                // Mark all as read
                if (this.elements.markAllReadBtn) {
                    this.elements.markAllReadBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.markAllAsRead();
                    });
                }

                // View all notifications
                if (this.elements.viewAllBtn) {
                    this.elements.viewAllBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        window.location.href = '{{ route('admin.notifications.index') }}';
                    });
                }

                // Auto-refresh with debounce
                this.startAutoRefresh();

                // Handle click outside
                this.handleClickOutside();
            }

            // Load only badge count (lightweight)
            async loadBadgeCount() {
                try {
                    const cached = this.getCachedData();
                    if (cached && cached.badgeCount !== undefined) {
                        this.updateBadge(cached.badgeCount);
                    }

                    const response = await fetch('{{ route('admin.dashboard.notifications') }}?badge_only=1', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();

                    if (data.success) {
                        const count = data.totalUnread || 0;
                        this.updateBadge(count);
                        this.cacheBadgeCount(count);
                    }
                } catch (error) {
                    console.warn('Error loading badge count:', error);
                    // Don't show error to user for badge count
                }
            }

            // Load full notifications
            async loadNotifications(showLoading = false) {
                if (this.state.isLoading) return;

                // Check cache first
                const cached = this.getCachedData();
                if (cached && cached.notifications && !this.isCacheExpired(cached.timestamp)) {
                    this.state.notifications = cached.notifications;
                    this.state.unreadCount = cached.unreadCount;
                    this.renderNotifications();
                    this.updateBadge(cached.unreadCount);

                    // Still fetch in background for fresh data
                    this.loadNotificationsFromAPI(showLoading, false);
                    return;
                }

                await this.loadNotificationsFromAPI(showLoading, true);
            }

            async loadNotificationsFromAPI(showLoading = false, updateUI = true) {
                this.state.isLoading = true;

                if (showLoading && this.elements.loading) {
                    this.elements.loading.style.display = 'block';
                }

                try {
                    const response = await fetch('{{ route('admin.dashboard.notifications') }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Cache-Control': 'no-cache'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.state.notifications = data.notifications || [];
                        this.state.unreadCount = data.totalUnread || 0;
                        this.state.lastFetchTime = Date.now();

                        // Cache the data
                        this.cacheData(data.notifications, this.state.unreadCount);

                        if (updateUI) {
                            this.renderNotifications();
                            this.updateBadge(this.state.unreadCount);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load notifications');
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                    if (updateUI) {
                        this.showError('Không thể tải thông báo. Vui lòng thử lại.');
                    }
                } finally {
                    this.state.isLoading = false;
                    if (this.elements.loading) {
                        this.elements.loading.style.display = 'none';
                    }
                }
            }

            renderNotifications() {
                if (this.elements.loading) {
                    this.elements.loading.style.display = 'none';
                }

                if (this.state.notifications.length === 0) {
                    this.showEmptyState();
                    return;
                }

                if (this.elements.empty) {
                    this.elements.empty.style.display = 'none';
                }

                this.elements.list.innerHTML = this.state.notifications.map(notif => this.renderNotificationItem(notif))
                    .join('');

                // Add click handlers with event delegation
                this.elements.list.addEventListener('click', (e) => {
                    const item = e.target.closest('.notification-item');
                    if (item && !item.classList.contains('read')) {
                        this.handleNotificationClick(item);
                    }
                });
            }

            renderNotificationItem(notif) {
                const unreadClass = notif.unread ? '' : 'read';
                const level = notif.level || 'info';
                const unreadDot = notif.unread ?
                    '<span class="unread-dot"></span>' :
                    '';

                return `
                    <div class="notification-item ${unreadClass}"
                         data-notification-id="${notif.id}"
                         data-type="${notif.type}"
                         data-level="${level}"
                         data-link="${this.escapeHtml(notif.link || '#')}">
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3">
                                <i class="bi ${notif.icon} ${notif.iconColor} fs-5"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="fw-semibold small mb-1">${this.escapeHtml(notif.title)}</div>
                                <div class="text-muted small mb-1" style="line-height: 1.4;">${this.escapeHtml(notif.message)}</div>
                                <div class="text-muted" style="font-size: 0.7rem; opacity: 0.8;">
                                    <i class="bi bi-clock me-1"></i>${this.escapeHtml(notif.time)}
                                </div>
                            </div>
                            ${unreadDot}
                        </div>
                    </div>
                `;
            }

            async handleNotificationClick(item) {
                const link = item.getAttribute('data-link');
                const notificationId = item.getAttribute('data-notification-id');

                // Mark as read on server (only if it's a database notification ID, not dynamic ID)
                // Dynamic notifications (order_123, review_456) don't exist in database
                if (notificationId && !item.classList.contains('read')) {
                    // Check if it's a numeric ID (database notification) or string ID (dynamic notification)
                    const isNumericId = /^\d+$/.test(notificationId);

                    if (isNumericId) {
                        try {
                            await fetch(`{{ route('admin.notifications.read', ':id') }}`.replace(':id',
                                notificationId), {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        ?.getAttribute('content') || ''
                                }
                            });
                        } catch (error) {
                            console.warn('Error marking notification as read:', error);
                        }
                    }
                }

                // Mark as read in UI (works for both database and dynamic notifications)
                this.markAsRead(item, notificationId);

                // Navigate if link is valid
                if (link && link !== '#') {
                    // Small delay for visual feedback
                    setTimeout(() => {
                        window.location.href = link;
                    }, 150);
                }
            }

            markAsRead(item, notificationId = null) {
                if (item.classList.contains('read')) return;

                item.classList.add('read');
                const unreadDot = item.querySelector('.unread-dot');
                if (unreadDot) {
                    unreadDot.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => unreadDot.remove(), 300);
                }

                // Update count
                this.state.unreadCount = Math.max(0, this.state.unreadCount - 1);
                this.updateBadge(this.state.unreadCount);

                // Update in notifications array
                if (notificationId) {
                    const notif = this.state.notifications.find(n => n.id === notificationId);
                    if (notif) {
                        notif.unread = false;
                    }
                }
            }

            async markAllAsRead() {
                try {
                    const response = await fetch('{{ route('admin.notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        }
                    });

                    if (response.ok) {
                        document.querySelectorAll('.notification-item:not(.read)').forEach(item => {
                            const notificationId = item.getAttribute('data-notification-id');
                            this.markAsRead(item, notificationId);
                        });
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                    // Fallback to client-side only
                    document.querySelectorAll('.notification-item:not(.read)').forEach(item => {
                        const notificationId = item.getAttribute('data-notification-id');
                        this.markAsRead(item, notificationId);
                    });
                }
            }

            updateBadge(count) {
                if (!this.elements.badge) return;

                const previousCount = parseInt(this.elements.badge.textContent) || 0;
                const headerCount = document.getElementById('notificationHeaderCount');

                if (count > 0) {
                    const displayCount = count > 99 ? '99+' : count;
                    this.elements.badge.textContent = displayCount;
                    this.elements.badge.style.display = 'inline-block';

                    // Update header count
                    if (headerCount) {
                        headerCount.textContent = displayCount;
                        headerCount.style.display = 'inline-block';
                    }

                    // Animation when count changes
                    if (previousCount !== count && previousCount > 0) {
                        this.elements.badge.style.animation = 'badgePulse 0.5s ease-out';
                        setTimeout(() => {
                            this.elements.badge.style.animation = '';
                        }, 500);
                    }
                } else {
                    this.elements.badge.style.display = 'none';
                    if (headerCount) {
                        headerCount.style.display = 'none';
                    }
                }
            }

            showEmptyState() {
                if (this.elements.loading) {
                    this.elements.loading.style.display = 'none';
                }
                if (this.elements.empty) {
                    this.elements.empty.style.display = 'block';
                }
            }

            showError(message) {
                if (this.elements.loading) {
                    this.elements.loading.style.display = 'none';
                }
                if (this.elements.list) {
                    this.elements.list.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                            <div class="text-muted small mt-2">${this.escapeHtml(message)}</div>
                            <button class="btn btn-sm btn-primary mt-2" onclick="notificationManager.loadNotifications(true)">
                                <i class="bi bi-arrow-clockwise me-1"></i>Thử lại
                            </button>
                        </div>
                    `;
                }
            }

            startAutoRefresh() {
                // Clear existing interval
                if (this.state.refreshInterval) {
                    clearInterval(this.state.refreshInterval);
                }

                // Refresh every 60 seconds, but only if page is visible
                this.state.refreshInterval = setInterval(() => {
                    if (!document.hidden) {
                        this.loadBadgeCount();
                    }
                }, 60000);
            }

            handleClickOutside() {
                document.addEventListener('click', (e) => {
                    if (this.elements.dropdown && this.elements.btn) {
                        if (!this.elements.dropdown.contains(e.target) &&
                            !this.elements.btn.contains(e.target)) {
                            const bsDropdown = bootstrap.Dropdown.getInstance(this.elements.btn);
                            if (bsDropdown && bsDropdown._isShown()) {
                                bsDropdown.hide();
                            }
                        }
                    }
                });
            }

            // Cache management
            getCachedData() {
                try {
                    const cached = localStorage.getItem(this.state.cacheKey);
                    if (cached) {
                        return JSON.parse(cached);
                    }
                } catch (e) {
                    console.warn('Error reading cache:', e);
                }
                return null;
            }

            isCacheExpired(timestamp) {
                return Date.now() - timestamp > this.state.cacheExpiry;
            }

            cacheData(notifications, unreadCount) {
                try {
                    localStorage.setItem(this.state.cacheKey, JSON.stringify({
                        notifications,
                        unreadCount,
                        badgeCount: unreadCount,
                        timestamp: Date.now()
                    }));
                } catch (e) {
                    console.warn('Error caching data:', e);
                }
            }

            cacheBadgeCount(count) {
                try {
                    const cached = this.getCachedData() || {};
                    cached.badgeCount = count;
                    cached.timestamp = Date.now();
                    localStorage.setItem(this.state.cacheKey, JSON.stringify(cached));
                } catch (e) {
                    console.warn('Error caching badge count:', e);
                }
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Public method for manual refresh
            refresh() {
                this.loadBadgeCount();
                if (this.elements.dropdown && this.elements.dropdown.classList.contains('show')) {
                    this.loadNotifications(true);
                }
            }
        }

        // Toast Notification System
        class ToastNotification {
            static show(message, type = 'info', duration = 4000) {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <i class="bi ${this.getIcon(type)} me-2"></i>
                        <span>${this.escapeHtml(message)}</span>
                    </div>
                    <button class="toast-close" onclick="this.parentElement.remove()">
                        <i class="bi bi-x"></i>
                    </button>
                `;

                document.body.appendChild(toast);

                // Trigger animation
                setTimeout(() => toast.classList.add('show'), 10);

                // Auto remove
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }

            static getIcon(type) {
                const icons = {
                    info: 'bi-info-circle-fill',
                    success: 'bi-check-circle-fill',
                    warning: 'bi-exclamation-triangle-fill',
                    danger: 'bi-x-circle-fill'
                };
                return icons[type] || icons.info;
            }

            static escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // Initialize notification manager
        let notificationManager;
        document.addEventListener('DOMContentLoaded', function() {
            notificationManager = new NotificationManager();

            // Listen for new notifications (for realtime updates)
            let previousUnreadCount = 0;
            setInterval(async () => {
                if (notificationManager && !document.hidden) {
                    await notificationManager.loadBadgeCount();
                    const badge = document.getElementById('notificationBadge');
                    const currentCount = badge ? parseInt(badge.textContent) || 0 : 0;
                    if (currentCount > previousUnreadCount && previousUnreadCount > 0) {
                        const newCount = currentCount - previousUnreadCount;
                        ToastNotification.show(
                            `Bạn có ${newCount} thông báo mới`,
                            'info',
                            3000
                        );
                    }
                    previousUnreadCount = currentCount;
                }
            }, 30000); // Check every 30 seconds
        });

        // Flash Messages from Session
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                ToastNotification.show("{{ session('success') }}", 'success', 5000);
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                ToastNotification.show("{{ session('error') }}", 'danger', 8000);
            });
        @endif
    </script>

    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
