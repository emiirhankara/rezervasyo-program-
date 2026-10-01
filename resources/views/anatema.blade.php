<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Anatema | Rezervasyon')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --ink: #202b28;
            --muted: #71807b;
            --line: #e4eae7;
            --green: #176b57;
            --green-dark: #105442;
            --mint: #e8f4ef;
            --coral: #e7785d;
            font-family: 'DM Sans', sans-serif;
            background: #fff;
            color: var(--ink);
        }

        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; background: #fff; }
        button, input, select { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .site-header { border-bottom: 1px solid #edf0ee; }
        .header-inner { width: min(1120px, calc(100% - 48px)); min-height: 76px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 28px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font: 800 20px 'Manrope', sans-serif; letter-spacing: .04em; }
        .brand-mark { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; color: white; background: var(--green); }
        .brand-mark svg { width: 20px; height: 20px; }
        .main-nav { display: flex; align-items: center; gap: 6px; height: 76px; }
        .nav-link, .nav-trigger { display: inline-flex; align-items: center; gap: 7px; min-height: 42px; padding: 0 14px; border: 0; border-radius: 4px; background: transparent; color: #40504a; font: 600 13px 'DM Sans', sans-serif; cursor: pointer; }
        .nav-link:hover, .nav-trigger:hover, .nav-link:focus-visible, .nav-trigger:focus-visible { color: var(--green); background: #f2f7f4; outline: none; }
        .nav-booking { color: var(--green); }
        .nav-trigger svg { width: 13px; height: 13px; transition: transform .18s; }
        .nav-dropdown { position: relative; height: 100%; display: flex; align-items: center; }
        .dropdown-menu { position: absolute; z-index: 5; top: calc(100% - 8px); right: 0; width: 205px; padding: 7px; border: 1px solid var(--line); border-radius: 6px; background: #fff; box-shadow: 0 12px 30px rgb(32 43 40 / 10%); opacity: 0; visibility: hidden; transform: translateY(5px); transition: opacity .16s, transform .16s, visibility .16s; }
        .nav-dropdown:hover .dropdown-menu, .nav-dropdown:focus-within .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-dropdown:hover .nav-trigger svg, .nav-dropdown:focus-within .nav-trigger svg { transform: rotate(180deg); }
        .dropdown-menu a { display: block; padding: 11px 12px; border-radius: 4px; color: #46544f; font-size: 13px; }
        .dropdown-menu a:hover, .dropdown-menu a:focus-visible { outline: none; background: var(--mint); color: var(--green-dark); }
        .mobile-booking-links { display: none; }
        .header-note { display: none; }
        .global-status { width: min(1120px, calc(100% - 48px)); margin: 18px auto 0; padding: 12px 15px; border-left: 3px solid var(--green); background: var(--mint); color: var(--green-dark); font-size: 13px; }
        .account-name { color: var(--green-dark); cursor: default; }
        .main-nav form { margin: 0; }
        main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 56px 0 72px; }
        .intro { max-width: 700px; margin-bottom: 34px; animation: rise .55s ease both; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; color: var(--green); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; }
        .eyebrow::before { content: ''; width: 22px; height: 2px; background: var(--coral); }
        h1 { margin: 14px 0 10px; font: 700 38px/1.16 'Manrope', sans-serif; letter-spacing: 0; }
        .intro p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.7; }
        .booking-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 44px; align-items: start; }
        .form-section { padding: 28px 0; border-top: 1px solid var(--line); animation: rise .55s ease both; }
        .form-section:nth-child(2) { animation-delay: .08s; }
        .form-section:nth-child(3) { animation-delay: .16s; }
        .section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
        .step { width: 30px; height: 30px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 50%; background: var(--mint); color: var(--green); font-size: 13px; font-weight: 700; }
        h2 { margin: 0; font: 700 17px 'Manrope', sans-serif; }
        .fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 8px; }
        .field.full { grid-column: 1 / -1; }
        label { color: #46544f; font-size: 13px; font-weight: 600; }
        input, select { width: 100%; min-height: 48px; padding: 0 14px; border: 1px solid #dce4e0; border-radius: 5px; outline: none; background: #fff; color: var(--ink); transition: border-color .18s, box-shadow .18s; }
        input:focus, select:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgb(23 107 87 / 10%); }
        input::placeholder { color: #a0aaa6; }
        .aside { position: sticky; top: 24px; padding: 24px; border: 1px solid var(--line); border-radius: 8px; background: #fff; animation: rise .6s .12s ease both; }
        .aside-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding-bottom: 20px; border-bottom: 1px solid var(--line); }
        .aside-kicker { margin-bottom: 6px; color: var(--muted); font-size: 12px; }
        .aside-title { font: 700 17px 'Manrope', sans-serif; }
        .badge { padding: 6px 9px; border-radius: 4px; background: var(--mint); color: var(--green); font-size: 11px; font-weight: 700; white-space: nowrap; }
        .summary-list { display: grid; gap: 15px; padding: 20px 0; border-bottom: 1px solid var(--line); }
        .summary-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; color: var(--muted); font-size: 13px; }
        .summary-row strong { color: var(--ink); font-weight: 600; text-align: right; }
        .summary-total { display: flex; justify-content: space-between; gap: 14px; padding: 19px 0 15px; font-weight: 700; }
        .summary-total strong { color: var(--green-dark); font: 800 22px 'Manrope', sans-serif; }
        .summary-caption { margin: -8px 0 18px; color: var(--muted); font-size: 11px; }
        .submit-button { width: 100%; min-height: 50px; display: flex; align-items: center; justify-content: center; gap: 10px; border: 0; border-radius: 5px; background: var(--green); color: #fff; font-weight: 700; cursor: pointer; transition: background .18s, transform .18s; }
        .submit-button:hover { background: var(--green-dark); transform: translateY(-1px); }
        .submit-button svg { width: 17px; height: 17px; }
        .secure-note { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 13px; color: var(--muted); font-size: 11px; }
        .secure-note svg { width: 14px; height: 14px; }
        .notice { display: none; margin-top: 14px; padding: 12px; border-left: 3px solid var(--green); background: var(--mint); color: var(--green-dark); font-size: 13px; line-height: 1.5; }
        .notice.visible { display: block; }
        .site-footer { border-top: 1px solid #edf0ee; color: var(--muted); font-size: 12px; }
        .footer-inner { width: min(1120px, calc(100% - 48px)); min-height: 58px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .footer-links { display: flex; gap: 18px; }
        .footer-links a:hover { color: var(--green); }
        @keyframes rise { from { opacity: 0; transform: translateY(9px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 800px) { .booking-layout { grid-template-columns: 1fr; gap: 20px; } .aside { position: static; grid-row: 1; } main { padding-top: 38px; } }
        @media (max-width: 620px) { .header-inner { width: min(100% - 28px, 1120px); min-height: 68px; flex-wrap: wrap; gap: 0; padding: 11px 0 7px; } .main-nav { height: auto; width: 100%; justify-content: flex-start; flex-wrap: wrap; gap: 0; } .nav-link, .nav-trigger { min-height: 39px; padding: 0 8px; font-size: 12px; } .nav-dropdown { height: auto; } .dropdown-menu { top: calc(100% + 2px); left: auto; right: 0; } .main-nav > .nav-dropdown { display: none; } .mobile-booking-links { display: flex; order: 6; flex: 0 0 100%; justify-content: space-around; gap: 4px; } .main-nav > a.nav-link:last-child, .main-nav > .account-name { order: 4; margin-left: auto; } .main-nav form { order: 5; } .account-name { max-width: 88px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; } .mobile-booking-links a { padding: 7px 8px; border-radius: 4px; color: var(--green); font-size: 11px; font-weight: 600; } .header-inner, main, .footer-inner { width: min(100% - 32px, 1120px); } .global-status { width: min(100% - 32px, 1120px); } main { padding-top: 32px; padding-bottom: 48px; } h1 { font-size: 31px; } .fields { grid-template-columns: 1fr; gap: 15px; } .field.full { grid-column: auto; } .aside { padding: 20px; } .footer-inner { justify-content: center; text-align: center; } .footer-inner span:nth-child(2) { display: none; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="ENTUR ana sayfa">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 18 12 5l8 13M7 13h10M9.5 18l2.5-4 2.5 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                ENTUR
            </a>
            <nav class="main-nav" aria-label="Ana menü">
                <a class="nav-link" href="{{ route('home') }}">Ana Sayfa</a>
                <a class="nav-link" href="{{ route('about') }}">Hakkımızda</a>
                <a class="nav-link" href="{{ route('contact') }}">İletişim</a>
                <div class="nav-dropdown">
                    <a class="nav-link nav-trigger nav-booking" href="{{ route('categories.index') }}" aria-haspopup="true">
                        Rezervasyon
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('categories.show', 'oteller') }}">Oteller</a>
                        <a href="{{ route('categories.show', 'etkinlikler') }}">Etkinlikler</a>
                        <a href="{{ route('categories.show', 'kiralik-villalar') }}">Kiralık villalar</a>
                    </div>
                </div>
                <div class="mobile-booking-links" aria-label="Rezervasyon seçenekleri">
                    <a href="{{ route('categories.show', 'oteller') }}">Oteller</a>
                    <a href="{{ route('categories.show', 'etkinlikler') }}">Etkinlikler</a>
                    <a href="{{ route('categories.show', 'kiralik-villalar') }}">Villalar</a>
                </div>
                @guest
                    <a class="nav-link nav-booking" href="{{ route('login') }}">Giriş / Kayıt</a>
                @else
                    <span class="nav-link account-name">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link" type="submit">Çıkış</button>
                    </form>
                @endguest
            </nav>
        </div>
    </header>
    @if (session('status'))
        <div class="global-status" role="status">{{ session('status') }}</div>
    @endif
    @yield('content')
    <footer class="site-footer">
        <div class="footer-inner">
            <span>© {{ date('Y') }} ENTUR</span>
            <span>Yolculuğunuzun her adımında yanınızdayız.</span>
            <nav class="footer-links" aria-label="Alt menü">
                <a href="{{ route('about') }}">Hakkımızda</a>
                <a href="{{ route('contact') }}">İletişim</a>
            </nav>
        </div>
    </footer>
</body>
</html>