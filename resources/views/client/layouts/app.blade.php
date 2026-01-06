@php
    $cart = [];
    $cartCount = 0;

    if (auth()->check()) {
        $cartModel = \App\Models\Cart::with(['items.product'])
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($cartModel) {
            foreach ($cartModel->items as $ci) {
                $product = $ci->product;
                $cart[$ci->id] = [
                    'name' => $product ? $product->name : '',
                    'price' => (float) $ci->price,
                    'quantity' => (int) $ci->quantity,
                ];
                $cartCount += (int) $ci->quantity;
            }
        }
    } else {
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $id => $item) {
            $cart[$id] = $item;
            $cartCount += $item['quantity'] ?? 0;
        }
    }

    $wishlistItems = collect();
    $wishlistCount = 0;

    if (auth()->check()) {
        $wishlistItems = \App\Models\Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        $wishlistCount = $wishlistItems->count();
    }
@endphp

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meteor Shop')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: #f9fafb;
            margin: 0;
        }

        /* ===== HEADER - Modern Professional Design ===== */
        .client-header {
            background: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .client-header.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .client-header__inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        /* Logo */
        .client-logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .client-logo:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .client-logo::before {
            content: '⚡';
            font-size: 24px;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Search Bar */
        .client-search {
            flex: 1;
            max-width: 600px;
            display: flex;
            align-items: center;
            position: relative;
            margin: 0 auto;
        }

        .client-search input {
            flex: 1;
            width: 100%;
            padding: 12px 20px 12px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: #f9fafb;
            color: #111;
            box-sizing: border-box;
        }

        .client-search input::placeholder {
            color: #9ca3af;
        }

        .client-search input:focus {
            border-color: #f97316;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

        .client-search::before {
            content: '\f002';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 18px;
            color: #9ca3af;
            font-size: 16px;
            pointer-events: none;
            z-index: 1;
        }

        .client-search button {
            position: absolute;
            right: 4px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
            z-index: 2;
        }

        .client-search button i {
            font-size: 14px;
        }

        .client-search button:hover {
            background: linear-gradient(135deg, #ea580c, #f97316);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .client-search button:active {
            transform: scale(0.95);
        }

        /* Actions */
        .client-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }

        .client-pill {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .client-pill:hover {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
            border-color: transparent;
        }

        .client-pill__icon {
            font-size: 20px;
        }

        .client-cart {
            position: relative;
        }

        .client-cart__badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
            animation: badgePulse 2s infinite;
        }

        @keyframes badgePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Account */
        .client-account {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border-radius: 50px;
            background: #f3f4f6;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .client-account:hover {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .client-account:hover .client-account__icon,
        .client-account:hover .client-account__primary,
        .client-account:hover .client-account__secondary {
            color: #ffffff;
        }

        .client-account__icon {
            font-size: 20px;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .client-account__labels {
            display: flex;
            flex-direction: column;
            font-size: 12px;
            line-height: 1.3;
        }

        .client-account__primary {
            font-weight: 700;
            color: #111827;
            font-size: 13px;
            transition: color 0.3s ease;
        }

        .client-account__secondary {
            color: #6b7280;
            font-size: 11px;
            transition: color 0.3s ease;
        }

        .client-account__secondary.dropdown-toggle::after {
            margin-left: 6px;
            border-top-color: currentColor;
        }

        /* Navigation */
        .client-nav {
            background: #ffffff;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .client-nav__inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            justify-content: center;
        }

        .client-nav ul {
            list-style: none;
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .client-nav li {
            position: relative;
        }

        .client-nav>.client-nav__inner>ul>li>a {
            display: block;
            padding: 14px 20px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .client-nav>.client-nav__inner>ul>li>a:hover {
            color: #f97316;
            background: rgba(249, 115, 22, 0.08);
        }

        .client-nav>.client-nav__inner>ul>li>a::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 60%;
            height: 2px;
            background: linear-gradient(90deg, #f97316, #fb923c);
            border-radius: 2px;
            transition: transform 0.3s ease;
        }

        .client-nav>.client-nav__inner>ul>li:hover>a::after {
            transform: translateX(-50%) scaleX(1);
        }

        /* Tạo vùng kết nối giữa menu item và dropdown để có thể di chuột vào */
        .client-nav li.has-dropdown::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 12px;
            background: transparent;
            z-index: 1001;
            pointer-events: none;
        }

        .client-nav .dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 0px);
            left: 0;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            min-width: 240px;
            padding: 12px 8px 8px 8px;
            z-index: 1002;
            border: 1px solid rgba(0, 0, 0, 0.06);
            animation: dropdownFadeIn 0.3s ease;
            margin-top: 0;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .client-nav .dropdown-menu li a {
            display: block;
            padding: 12px 16px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
            white-space: nowrap;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .client-nav .dropdown-menu li a:hover {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(251, 146, 60, 0.1));
            color: #f97316;
            padding-left: 20px;
        }

        /* Giữ dropdown hiển thị khi hover vào menu item hoặc dropdown */
        .client-nav li:hover>.dropdown-menu {
            display: block;
        }

        /* Đảm bảo dropdown vẫn hiển thị khi hover vào chính nó */
        .client-nav .dropdown-menu:hover {
            display: block;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Icon menu dọc */
        .menu-toggle {
            font-size: 22px;
            cursor: pointer;
            padding: 6px 5px;
            transition: color 0.3s;
        }

        .menu-toggle:hover {
            color: #ffb703;
        }

        /* MENU DỌC (Sidebar) */
        .vertical-menu {
            position: fixed;
            top: 0;
            right: -33%;
            width: 33%;
            height: 100vh;
            background: rgb(91, 101, 101);
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.3);
            transition: right 0.3s ease;
            z-index: 1001;
        }

        .vertical-menu a {
            color: #fff;
            text-decoration: none;
            padding: 10px 0;
            font-weight: 500;
        }

        .vertical-menu.active {
            right: 0;
        }

        /* Lớp mờ nền */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 1000;
        }

        .overlay.active {
            display: block;
        }


        /* Footer - Professional Design */
        footer {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        .footer-wrapper {
            position: relative;
            z-index: 1;
        }

        .footer-main {
            padding: 20px 0 0px;
            max-width: 1320px;
            margin: 0 auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-widget {
            animation: fadeInUp 0.6s ease-out;
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

        .footer-widget-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 12px;
        }

        .footer-widget-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #f97316, #fb923c);
            border-radius: 2px;
        }

        .footer-logo {
            max-width: 150px;
            margin-bottom: 20px;
            filter: brightness(1.1);
            transition: transform 0.3s ease;
        }

        .footer-logo:hover {
            transform: scale(1.05);
        }

        .footer-description {
            font-size: 14px;
            line-height: 1.7;
            color: #cbd5e1;
            margin-bottom: 24px;
        }

        .footer-social {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .footer-social-link {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            font-size: 18px;
            transition: all 0.3s ease;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .footer-social-link:hover {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a::before {
            content: '→';
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            color: #f97316;
        }

        .footer-links a:hover {
            color: #ffffff;
            padding-left: 8px;
        }

        .footer-links a:hover::before {
            opacity: 1;
            transform: translateX(0);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-contact-icon {
            width: 20px;
            height: 20px;
            color: #f97316;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .footer-contact-text {
            color: #cbd5e1;
            flex: 1;
        }

        .footer-contact-text strong {
            color: #ffffff;
            display: block;
            margin-bottom: 4px;
        }

        .footer-newsletter {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .footer-newsletter-form {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            width: 100%;
            box-sizing: border-box;
            flex-wrap: nowrap;
            align-items: stretch;
        }

        .footer-newsletter-input {
            flex: 1 1 auto;
            min-width: 0;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .footer-newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .footer-newsletter-input:focus {
            border-color: #f97316;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .footer-newsletter-btn {
            flex: 0 0 auto;
            padding: 12px 20px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-sizing: border-box;
            min-width: fit-content;
        }

        .footer-newsletter-btn:hover {
            background: linear-gradient(135deg, #ea580c, #f97316);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .footer-newsletter-btn:active {
            transform: translateY(0);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .footer-bottom-content {
            max-width: 1320px;
            margin: 0 auto;
            padding-left: 24px;
            padding-right: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            text-align: center;
        }

        .footer-copyright {
            color: #94a3b8;
            font-size: 14px;
        }

        .footer-copyright strong {
            color: #ffffff;
            font-weight: 600;
        }

        .footer-payment-methods {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .footer-payment-text {
            color: #94a3b8;
            font-size: 13px;
            margin-right: 8px;
        }

        .footer-payment-icon {
            width: 40px;
            height: 24px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 600;
        }

        /* ===== HEADER RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .client-header__inner {
                padding: 14px 24px;
                gap: 16px;
            }

            .client-search {
                max-width: 400px;
            }

            .client-nav__inner {
                padding: 0 24px;
            }

            .client-nav>.client-nav__inner>ul {
                gap: 4px;
            }

            .client-nav>.client-nav__inner>ul>li>a {
                padding: 12px 16px;
                font-size: 13px;
            }
        }

        @media (max-width: 768px) {
            .client-header__inner {
                padding: 12px 16px;
                gap: 12px;
                flex-wrap: wrap;
            }

            .client-logo {
                font-size: 24px;
            }

            .client-search {
                order: 3;
                width: 100%;
                max-width: 100%;
                margin-top: 12px;
            }

            .client-search input {
                padding: 10px 16px 10px 44px;
                font-size: 14px;
            }

            .client-search button {
                width: 40px;
                height: 40px;
            }

            .client-actions {
                gap: 12px;
            }

            .client-pill {
                width: 40px;
                height: 40px;
            }

            .client-pill__icon {
                font-size: 18px;
            }

            .client-account {
                padding: 6px 12px;
            }

            .client-account__labels {
                display: none;
            }

            .client-account__icon {
                font-size: 18px;
            }

            .client-nav {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .client-header__inner {
                padding: 10px 12px;
            }

            .client-logo {
                font-size: 20px;
            }

            .client-actions {
                gap: 8px;
            }

            .client-pill {
                width: 36px;
                height: 36px;
            }

            .client-pill__icon {
                font-size: 16px;
            }

            .client-cart__badge {
                min-width: 18px;
                height: 18px;
                font-size: 10px;
            }
        }

        /* Responsive Footer */
        @media (max-width: 768px) {
            .footer-main {
                padding: 40px 0 30px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .footer-newsletter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .footer-newsletter-input {
                width: 100%;
                margin-bottom: 0;
            }

            .footer-newsletter-btn {
                width: 100%;
                margin-top: 8px;
            }

            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-payment-methods {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .footer-main {
                padding-left: 16px;
                padding-right: 16px;
            }

            .footer-bottom-content {
                padding-left: 16px;
                padding-right: 16px;
            }
        }

        /* Các style khác */
        a {
            text-decoration: none;
            color: inherit;
        }

        .product-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
            padding: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-20px);
            padding: 16px;
        }

        .product-card img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 6px;
            background: #eee;
            transition: transform 0.4s ease;
            display: block;
            transform-origin: center center;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #111;
            margin: 12px 0 4px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-name:hover {
            color: #09f;
            text-decoration: underline;
        }

        .product-price {
            color: #d41;
            font-weight: 600;
            font-size: 15px;
        }

        .grid-products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(calc(90%/4), 1fr));
            align-items: stretch;
            gap: 24px;
        }

        .badge-new {
            font-size: 12px;
            font-weight: 500;
            background: #111;
            color: #fff;
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .related-wrap {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .slide-wrapper {
            position: relative;
            width: 100%;
            max-width: 100%;
            height: 600px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .slide {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .7s
        }

        .slide.active {
            opacity: 1;
        }

        .imageSlide {
            width: 100vw;
            height: 90vh;
            object-fit: cover;
            position: absolute;
            z-index: -1;
            filter: brightness(0.6)
        }

        h2 {
            color: #000;
            font-size: 2em;
            margin-bottom: 20px;
            z-index: 1
        }


        /* Đã bỏ comment cho button, sử dụng style từ file 2 */
        button {
            z-index: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background: #09f;
            color: #fff;
            cursor: pointer;
            font-style: 1em;
        }

        */ .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8ox 20px rgba(0, 0, 0, 0.15);
        }

        @media (max-width:776px) {
            .room {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width:776px) {
            .grid-products {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px;
            }
        }
    </style>
    @stack('head')

</head>

<body>
    @php
        // Lấy danh mục cha (Phòng) nếu chưa có sẵn
        // Giữ lại logic Laravel Blade từ File 1 để đảm bảo Menu Dropdown hoạt động
        $parentCategories =
            $parentCategories ?? \App\Models\Category::whereNull('parent_id')->where('status', 1)->get();

        // Giả định $childCategories hoặc $cate được truyền vào View hoặc cần được định nghĩa
        // Nếu $childCategories chưa được truyền, bạn cần phải định nghĩa nó ở đây hoặc trong Controller
        $childCategories = $childCategories ?? [];
        // Giả định $cate là danh mục dùng cho Menu dọc
        $cate = $cate ?? ($parentCategories->isNotEmpty() ? $parentCategories : collect());
    @endphp

    <header class="client-header">
        <div class="client-header__inner">
            <a href="{{ route('client.home') }}" class="client-logo">
                Meteor
            </a>

            <form action="{{ route('client.product.search') }}" method="GET" class="client-search">
                <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." value="{{ $searchQuery ?? '' }}"
                    autocomplete="off">
                <button type="submit" aria-label="Tìm kiếm">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <div class="client-actions">
                <div class="client-cart">
                    @auth
                        <a data-bs-toggle="offcanvas" href="#wishlistCanvas" role="button" class="client-pill">
                            <i class="bi bi-heart client-pill__icon"></i>
                        </a>
                        <span class="client-cart__badge {{ $wishlistCount > 0 ? '' : 'd-none' }}" data-wishlist-badge>
                            {{ $wishlistCount }}
                        </span>
                    @else
                        <a href="{{ route('client.login') }}" class="client-pill">
                            <i class="bi bi-heart client-pill__icon"></i>
                        </a>
                    @endauth
                </div>

                <div class="client-cart">
                    @auth
                        <a data-bs-toggle="offcanvas" href="#cartCanvas" role="button" class="client-pill">
                            <i class="bi bi-cart3 client-pill__icon"></i>
                        </a>
                        @if ($cartCount > 0)
                            <span class="client-cart__badge">{{ $cartCount }}</span>
                        @endif
                    @else
                        <a href="{{ route('client.login') }}" class="client-pill">
                            <i class="bi bi-cart3 client-pill__icon"></i>
                        </a>
                    @endauth
                </div>

                <div class="client-account">
                    <i class="fa-regular fa-user client-account__icon"></i>
                    <div class="client-account__labels">
                        @auth
                            <span class="client-account__primary">{{ Auth::user()->name }}</span>
                            <div class="dropdown">
                                <a class="client-account__secondary dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Tài khoản của tôi
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-2">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.account.wallet.index') }}">
                                            <i class="bi bi-wallet2 me-2"></i>Ví của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.account.orders.index') }}">
                                            <i class="bi bi-receipt-cutoff me-2"></i>Đơn hàng
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('client.account.profile') }}">
                                            <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('client.logout') }}" method="POST">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a class="client-account__primary" href="{{ route('client.login') }}">Đăng nhập</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <nav class="client-nav">
            <div class="client-nav__inner">
                <ul>
                    <li class="has-dropdown">
                        <a href="{{ route('client.product.search') }}?category=&minPrice=&maxPrice=&sort=newest"
                            class="dropdown-toggle">Sản phẩm</a>
                        <ul class="dropdown-menu">
                            @forelse ($childCategories as $child)
                                <li>
                                    <a href="{{ route('client.product.category', $child->slug) }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @empty
                                <li><span
                                        style="display: block; padding: 12px 16px; color: #9ca3af; font-size: 14px;">Đang
                                        cập nhật</span></li>
                            @endforelse
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <a href="{{ route('client.product.search') }}?category=&minPrice=&maxPrice=&sort=newest"
                            class="dropdown-toggle">Phòng</a>
                        <ul class="dropdown-menu">
                            @foreach ($parentCategories as $parent)
                                <li>
                                    <a href="{{ route('client.product.category', $parent->slug) }}">
                                        {{ $parent->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="{{ route('client.contact.list') }}">Thiết kế nội thất</a></li>
                    <li><a href="{{ route('client.blogs.list') }}">Bài viết</a></li>
                </ul>
            </div>
        </nav>
    </header>


    <main class="container">
        @yield('content')
    </main>

    {{-- Offcanvas Wishlist --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Danh sách yêu thích</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column" style="height: 100%;">
            @if (auth()->check())
                @if ($wishlistItems->count())
                    <ul class="list-group mb-3">
                        @foreach ($wishlistItems as $wishlist)
                            @php $product = $wishlist->product; @endphp
                            @if ($product)
                                <li class="list-group-item d-flex align-items-center position-relative">
                                    <a href="{{ route('client.product.detail', $product->slug) }}"
                                        class="d-flex flex-column text-decoration-none text-dark flex-grow-1 pe-4">
                                        <strong>{{ $product->name }}</strong>
                                        <small class="text-muted">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </small>
                                    </a>
                                    <button class="btn-close position-absolute top-0 end-0 m-2 remove-wishlist-item"
                                        data-product-id="{{ $product->id }}"></button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="{{ route('client.wishlist.index') }}" class="btn btn-outline-dark w-100">
                            Xem danh sách chi tiết
                        </a>
                    </div>
                @else
                    <p>Danh sách yêu thích trống.</p>
                    <a href="{{ route('client.products.index') }}" class="btn btn-primary w-100 mt-2">
                        Khám phá sản phẩm
                    </a>
                @endif
            @else
                <p>Vui lòng đăng nhập để xem danh sách yêu thích.</p>
                <a href="{{ route('client.login') }}" class="btn btn-primary w-100 mt-2">
                    Đăng nhập
                </a>
            @endif
        </div>
    </div>

    {{-- Offcanvas Cart --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartCanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Giỏ hàng</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column" style="height: 100%;">
            @if ($cart && count($cart))
                <ul class="list-group mb-3">
                    @foreach ($cart as $id => $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center position-relative"
                            id="cart-item-{{ $id }}">
                            <div>
                                <strong>{{ $item['name'] }}</strong> <br>
                                Số lượng: {{ $item['quantity'] }}
                            </div>
                            <span>{{ number_format($item['price'] * $item['quantity']) }}₫</span>
                            <button class="btn-close position-absolute top-0 end-0 m-2 remove-cart-item"
                                data-id="{{ $id }}"></button>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>Tổng:</span>
                    <span id="cart-total">
                        {{ number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart))) }}₫
                    </span>
                </div>

                <div class="mt-auto d-flex flex-column gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-dark w-100">Xem giỏ hàng</a>
                </div>
            @else
                <p>Giỏ hàng trống.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary w-100 mt-2">
                    Quay về trang chủ
                </a>
            @endif
        </div>
    </div>
    <footer id="footer" class="footer-wrapper">
        <div class="footer-main">
            <div class="footer-grid">
                {{-- Widget 1: About & Social --}}
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Về Meteor Shop</h3>
                    <img src="{{ asset('storage/images/meteor.jpg') }}" alt="Meteor Shop Logo" class="footer-logo">
                    <p class="footer-description">
                        Meteor Shop - Thương hiệu nội thất hiện đại hàng đầu Việt Nam.
                        Chúng tôi mang đến những sản phẩm chất lượng cao, thiết kế tinh tế
                        cho không gian sống của bạn.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="footer-social-link" aria-label="Facebook" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Instagram" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Youtube" title="Youtube">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="TikTok" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Zalo" title="Zalo">
                            <i class="bi bi-chat-dots"></i>
                        </a>
                    </div>
                </div>

                {{-- Widget 2: Quick Links --}}
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Meteor Shop</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('client.home') }}">Trang chủ</a></li>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Chuyện Meteor</a></li>
                        <li><a href="#">Tổng công ty</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Thẻ hội viên</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                    </ul>
                </div>

                {{-- Widget 3: Inspiration & Products --}}
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Cảm hứng & Sản phẩm</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('client.products.index') }}">Tất cả sản phẩm</a></li>
                        <li><a href="#">Ý tưởng và cảm hứng</a></li>
                        <li><a href="{{ route('client.blogs.list') }}">Bài viết</a></li>
                        <li><a href="#">Bộ sưu tập</a></li>
                        <li><a href="#">Phòng khách</a></li>
                        <li><a href="#">Phòng ngủ</a></li>
                        <li><a href="#">Phòng làm việc</a></li>
                    </ul>
                </div>

                {{-- Widget 4: Contact & Newsletter --}}
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Liên hệ & Newsletter</h3>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope-fill footer-contact-icon"></i>
                        <div class="footer-contact-text">
                            <strong>Email</strong>
                            <span>meteor@meteorshop.com</span>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone-fill footer-contact-icon"></i>
                        <div class="footer-contact-text">
                            <strong>Hotline</strong>
                            <span>0397 766 836</span>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock-fill footer-contact-icon"></i>
                        <div class="footer-contact-text">
                            <strong>Giờ làm việc</strong>
                            <span>8:00 - 22:00 (Tất cả các ngày)</span>
                        </div>
                    </div>

                    <div class="footer-newsletter">
                        <p style="color: #cbd5e1; font-size: 14px; margin-bottom: 12px; line-height: 1.6;">
                            Đăng ký nhận thông tin mới nhất về sản phẩm và ưu đãi đặc biệt từ Meteor Shop
                        </p>
                        <form class="footer-newsletter-form" id="footerNewsletterForm"
                            onsubmit="handleNewsletterSubmit(event)">
                            @csrf
                            <input type="email" name="email" class="footer-newsletter-input"
                                placeholder="Nhập email của bạn" required>
                            <button type="submit" class="footer-newsletter-btn">
                                <i class="bi bi-send me-1"></i> Đăng ký
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    © <strong>{{ date('Y') }}</strong> Bản quyền của Meteor 
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Newsletter form handler
        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const email = form.querySelector('input[name="email"]').value;
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            // Disable button
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Đang gửi...';

            // Simulate API call (replace with actual endpoint)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Đăng ký thành công!',
                    text: 'Cảm ơn bạn đã đăng ký nhận thông tin từ Meteor Shop.',
                    timer: 3000,
                    showConfirmButton: false
                });
                form.reset();
                button.disabled = false;
                button.innerHTML = originalText;
            }, 1000);
        }

        // Smooth scroll to top on footer logo click
        document.addEventListener('DOMContentLoaded', function() {
            const footerLogo = document.querySelector('.footer-logo');
            if (footerLogo) {
                footerLogo.style.cursor = 'pointer';
                footerLogo.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ----- Xóa sản phẩm khỏi giỏ hàng và reload -----
            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    fetch("{{ route('cart.remove') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                id
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Reload lại toàn bộ trang
                                window.location.reload();
                            } else {
                                alert(data.message || 'Có lỗi xảy ra!');
                            }
                        })
                        .catch(err => console.error(err));
                });
            });

        });
    </script>

    {{-- SweetAlert2 for client-side modals (báo cáo bình luận, thông báo đẹp) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const errorMessage = {!! json_encode(session('error')) !!};

                if (errorMessage.includes('100 triệu') || errorMessage.includes('100.000.000')) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo giới hạn giá trị',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Để đảm bảo an toàn giao dịch và hỗ trợ phương thức vận chuyển đặc biệt cho đơn hàng giá trị cao <b>(trên 100 triệu VNĐ)</b>.</p>
                                <p class="mb-0">Nếu quý khách có nhu cầu đặt giá trị cao, vui lòng liên hệ <b>Hotline/Mail</b> để được hỗ trợ thanh toán trực tiếp và nhận ưu đãi riêng.</p>
                            </div>
                        `,
                        confirmButtonText: 'Đã hiểu',
                        confirmButtonColor: '#3085d6'
                    });
                } else if (errorMessage.includes('10 sản phẩm')) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo giới hạn số lượng',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Để đảm bảo chất lượng vận chuyển tốt nhất, hệ thống hiện giới hạn tối đa <b>10 sản phẩm</b> trên mỗi đơn hàng.</p>
                                <p class="mb-0">Nếu quý khách có nhu cầu đặt số lượng lớn, vui lòng liên hệ <b>Hotline/Mail</b> để nhận chính sách ưu đãi riêng.</p>
                            </div>
                        `,
                        confirmButtonText: 'Đã hiểu',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thông báo',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        </script>
    @endif

    <script>
        // Header scroll effect
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.client-header');
            let lastScroll = 0;

            window.addEventListener('scroll', function() {
                const currentScroll = window.pageYOffset;

                if (currentScroll > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                lastScroll = currentScroll;
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href.length > 1) {
                        const target = document.querySelector(href);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')

    <!-- Bootstrap JS Bundle to enable dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chatbox Widget - Dynamic -->
    <div class="chatbox-wrapper" id="chatboxWrapper" style="display: none;">
        <!-- Chat Icon Button -->
        <div class="chatbox-toggle" id="chatboxToggle">
            <i class="bi bi-chat-dots-fill chatbox-toggle__icon"></i>
            <span class="chatbox-toggle__badge" id="chatBadge" style="display: none;">0</span>
        </div>

        <!-- Chat Popup -->
        <div class="chatbox-popup" id="chatboxPopup">
            <div class="chatbox-popup__header" id="chatboxHeader">
                <div class="chatbox-popup__header-info">
                    <div class="chatbox-popup__avatar">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="chatbox-popup__header-text">
                        <h4 id="chatboxTitle">Hỗ trợ trực tuyến</h4>
                        <span class="chatbox-popup__status" id="chatboxStatus">
                            <span class="chatbox-popup__status-dot"></span>
                            <span id="chatboxStatusText">Trực tuyến</span>
                        </span>
                    </div>
                </div>
                <button class="chatbox-popup__close" id="chatboxClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="chatbox-popup__messages" id="chatMessages">
                <!-- Messages will be loaded dynamically -->
                <div class="chatbox-loading" id="chatLoading">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span>Đang tải...</span>
                </div>
            </div>

            <div class="chatbox-popup__quick-replies" id="chatQuickReplies">
                <!-- Quick replies will be loaded dynamically -->
            </div>

            <div class="chatbox-popup__input">
                <input type="file" id="chatImageInput" accept="image/*" style="display: none;">
                <button class="chatbox-popup__attach" id="chatAttach" title="Gửi hình ảnh">
                    <i class="bi bi-image"></i>
                </button>
                <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button class="chatbox-popup__send" id="chatSend">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
            <!-- Image Preview -->
            <div class="chatbox-popup__preview" id="chatImagePreview" style="display: none;">
                <div class="chatbox-popup__preview-inner">
                    <img src="" alt="Preview" id="chatPreviewImg">
                    <button class="chatbox-popup__preview-remove" id="chatPreviewRemove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Chatbox Wrapper */
        .chatbox-wrapper {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Chat Toggle Button */
        .chatbox-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .chatbox-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }

        .chatbox-toggle__icon {
            font-size: 28px;
            color: #fff;
        }

        .chatbox-toggle__badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* Chat Popup */
        .chatbox-popup {
            position: absolute;
            bottom: 75px;
            right: 0;
            width: 380px;
            max-height: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: chatboxSlideUp 0.3s ease;
        }

        .chatbox-popup.active {
            display: flex;
        }

        @keyframes chatboxSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chat Header */
        .chatbox-popup__header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chatbox-popup__header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chatbox-popup__avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .chatbox-popup__header-text h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .chatbox-popup__status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            opacity: 0.9;
        }

        .chatbox-popup__status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2ecc71;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .chatbox-popup__close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .chatbox-popup__close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Chat Messages */
        .chatbox-popup__messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f8f9fa;
            max-height: 280px;
        }

        .chatbox-message {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .chatbox-message--sent {
            flex-direction: row-reverse;
        }

        .chatbox-message__avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            flex-shrink: 0;
        }

        .chatbox-message--sent .chatbox-message__avatar {
            background: #3498db;
        }

        .chatbox-message__content {
            max-width: 70%;
            background: #fff;
            padding: 10px 14px;
            border-radius: 16px;
            border-top-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .chatbox-message--sent .chatbox-message__content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 16px;
            border-top-right-radius: 4px;
        }

        .chatbox-message__content p {
            margin: 0 0 6px 0;
            font-size: 14px;
            line-height: 1.4;
        }

        .chatbox-message__content p:last-of-type {
            margin-bottom: 0;
        }

        .chatbox-message__time {
            font-size: 11px;
            color: #999;
            display: block;
            margin-top: 6px;
        }

        .chatbox-message--sent .chatbox-message__time {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Quick Replies */
        .chatbox-popup__quick-replies {
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chatbox-quick-reply {
            background: #f0f2f5;
            border: 1px solid #e4e6eb;
            border-radius: 20px;
            padding: 8px 14px;
            font-size: 13px;
            color: #333;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chatbox-quick-reply:hover {
            background: #667eea;
            color: #fff;
            border-color: #667eea;
        }

        /* Chat Input */
        .chatbox-popup__input {
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chatbox-popup__input input {
            flex: 1;
            border: 1px solid #e4e6eb;
            border-radius: 24px;
            padding: 10px 16px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .chatbox-popup__input input:focus {
            border-color: #667eea;
        }

        .chatbox-popup__send {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        .chatbox-popup__send:hover {
            transform: scale(1.05);
        }

        /* Attach button */
        .chatbox-popup__attach {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f0f2f5;
            border: none;
            color: #667eea;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 18px;
        }

        .chatbox-popup__attach:hover {
            background: #667eea;
            color: #fff;
        }

        /* Image Preview */
        .chatbox-popup__preview {
            padding: 10px 16px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }

        .chatbox-popup__preview-inner {
            position: relative;
            display: inline-block;
        }

        .chatbox-popup__preview img {
            max-width: 150px;
            max-height: 100px;
            border-radius: 8px;
            object-fit: cover;
        }

        .chatbox-popup__preview-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #dc3545;
            border: 2px solid #fff;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* Image in message */
        .chatbox-message__image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .chatbox-message__image:hover {
            transform: scale(1.02);
        }

        /* Image modal */
        .chatbox-image-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 10002;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chatbox-image-modal img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .chatbox-image-modal__close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        /* Bot message style */
        .chatbox-message--bot .chatbox-message__avatar {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        }

        .chatbox-message--bot .chatbox-message__content {
            background: #e8f5e9;
            border-left: 3px solid #00b894;
        }

        /* Loading */
        .chatbox-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            color: #666;
        }

        /* Typing indicator */
        .chatbox-typing {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
        }

        .chatbox-typing__dots {
            display: flex;
            gap: 4px;
        }

        .chatbox-typing__dots span {
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
        }

        .chatbox-typing__dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .chatbox-typing__dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typingBounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .chatbox-wrapper {
                bottom: 16px;
                right: 16px;
            }

            .chatbox-popup {
                width: calc(100vw - 32px);
                right: 0;
                bottom: 70px;
            }

            .chatbox-toggle {
                width: 54px;
                height: 54px;
            }

            .chatbox-toggle__icon {
                font-size: 24px;
            }
        }

        .chatbox-wrapper.chatbox-hidden {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chatbox elements
            const chatboxWrapper = document.getElementById('chatboxWrapper');
            const chatboxToggle = document.getElementById('chatboxToggle');
            const chatboxPopup = document.getElementById('chatboxPopup');
            const chatboxClose = document.getElementById('chatboxClose');
            const chatInput = document.getElementById('chatInput');
            const chatSend = document.getElementById('chatSend');
            const chatMessages = document.getElementById('chatMessages');
            const chatQuickReplies = document.getElementById('chatQuickReplies');
            const chatBadge = document.getElementById('chatBadge');
            const chatLoading = document.getElementById('chatLoading');
            const chatboxTitle = document.getElementById('chatboxTitle');
            const chatboxStatusText = document.getElementById('chatboxStatusText');
            const chatboxHeader = document.getElementById('chatboxHeader');
            const chatAttach = document.getElementById('chatAttach');
            const chatImageInput = document.getElementById('chatImageInput');
            const chatImagePreview = document.getElementById('chatImagePreview');
            const chatPreviewImg = document.getElementById('chatPreviewImg');
            const chatPreviewRemove = document.getElementById('chatPreviewRemove');

            // State
            let chatSettings = null;
            let sessionToken = localStorage.getItem('chat_session_token') || '';
            let lastMessageId = 0;
            let pollingInterval = null;
            let isEnabled = false;
            let selectedImage = null;

            // Initialize chatbox
            initChatbox();

            // Image upload handlers
            chatAttach.addEventListener('click', () => chatImageInput.click());

            chatImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Hình ảnh không được vượt quá 5MB');
                        return;
                    }
                    selectedImage = file;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        chatPreviewImg.src = e.target.result;
                        chatImagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            chatPreviewRemove.addEventListener('click', function() {
                selectedImage = null;
                chatImageInput.value = '';
                chatImagePreview.style.display = 'none';
            });

            async function initChatbox() {
                try {
                    const response = await fetch('/chat/settings' + (sessionToken ? '?session_token=' +
                        sessionToken : ''));
                    const data = await response.json();

                    if (!data.enabled) {
                        chatboxWrapper.style.display = 'none';
                        return;
                    }

                    isEnabled = true;
                    chatSettings = data.settings;
                    sessionToken = data.session_token;
                    localStorage.setItem('chat_session_token', sessionToken);

                    // Apply settings
                    applySettings(data.settings);

                    // Load messages
                    if (data.messages && data.messages.length > 0) {
                        chatLoading.style.display = 'none';
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });
                    } else {
                        chatLoading.style.display = 'none';
                    }

                    // Update unread badge
                    updateBadge(data.unread_count || 0);

                    // Show chatbox
                    chatboxWrapper.style.display = 'block';

                    // Start polling for new messages
                    startPolling();

                } catch (error) {
                    console.error('Failed to initialize chatbox:', error);
                    chatboxWrapper.style.display = 'none';
                }
            }

            function applySettings(settings) {
                // Title
                chatboxTitle.textContent = settings.title || 'Hỗ trợ trực tuyến';

                // Status
                chatboxStatusText.textContent = settings.is_working_hours ? 'Trực tuyến' : 'Ngoài giờ làm việc';

                // Colors
                if (settings.primary_color) {
                    const gradient =
                        `linear-gradient(135deg, ${settings.primary_color} 0%, ${settings.secondary_color || settings.primary_color} 100%)`;
                    chatboxHeader.style.background = gradient;
                    document.querySelector('.chatbox-toggle').style.background = gradient;
                }

                // Quick replies
                if (settings.quick_replies && settings.quick_replies.length > 0) {
                    chatQuickReplies.innerHTML = settings.quick_replies.map(qr => `
                        <button class="chatbox-quick-reply" data-message="${qr.message || qr.text}">
                            <i class="bi ${qr.icon || 'bi-chat'}"></i> ${qr.text}
                        </button>
                    `).join('');

                    // Bind quick reply events
                    document.querySelectorAll('.chatbox-quick-reply').forEach(btn => {
                        btn.addEventListener('click', function() {

                            sendMessage(this.dataset.message);
                        });
                    });
                } else {
                    chatQuickReplies.style.display = 'none';
                }

                // Mobile visibility
                if (!settings.show_on_mobile && window.innerWidth <= 768) {
                    chatboxWrapper.classList.add('chatbox-hidden');
                }
            }

            function appendMessage(msg) {
                const isClient = msg.sender_type === 'client';
                const isBot = msg.sender_type === 'bot';
                const typeClass = isClient ? 'chatbox-message--sent' : (isBot ?
                    'chatbox-message--received chatbox-message--bot' : 'chatbox-message--received');
                const icon = isClient ? 'bi-person-fill' : (isBot ? 'bi-robot' : 'bi-headset');

                // Check if message has image
                let contentHtml = '';
                if (msg.message_type === 'image' && msg.attachment_url) {
                    contentHtml =
                        `<img src="${msg.attachment_url}" class="chatbox-message__image" onclick="openImageModal('${msg.attachment_url}')" alt="Image">`;
                    if (msg.message && msg.message !== '[Hình ảnh]') {
                        contentHtml += `<p>${escapeHtml(msg.message)}</p>`;
                    }
                } else {
                    contentHtml = `<p>${escapeHtml(msg.message)}</p>`;
                }

                const html = `
                    <div class="chatbox-message ${typeClass}" data-id="${msg.id}">
                        <div class="chatbox-message__avatar">
                            <i class="bi ${icon}"></i>
                        </div>
                        <div class="chatbox-message__content">
                            ${msg.sender_name && !isClient ? `<small class="text-muted d-block mb-1">${msg.sender_name}</small>` : ''}
                            ${contentHtml}
                            <span class="chatbox-message__time">${msg.time || 'Vừa xong'}</span>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', html);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Image modal function (global)
            window.openImageModal = function(imageUrl) {
                const modal = document.createElement('div');
                modal.className = 'chatbox-image-modal';
                modal.innerHTML = `
                    <button class="chatbox-image-modal__close" onclick="this.parentElement.remove()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <img src="${imageUrl}" alt="Full image">
                `;
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) modal.remove();
                });
                document.body.appendChild(modal);
            };

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            async function sendMessage(message, imageFile = null) {
                if (!message.trim() && !imageFile) return;
                if (!isEnabled) return;

                chatInput.value = '';
                chatInput.disabled = true;
                chatSend.disabled = true;

                // Clear image preview
                if (selectedImage) {
                    chatImagePreview.style.display = 'none';
                    chatImageInput.value = '';
                }

                // Show sent message immediately
                const tempMsg = {
                    id: 'temp-' + Date.now(),
                    message: imageFile ? '📷 Đang gửi hình ảnh...' : message,
                    sender_type: 'client',
                    time: 'Đang gửi...'
                };
                appendMessage(tempMsg);

                try {
                    let response;
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content');

                    if (imageFile) {
                        // Send with FormData for image upload
                        const formData = new FormData();
                        formData.append('_token', csrfToken);

                        formData.append('image', imageFile);
                        if (message.trim()) {
                            formData.append('message', message);
                        }
                        formData.append('session_token', sessionToken);
                        formData.append('page_url', window.location.href);

                        response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken

                            },
                            body: formData
                        });
                    } else {
                        // Send as JSON for text only
                        response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({

                                message: message,
                                session_token: sessionToken,
                                page_url: window.location.href
                            })
                        });
                    }

                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Update session token
                        if (data.session_token) {
                            sessionToken = data.session_token;
                            localStorage.setItem('chat_session_token', sessionToken);
                        }

                        // Remove temp message and add real messages
                        const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                        if (tempEl) tempEl.remove();

                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });

                        // Clear selected image
                        selectedImage = null;

                        // Play sound if enabled
                        if (chatSettings?.play_sound && data.messages.length > 1) {
                            playNotificationSound();
                        }
                    } else if (data.error) {
                        console.error('Server error:', data.error);
                        const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                        if (tempEl) {
                            tempEl.querySelector('.chatbox-message__time').textContent = 'Lỗi: ' + data.error;
                        }
                    }
                } catch (error) {
                    console.error('Failed to send message:', error);
                    console.error('Error details:', error.message);
                    // Update temp message to show error
                    const tempEl = document.querySelector(`[data-id="${tempMsg.id}"]`);
                    if (tempEl) {
                        tempEl.querySelector('.chatbox-message__time').textContent = 'Lỗi gửi tin';
                    }
                }

                chatInput.disabled = false;
                chatSend.disabled = false;
                chatInput.focus();
            }

            async function pollNewMessages() {
                if (!isEnabled || !sessionToken) return;

                try {
                    const response = await fetch(
                        `/chat/messages?session_token=${sessionToken}&last_id=${lastMessageId}`);
                    const data = await response.json();

                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = Math.max(lastMessageId, msg.id);
                        });

                        // Update badge if popup is closed
                        if (!chatboxPopup.classList.contains('active')) {
                            const currentBadge = parseInt(chatBadge.textContent) || 0;
                            updateBadge(currentBadge + data.messages.length);
                        }

                        // Play sound
                        if (chatSettings?.play_sound) {
                            playNotificationSound();
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }

            function startPolling() {
                if (pollingInterval) clearInterval(pollingInterval);
                pollingInterval = setInterval(pollNewMessages, 5000);
            }

            function updateBadge(count) {
                if (count > 0) {
                    chatBadge.textContent = count > 99 ? '99+' : count;
                    chatBadge.style.display = 'flex';

                } else {
                    chatBadge.style.display = 'none';
                }
            }

            function playNotificationSound() {
                try {
                    const audio = new Audio(
                        'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQAA'
                    );
                    audio.volume = 0.3;
                    audio.play().catch(() => {});
                } catch (e) {}
            }

            // Event listeners
            chatboxToggle.addEventListener('click', function() {
                chatboxPopup.classList.toggle('active');
                if (chatboxPopup.classList.contains('active')) {
                    updateBadge(0);
                    chatInput.focus();
                }
            });

            chatboxClose.addEventListener('click', function() {
                chatboxPopup.classList.remove('active');
            });

            chatSend.addEventListener('click', function() {
                sendMessage(chatInput.value, selectedImage);
            });

            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !selectedImage) {
                    sendMessage(chatInput.value);
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý xóa sản phẩm khỏi wishlist trong offcanvas popup
            document.querySelectorAll('.remove-wishlist-item').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productId = this.dataset.productId;
                    if (!productId) return;

                    // Loading nhẹ
                    const originalIcon = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    this.disabled = true;

                    fetch('{{ route('client.wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Server error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success' && data.liked === false) {
                                // 1. Xóa item khỏi popup ngay
                                this.closest('li').remove();

                                // 2. Cập nhật badge wishlist ở header
                                const badge = document.querySelector('[data-wishlist-badge]');
                                if (badge) {
                                    let count = parseInt(badge.textContent) || 1;
                                    count--;
                                    if (count <= 0) {
                                        badge.classList.add('d-none');
                                        // Hiển thị thông báo trống trong popup
                                        const list = document.querySelector(
                                            '#wishlistCanvas .offcanvas-body ul.list-group');
                                        if (list) {
                                            list.innerHTML = `
                                    <p class="text-center text-muted py-4">Danh sách yêu thích trống.</p>
                                    <a href="{{ route('client.products.index') }}" class="btn btn-primary w-100">
                                        Khám phá sản phẩm
                                    </a>
                                `;
                                        }
                                    } else {
                                        badge.textContent = count > 99 ? '99+' : count;
                                    }
                                }

                                // 3. QUAN TRỌNG: Cập nhật tim đỏ → trắng trên trang hiện tại (nếu có)
                                const heartButtons = document.querySelectorAll(
                                    `[data-wishlist-product-id="${productId}"], .wishlist-toggle-btn[data-product-id="${productId}"]`
                                );
                                heartButtons.forEach(heartBtn => {
                                    const icon = heartBtn.querySelector('i');
                                    if (icon) {
                                        icon.classList.remove('bi-heart-fill',
                                            'text-danger');
                                        icon.classList.add('bi-heart');
                                    }
                                    // Nếu có lớp active hoặc liked, xóa đi
                                    heartBtn.classList.remove('liked', 'active');
                                });

                                // Thông báo thành công
                                alert(data.message ||
                                    'Đã xóa khỏi danh sách yêu thích thành công!');
                            } else {
                                alert(data.message || 'Không thể xóa.');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Lỗi kết nối. Vui lòng thử lại.');
                        })
                        .finally(() => {
                            this.innerHTML = originalIcon;
                            this.disabled = false;
                        });
                });
            });
        });
    </script>
    <!-- Floating Contact Buttons (Zalo, Messenger, Phone) -->
    <div class="position-fixed end-0 top-50 translate-middle-y me-3" style="z-index: 1040; pointer-events: none;">
        <div class="d-flex flex-column gap-3 align-items-end" style="pointer-events: auto;">
            @php $contact = \App\Models\ContactInfo::getActive(); @endphp


            @if ($contact->show_messenger && $contact->messenger_link)
                <a href="{{ $contact->messenger_link }}" target="_blank"
                    class="btn btn-info rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; background: #0084ff;" title="Chat Messenger">
                    <i class="bi bi-messenger fs-3 text-white"></i>
                </a>
            @endif

            @if ($contact->show_zalo && $contact->zalo_link)
                <a href="{{ $contact->zalo_link }}" target="_blank"
                    class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; background: #00c853;" title="Chat Zalo">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg" alt="Zalo"
                        width="36">
                </a>
            @endif

            @if ($contact->show_phone && $contact->phone_number)
                <a href="tel:{{ preg_replace('/\D/', '', $contact->phone_number) }}"
                    class="btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;" title="Gọi ngay: {{ $contact->phone_number }}">
                    <i class="bi bi-telephone-fill fs-3 text-white"></i>
                </a>
            @endif
        </div>
    </div>
</body>

</html>
