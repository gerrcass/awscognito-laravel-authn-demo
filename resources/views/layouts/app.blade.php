<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediCore')</title>
    <style>
        /* ============================================
           Design System — MediCore PoC
           ============================================ */

        :root {
            --color-primary: #1e3a5f;
            --color-primary-dark: #152a45;
            --color-primary-light: #2a5285;
            --color-surface: #ffffff;
            --color-background: #f5f7fa;
            --color-text: #1a1a2e;
            --color-text-secondary: #64748b;
            --color-text-muted: #94a3b8;
            --color-border: #e2e8f0;
            --color-border-light: #f1f5f9;
            --color-success: #16a34a;
            --color-success-bg: #dcfce7;
            --color-error: #dc2626;
            --color-error-bg: #fee2e2;
            --color-warning: #ca8a04;
            --color-warning-bg: #fef9c3;
            --color-info: #2563eb;
            --color-info-bg: #dbeafe;
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --font-mono: "SF Mono", Monaco, "Cascadia Code", monospace;
            --space-1: 0.25rem;   /* 4px  */
            --space-2: 0.5rem;    /* 8px  */
            --space-3: 0.75rem;   /* 12px */
            --space-4: 1rem;      /* 16px */
            --space-5: 1.25rem;   /* 20px */
            --space-6: 1.5rem;    /* 24px */
            --space-8: 2rem;      /* 32px */
            --space-10: 2.5rem;   /* 40px */
            --space-12: 3rem;     /* 48px */
            --space-16: 4rem;     /* 64px */
            --transition-base: 150ms ease;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: var(--font-sans);
            font-size: 0.9375rem;
            line-height: 1.6;
            color: var(--color-text);
            background-color: var(--color-background);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============================================
           Typography
           ============================================ */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.25;
            color: var(--color-text);
            letter-spacing: -0.01em;
        }

        h1 { font-size: 1.75rem; }
        h2 { font-size: 1.5rem; }
        h3 { font-size: 1.25rem; }
        p { margin-bottom: var(--space-4); }
        p:last-child { margin-bottom: 0; }

        a {
            color: var(--color-primary-light);
            text-decoration: none;
            transition: color var(--transition-base);
        }
        a:hover { color: var(--color-primary); text-decoration: underline; }
        a:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
            border-radius: var(--radius-sm);
        }

        /* ============================================
           Layout
           ============================================ */
        .app-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .app-header {
            background-color: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .app-header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--space-4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 3.5rem;
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--color-primary);
            text-decoration: none;
        }
        .app-brand:hover { text-decoration: none; color: var(--color-primary); }
        .app-brand svg {
            width: 1.5rem;
            height: 1.5rem;
            color: var(--color-primary);
        }

        .app-nav {
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        .app-nav-link {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-3);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-text-secondary);
            transition: all var(--transition-base);
        }
        .app-nav-link:hover {
            background-color: var(--color-background);
            color: var(--color-text);
            text-decoration: none;
        }
        .app-nav-link.is-active {
            background-color: var(--color-primary);
            color: #ffffff;
        }
        .app-nav-link.is-active:hover {
            background-color: var(--color-primary-dark);
            color: #ffffff;
        }

        .app-nav-separator {
            width: 1px;
            height: 1.25rem;
            background-color: var(--color-border);
            margin: 0 var(--space-2);
        }

        .app-user {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            font-size: 0.875rem;
            color: var(--color-text-secondary);
        }

        .app-user-name {
            font-weight: 500;
            color: var(--color-text);
        }

        .app-main {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: var(--space-6) var(--space-4);
        }

        /* ============================================
           Cards
           ============================================ */
        .card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: var(--space-5) var(--space-6);
            border-bottom: 1px solid var(--color-border-light);
        }
        .card-header h1,
        .card-header h2,
        .card-header h3 {
            margin-bottom: var(--space-1);
        }
        .card-header p {
            color: var(--color-text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .card-body {
            padding: var(--space-6);
        }

        .card-body.is-padded-sm {
            padding: var(--space-4);
        }

        /* ============================================
           Forms
           ============================================ */
        .form-group {
            margin-bottom: var(--space-5);
        }
        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            margin-bottom: var(--space-2);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-text);
        }
        .form-label.is-required::after {
            content: " *";
            color: var(--color-error);
        }

        .form-input {
            display: block;
            width: 100%;
            padding: var(--space-3) var(--space-4);
            font-size: 0.9375rem;
            line-height: 1.5;
            color: var(--color-text);
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            transition: border-color var(--transition-base), box-shadow var(--transition-base);
        }
        .form-input::placeholder {
            color: var(--color-text-muted);
        }
        .form-input:hover {
            border-color: var(--color-text-muted);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgb(30 58 95 / 0.1);
        }

        /* ============================================
           Buttons
           ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            padding: var(--space-3) var(--space-5);
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all var(--transition-base);
            text-decoration: none;
        }
        .btn:hover { text-decoration: none; }
        .btn:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            border-color: var(--color-primary);
        }
        .btn-primary:hover {
            background-color: var(--color-primary-dark);
            border-color: var(--color-primary-dark);
        }

        .btn-secondary {
            background-color: var(--color-surface);
            color: var(--color-text);
            border-color: var(--color-border);
        }
        .btn-secondary:hover {
            background-color: var(--color-background);
            border-color: var(--color-text-muted);
        }

        .btn-danger {
            background-color: var(--color-error);
            color: #ffffff;
            border-color: var(--color-error);
        }
        .btn-danger:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .btn-success {
            background-color: var(--color-success);
            color: #ffffff;
            border-color: var(--color-success);
        }
        .btn-success:hover {
            background-color: #15803d;
            border-color: #15803d;
        }

        .btn-sm {
            padding: var(--space-2) var(--space-3);
            font-size: 0.8125rem;
        }

        .btn-lg {
            padding: var(--space-3) var(--space-6);
            font-size: 1rem;
        }

        /* ============================================
           Tables
           ============================================ */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }
        .table thead {
            background-color: var(--color-background);
        }
        .table th {
            padding: var(--space-3) var(--space-4);
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-text-secondary);
            border-bottom: 1px solid var(--color-border);
        }
        .table td {
            padding: var(--space-3) var(--space-4);
            border-bottom: 1px solid var(--color-border-light);
            color: var(--color-text);
            vertical-align: top;
        }
        .table tbody tr:hover {
            background-color: var(--color-background);
        }
        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ============================================
           Status badges
           ============================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: var(--space-1) var(--space-2);
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            border-radius: 9999px;
            white-space: nowrap;
        }
        .badge-success {
            background-color: var(--color-success-bg);
            color: var(--color-success);
        }
        .badge-error {
            background-color: var(--color-error-bg);
            color: var(--color-error);
        }
        .badge-warning {
            background-color: var(--color-warning-bg);
            color: var(--color-warning);
        }
        .badge-info {
            background-color: var(--color-info-bg);
            color: var(--color-info);
        }

        /* ============================================
           Alerts
           ============================================ */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: var(--space-3);
            padding: var(--space-4);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-5);
            font-size: 0.875rem;
        }
        .alert:last-child {
            margin-bottom: 0;
        }
        .alert-success {
            background-color: var(--color-success-bg);
            color: var(--color-success);
            border: 1px solid #bbf7d0;
        }
        .alert-error {
            background-color: var(--color-error-bg);
            color: var(--color-error);
            border: 1px solid #fecaca;
        }
        .alert-warning {
            background-color: var(--color-warning-bg);
            color: var(--color-warning);
            border: 1px solid #fde68a;
        }
        .alert-info {
            background-color: var(--color-info-bg);
            color: var(--color-info);
            border: 1px solid #bfdbfe;
        }

        .alert-icon {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 1px;
        }

        .alert-content {
            flex: 1;
        }
        .alert-content p {
            margin-bottom: var(--space-1);
        }
        .alert-content p:last-child {
            margin-bottom: 0;
        }

        /* ============================================
           Empty / Error / Access Denied States
           ============================================ */
        .state-empty,
        .state-error,
        .state-denied {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: var(--space-16) var(--space-4);
            max-width: 28rem;
            margin: 0 auto;
        }

        .state-icon {
            width: 4rem;
            height: 4rem;
            margin-bottom: var(--space-5);
            color: var(--color-text-muted);
        }
        .state-denied .state-icon {
            color: var(--color-error);
        }

        .state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: var(--space-2);
        }

        .state-description {
            color: var(--color-text-secondary);
            margin-bottom: var(--space-6);
        }

        /* ============================================
           Login page specific
           ============================================ */
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: var(--space-4);
            background: linear-gradient(160deg, var(--color-background) 0%, #e8ecf1 100%);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .login-card-header {
            padding: var(--space-8) var(--space-6) var(--space-4);
            text-align: center;
        }
        .login-card-header .brand {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--color-primary);
            margin-bottom: var(--space-4);
        }
        .login-card-header h1 {
            font-size: 1.25rem;
            margin-bottom: var(--space-1);
        }
        .login-card-header p {
            color: var(--color-text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .login-card-body {
            padding: var(--space-6);
        }

        .login-card-footer {
            padding: var(--space-4) var(--space-6);
            background-color: var(--color-background);
            border-top: 1px solid var(--color-border);
            text-align: center;
            font-size: 0.8125rem;
            color: var(--color-text-muted);
        }

        /* ============================================
           Dashboard cards
           ============================================ */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: var(--space-5);
        }
        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .dashboard-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition-base), transform var(--transition-base);
        }
        .dashboard-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }
        .dashboard-card-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: var(--radius-md);
            background-color: var(--color-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-4);
        }
        .dashboard-card-icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .dashboard-card h3 {
            font-size: 1rem;
            margin-bottom: var(--space-1);
        }
        .dashboard-card p {
            font-size: 0.875rem;
            color: var(--color-text-secondary);
            margin-bottom: var(--space-4);
        }
        .dashboard-card a {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* ============================================
           Action bar (Caja)
           ============================================ */
        .action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-3);
            align-items: center;
        }

        /* ============================================
           Responsive nav
           ============================================ */
        @media (max-width: 767px) {
            .app-header-inner {
                flex-wrap: wrap;
                height: auto;
                padding-top: var(--space-2);
                padding-bottom: var(--space-2);
                gap: var(--space-2);
            }
            .app-nav {
                width: 100%;
                overflow-x: auto;
                padding-bottom: var(--space-1);
            }
            .app-main {
                padding: var(--space-4);
            }
            .card-header,
            .card-body {
                padding: var(--space-4);
            }
            .table th,
            .table td {
                padding: var(--space-2) var(--space-3);
            }
        }
    </style>
