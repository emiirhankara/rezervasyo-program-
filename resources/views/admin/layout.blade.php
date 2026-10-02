<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yönetim Paneli | ENTUR')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f172a;
            --ink-soft: #334155;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-light: #f1f5f9;
            --bg: #f8fafc;
            --card: #ffffff;
            --green: #0d9488;
            --green-dark: #0f766e;
            --green-light: #ccfbf1;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --amber: #d97706;
            --amber-light: #fef3c7;
            --red: #dc2626;
            --red-light: #fee2e2;
            --purple: #7c3aed;
            --purple-light: #ede9fe;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            --radius: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; color: var(--ink); background: var(--bg); min-height: 100vh; display: flex; flex-direction: column; }
        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        /* Top Bar */
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            padding: 0 28px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 19px;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .admin-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0d9488, #2563eb);
            border-radius: 9px;
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
        }

        .admin-brand-icon svg { width: 20px; height: 20px; }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .role-badge.system_admin {
            background: var(--purple-light);
            color: var(--purple);
            border: 1px solid #ddd6fe;
        }

        .role-badge.organizer {
            background: var(--green-light);
            color: var(--green-dark);
            border: 1px solid #99f6e4;
        }

        .role-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-site-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-soft);
            transition: all 0.15s ease;
        }

        .btn-site-preview:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: var(--ink);
        }

        .user-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-logout {
            padding: 7px 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fff;
            color: var(--red);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: var(--red-light);
        }

        /* Main Container */
        .admin-container {
            width: min(1360px, calc(100% - 48px));
            margin: 0 auto;
            padding: 32px 0 64px;
            flex: 1;
        }

        .dashboard-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .dashboard-header h1 {
            font-family: 'Manrope', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .dashboard-header p {
            margin-top: 4px;
            color: var(--muted);
            font-size: 14px;
        }

        /* Period Filter Tabs */
        .period-selector {
            display: inline-flex;
            padding: 4px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: var(--shadow-sm);
        }

        .period-btn {
            padding: 8px 16px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            transition: all 0.15s ease;
        }

        .period-btn.active {
            background: var(--ink);
            color: #fff;
            box-shadow: var(--shadow-sm);
        }

        .period-btn:hover:not(.active) {
            color: var(--ink);
            background: #f1f5f9;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .metric-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .metric-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .metric-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .metric-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: #f8fafc;
            color: var(--ink-soft);
        }

        .metric-val {
            font-family: 'Manrope', sans-serif;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .metric-meta {
            margin-top: 8px;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Section Cards */
        .admin-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 28px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .card-header h2 {
            font-family: 'Manrope', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-badge {
            font-size: 12px;
            padding: 3px 9px;
            border-radius: 9999px;
            background: #f1f5f9;
            color: var(--muted);
            font-weight: 700;
        }

        .card-body {
            padding: 24px;
        }

        /* Two Columns Layout */
        .admin-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        /* Modern Tables */
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .data-table th {
            padding: 14px 18px;
            background: #f8fafc;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        .data-table td {
            padding: 16px 18px;
            border-bottom: 1px solid var(--line-light);
            color: var(--ink-soft);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: 0;
        }

        .data-table tr:hover td {
            background: #fafbfd;
        }

        /* Badges & Status */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-badge.paid,
        .status-badge.active,
        .status-badge.confirmed,
        .status-badge.approved {
            background: var(--green-light);
            color: var(--green-dark);
        }

        .status-badge.pending,
        .status-badge.unpaid,
        .status-badge.requested,
        .status-badge.draft {
            background: var(--amber-light);
            color: var(--amber);
        }

        .status-badge.rejected,
        .status-badge.cancelled,
        .status-badge.suspended,
        .status-badge.refunded {
            background: var(--red-light);
            color: var(--red);
        }

        /* Forms & Inputs */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-soft);
        }

        .form-control {
            width: 100%;
            min-height: 44px;
            padding: 9px 13px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            font-size: 14px;
            transition: all 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        textarea.form-control {
            min-height: 85px;
            resize: vertical;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 16px;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-teal {
            background: var(--green);
            color: #fff;
        }

        .btn-teal:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--red);
            color: #fff;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-outline {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--ink-soft);
        }

        .btn-outline:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: var(--ink);
        }

        .btn-sm {
            padding: 6px 11px;
            font-size: 12px;
            border-radius: 6px;
        }

        /* Banner alerts */
        .alert-box {
            padding: 14px 18px;
            border-radius: var(--radius);
            font-size: 13px;
            line-height: 1.5;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .alert-box.success {
            background: var(--green-light);
            border: 1px solid #99f6e4;
            color: var(--green-dark);
        }

        .alert-box.error {
            background: var(--red-light);
            border: 1px solid #fecaca;
            color: var(--red);
        }

        .alert-box.info {
            background: var(--primary-light);
            border: 1px solid #bfdbfe;
            color: var(--primary-dark);
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .progress-bar-wrap {
            width: 100%;
            height: 8px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #0d9488, #2563eb);
            transition: width 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 960px) {
            .admin-cols { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: auto; }
            .admin-header { padding: 0 16px; }
            .admin-container { width: calc(100% - 32px); }
        }

        @media (max-width: 640px) {
            .dashboard-header { flex-direction: column; align-items: flex-start; }
            .header-right .user-name { display: none; }
            .metrics-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <a class="admin-brand" href="{{ auth()->user()->role === 'system_admin' ? route('system.dashboard') : route('organizer.dashboard') }}">
                <div class="admin-brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                        <polyline points="2 17 12 22 22 17"/>
                        <polyline points="2 12 12 17 22 12"/>
                    </svg>
                </div>
                <span>ENTUR <span style="font-weight:500;color:var(--muted);font-size:14px;margin-left:4px">YÖNETİM</span></span>
            </a>
            @if (auth()->user()->role === 'system_admin')
                <span class="role-badge system_admin">
                    <span class="role-dot"></span>
                    Sistem Yöneticisi
                </span>
            @else
                <span class="role-badge organizer">
                    <span class="role-dot"></span>
                    Organizatör
                </span>
            @endif
        </div>

        <div class="header-right">
            <a class="btn-site-preview" href="{{ route('home') }}" target="_blank" title="Kullanıcı arayüzünü yeni sekmede aç">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Siteyi Gör
            </a>
            <div class="user-menu-item">
                <span class="user-name" style="color:var(--ink-soft)">{{ auth()->user()->name }}</span>
                <form action="{{ route('admin.logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button class="btn-logout" type="submit">Çıkış</button>
                </form>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div style="width:min(1360px, calc(100% - 48px)); margin:20px auto 0;">
            <div class="alert-box success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div style="width:min(1360px, calc(100% - 48px)); margin:20px auto 0;">
            <div class="alert-box error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    @yield('content')
</body>
</html>
