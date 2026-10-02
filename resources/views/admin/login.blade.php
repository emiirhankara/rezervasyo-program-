@extends('anatema')

@section('title', 'Yönetim Girişi | ENTUR')

@section('content')
<style>
    .admin-auth-page {
        min-height: calc(100vh - 160px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        background: radial-gradient(circle at 50% 0%, #f1f5f9 0%, #f8fafc 100%);
    }

    .auth-card {
        width: min(540px, 100%);
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
        padding: 38px 40px;
        position: relative;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 5px 14px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 12px;
    }

    .auth-header h1 {
        font-family: 'Manrope', sans-serif;
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .auth-header p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Two-option Role Cards Selector */
    .role-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 24px;
    }

    .role-option-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        background: #fafafa;
        position: relative;
    }

    .role-option-card:hover {
        border-color: #cbd5e1;
        background: #fff;
    }

    .role-option-card.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .role-option-card.active.organizer-theme {
        border-color: #0d9488;
        background: #f0fdfa;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .role-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        margin-bottom: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: 20px;
    }

    .role-option-card.active .role-icon {
        border-color: transparent;
        background: #2563eb;
        color: #fff;
    }

    .role-option-card.active.organizer-theme .role-icon {
        background: #0d9488;
        color: #fff;
    }

    .role-title {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .role-desc {
        font-size: 11px;
        color: #64748b;
        line-height: 1.4;
    }

    .role-check-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #cbd5e1;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 11px;
    }

    .role-option-card.active .role-check-indicator {
        background: #2563eb;
    }

    .role-option-card.active.organizer-theme .role-check-indicator {
        background: #0d9488;
    }

    /* Form Fields */
    .admin-form-group {
        margin-bottom: 18px;
    }

    .admin-form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 7px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        display: flex;
        align-items: center;
        pointer-events: none;
    }

    .admin-input {
        width: 100%;
        min-height: 48px;
        padding: 10px 14px 10px 42px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
        transition: all 0.15s ease;
    }

    .admin-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .remember-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        font-size: 13px;
        color: #475569;
    }

    .remember-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-admin-submit {
        width: 100%;
        min-height: 48px;
        border: 0;
        border-radius: 9px;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        font-family: 'Manrope', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-admin-submit:hover {
        background: linear-gradient(135deg, #1e293b, #334155);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
    }

    /* Demo Helper */
    .demo-credentials {
        margin-top: 26px;
        padding-top: 20px;
        border-top: 1px dashed #e2e8f0;
        text-align: center;
    }

    .demo-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }

    .demo-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-demo {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-demo:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
    }

    .auth-alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    @media (max-width: 520px) {
        .auth-card { padding: 26px 20px; }
        .role-cards { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-badge">ENTUR Güvenli Yönetim Portalı</span>
            <h1>Yönetici Girişi</h1>
            <p>Lütfen yetki türünüzü seçip hesap bilgilerinizle giriş yapın.</p>
        </div>

        @if ($errors->any())
            <div class="auth-alert" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('admin.login.store') }}" method="POST">
            @csrf

            <!-- Hidden Role Input Controlled by Interactive Role Cards -->
            <input type="hidden" name="role" id="selectedRole" value="{{ old('role', 'system_admin') }}">

            <!-- Two-Option Selection Cards -->
            <div class="role-cards" role="radiogroup" aria-label="Yönetici Türü Seçimi">
                <div class="role-option-card {{ old('role', 'system_admin') === 'system_admin' ? 'active' : '' }}" id="cardSystemAdmin" onclick="selectAdminRole('system_admin')">
                    <div class="role-check-indicator">✓</div>
                    <div class="role-icon">🛡️</div>
                    <div class="role-title">Sistem Yöneticisi</div>
                    <div class="role-desc">Tüm platform, aidatlar, organizatörler ve finans</div>
                </div>

                <div class="role-option-card organizer-theme {{ old('role') === 'organizer' ? 'active' : '' }}" id="cardOrganizer" onclick="selectAdminRole('organizer')">
                    <div class="role-check-indicator">✓</div>
                    <div class="role-icon">🎪</div>
                    <div class="role-title">Organizatör</div>
                    <div class="role-desc">İlanlarım, bilet kotaları, satışlar ve mesajlar</div>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="adminEmail">E-posta Adresi</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <input class="admin-input" id="adminEmail" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="ornek@entur.com">
                </div>
            </div>

            <div class="admin-form-group">
                <label for="adminPassword">Şifre</label>
                <div class="input-wrapper">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <input class="admin-input" id="adminPassword" name="password" type="password" required autocomplete="current-password" placeholder="••••••••••••">
                </div>
            </div>

            <div class="remember-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember" value="1" style="width:16px;height:16px;accent-color:#2563eb">
                    <span>Oturumumu açık tut</span>
                </label>
            </div>

            <button class="btn-admin-submit" type="submit" id="submitBtn">
                <span id="submitBtnText">Sistem Yöneticisi Olarak Giriş Yap</span>
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <div class="demo-credentials">
            <div class="demo-title">Hızlı Test Bilgileri</div>
            <div class="demo-buttons">
                <button class="btn-demo" type="button" onclick="fillDemo('system_admin', 'admin@entur.com', 'Admin123456!')">
                    🛡️ Sistem Yöneticisi
                </button>
                <button class="btn-demo" type="button" onclick="fillDemo('organizer', 'organizer@entur.com', 'Organizer123456!')">
                    🎪 Organizatör
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function selectAdminRole(role) {
        document.getElementById('selectedRole').value = role;
        const cardSys = document.getElementById('cardSystemAdmin');
        const cardOrg = document.getElementById('cardOrganizer');
        const submitText = document.getElementById('submitBtnText');

        if (role === 'system_admin') {
            cardSys.classList.add('active');
            cardOrg.classList.remove('active');
            submitText.textContent = 'Sistem Yöneticisi Olarak Giriş Yap';
        } else {
            cardOrg.classList.add('active');
            cardSys.classList.remove('active');
            submitText.textContent = 'Organizatör Olarak Giriş Yap';
        }
    }

    function fillDemo(role, email, pass) {
        selectAdminRole(role);
        document.getElementById('adminEmail').value = email;
        document.getElementById('adminPassword').value = pass;
    }

    // Initialize button text on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentRole = document.getElementById('selectedRole').value;
        selectAdminRole(currentRole);
    });
</script>
@endsection