</head>
<body>

    @auth
    <header class="app-header">
        <div class="app-header-inner">
            <a href="{{ route('dashboard') }}" class="app-brand">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
                MediCore
            </a>

            <nav class="app-nav" aria-label="Navegación principal">
                @php
                    $currentRoute = Route::currentRouteName();
                @endphp

                @if(Auth::user()->hasPerm('dashboard.view'))
                    <a href="{{ route('dashboard') }}" class="app-nav-link {{ $currentRoute === 'dashboard' ? 'is-active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                        Dashboard
                    </a>
                @endif
                @if(Auth::user()->hasPerm('patients.view'))
                    <a href="{{ route('patients') }}" class="app-nav-link {{ $currentRoute === 'patients' ? 'is-active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        Pacientes
                    </a>
                @endif
                @if(Auth::user()->hasPerm('users.manage'))
                    <a href="{{ route('admin.users') }}" class="app-nav-link {{ $currentRoute === 'admin.users' ? 'is-active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        Usuarios
                    </a>
                @endif
                @if(Auth::user()->hasPerm('dashboard.view'))
                    <a href="{{ route('caja') }}" class="app-nav-link {{ $currentRoute === 'caja' ? 'is-active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Caja
                    </a>
                @endif
            </nav>

            <div class="app-user">
                <span class="app-user-name">{{ Auth::user()->name }}</span>
                <span class="badge badge-info">{{ Auth::user()->role?->name ?? 'Sin rol' }}</span>
                <span class="app-nav-separator"></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary" style="padding: var(--space-1) var(--space-3); font-size: 0.8125rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </header>
    @endauth

    @if(session('status'))
        <div class="app-container" style="padding-top: var(--space-4);">
            <div class="app-main" style="padding-top: 0; padding-bottom: 0;">
                <div class="alert alert-success" role="status">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div class="alert-content">{{ session('status') }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="app-container">
        <main class="app-main" role="main">
            @yield('content')
        </main>
    </div>

</body>
</html>
