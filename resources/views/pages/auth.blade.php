@extends('anatema')

@section('title', ($activeTab === 'register' ? 'Kayıt ol' : 'Giriş yap') . ' | ENTUR')

@section('content')
<style>
    .auth-main { width: min(1020px, calc(100% - 48px)); margin: 0 auto; padding: 54px 0 72px; }
    .auth-shell { display: grid; grid-template-columns: .88fr 1.12fr; min-height: 600px; border: 1px solid var(--line); border-radius: 6px; overflow: hidden; animation: rise .5s ease both; }
    .auth-aside { display: flex; flex-direction: column; justify-content: space-between; padding: 42px 38px; background: #164f43; color: #fff; }
    .auth-aside .eyebrow { color: #d6e9df; }
    .auth-aside h1 { max-width: 340px; margin: 16px 0 13px; font: 700 34px/1.18 'Manrope', sans-serif; }
    .auth-aside p { max-width: 340px; margin: 0; color: rgb(255 255 255 / 78%); font-size: 14px; line-height: 1.8; }
    .auth-aside-mark { width: 58px; height: 58px; display: grid; place-items: center; border: 1px solid rgb(255 255 255 / 30%); border-radius: 50%; color: #e9c17b; font: 700 21px 'Manrope', sans-serif; }
    .auth-panel { padding: 36px 42px; background: #fff; }
    .auth-tabs { display: flex; gap: 22px; border-bottom: 1px solid var(--line); }
    .auth-tabs a { position: relative; padding: 0 0 13px; color: var(--muted); font-size: 13px; font-weight: 600; }
    .auth-tabs a.active { color: var(--green-dark); }
    .auth-tabs a.active::after { position: absolute; right: 0; bottom: -1px; left: 0; height: 2px; content: ''; background: var(--green); }
    .auth-heading { margin: 25px 0 22px; }
    .auth-heading h2 { margin: 0 0 7px; font: 700 23px 'Manrope', sans-serif; }
    .auth-heading p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.6; }
    .auth-form { display: grid; grid-template-columns: 1fr; gap: 15px; }
    .auth-form input { min-height: 47px; }
    .password-control { position: relative; }
    .password-control input { padding-right: 48px; }
    .password-toggle { position: absolute; top: 50%; right: 12px; width: 28px; height: 28px; display: grid; place-items: center; transform: translateY(-50%); border: 0; border-radius: 4px; background: transparent; color: #71807b; cursor: pointer; }
    .password-toggle:hover { background: var(--mint); color: var(--green-dark); }
    .password-toggle:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }
    .password-toggle svg { width: 18px; height: 18px; }
    .password-toggle svg[hidden] { display: none; }
    .auth-submit { width: 100%; min-height: 49px; margin-top: 5px; border: 0; border-radius: 4px; background: var(--green); color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; transition: background .18s, transform .18s; }
    .auth-submit:hover { transform: translateY(-1px); background: var(--green-dark); }
    .auth-error { color: #b94335; font-size: 12px; }
    .auth-error-summary { margin: 0 0 16px; padding: 11px 13px; border-left: 3px solid #c55343; background: #fff2ef; color: #8f382d; font-size: 12px; line-height: 1.6; }
    .remember-row { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; }
    .remember-row input { width: 15px; min-height: 15px; accent-color: var(--green); }
    .auth-footnote { margin: 16px 0 0; color: #87938e; font-size: 11px; line-height: 1.6; }
    @media(max-width: 760px) { .auth-main { width: min(100% - 32px, 560px); padding: 34px 0 52px; } .auth-shell { grid-template-columns: 1fr; min-height: 0; } .auth-aside { min-height: 190px; padding: 25px 26px; } .auth-aside h1 { max-width: 470px; margin: 12px 0 8px; font-size: 27px; } .auth-aside p { max-width: 450px; font-size: 13px; } .auth-aside-mark { display: none; } .auth-panel { padding: 27px 25px 31px; } }
</style>
<main class="auth-main">
    <div class="auth-shell">
        <aside class="auth-aside">
            <div>
                <span class="eyebrow">ENTUR hesabı</span>
                <h1>Yolculuğunuza kaldığınız yerden devam edin.</h1>
                <p>Rezervasyon taleplerinizi ve seyahat planlarınızı tek bir hesapta buluşturun.</p>
            </div>
            <div class="auth-aside-mark" aria-hidden="true">E</div>
        </aside>
        <section class="auth-panel" aria-label="Hesap işlemleri">
            <nav class="auth-tabs" aria-label="Giriş ve kayıt">
                <a class="{{ $activeTab === 'login' ? 'active' : '' }}" href="{{ route('login', ['tab' => 'login']) }}" @if ($activeTab === 'login') aria-current="page" @endif>Giriş yap</a>
                <a class="{{ $activeTab === 'register' ? 'active' : '' }}" href="{{ route('register') }}" @if ($activeTab === 'register') aria-current="page" @endif>Kayıt ol</a>
            </nav>

            @if ($errors->any())
                <div class="auth-error-summary" role="alert">{{ $errors->first() }}</div>
            @endif

            @if ($activeTab === 'register')
                <div class="auth-heading">
                    <h2>Yeni hesap oluşturun</h2>
                    <p>Hesap türünüzü seçin ve bilgilerinizi girin.</p>
                </div>
                <form class="auth-form" action="{{ route('register.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="account_type" id="accountType" value="{{ old('account_type', 'customer') }}">

                    {{-- Account Type Selection --}}
                    <div class="field full" style="margin-bottom:4px">
                        <label style="margin-bottom:8px;display:block">Hesap Türü</label>
                        <div class="account-type-cards">
                            <button type="button" class="type-card {{ old('account_type', 'customer') === 'customer' ? 'active' : '' }}" id="typeCardCustomer" onclick="selectAccountType('customer')">
                                <span class="type-card-icon">🛒</span>
                                <span class="type-card-title">Müşteri Hesabı</span>
                                <span class="type-card-desc">Otellere, etkinliklere ve villalara rezervasyon yapın, bilet satın alın.</span>
                            </button>
                            <button type="button" class="type-card {{ old('account_type') === 'organizer' ? 'active' : '' }}" id="typeCardOrganizer" onclick="selectAccountType('organizer')">
                                <span class="type-card-icon">🎪</span>
                                <span class="type-card-title">Organizatör Hesabı</span>
                                <span class="type-card-desc">Etkinlik, otel veya villa ilanı oluşturun, bilet satın ve gelirinizi yönetin.</span>
                            </button>
                        </div>
                        <div class="organizer-notice" id="organizerNotice" style="{{ old('account_type') === 'organizer' ? '' : 'display:none' }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Organizatör hesabınız kayıt sonrası <strong>onay bekleyen</strong> durumda olacaktır. Sistem yöneticisi onayı ve yıllık aidat ödemeniz tamamlandıktan sonra panele erişebilirsiniz.
                        </div>
                        @error('account_type')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="name">İsim soyisim</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="160">
                        @error('name')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="email">E-posta adresi</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="190">
                        @error('email')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="phone">Telefon numarası</label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" required maxlength="30">
                        @error('phone')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="register-password">Şifre</label>
                        <div class="password-control">
                            <input id="register-password" name="password" type="password" autocomplete="new-password" required minlength="8">
                            <button class="password-toggle" type="button" aria-label="Şifreyi göster" aria-pressed="false" data-password-toggle="register-password">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-3 3.5M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 6 9.5 6c1.1 0 2.1-.2 3-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                        @error('password')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="password-confirmation">Şifreyi onayla</label>
                        <div class="password-control">
                            <input id="password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8">
                            <button class="password-toggle" type="button" aria-label="Şifre onayını göster" aria-pressed="false" data-password-toggle="password-confirmation">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-3 3.5M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 6 9.5 6c1.1 0 2.1-.2 3-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <button class="auth-submit" type="submit" id="registerBtn">Kayıt ol ve giriş yap</button>
                    <p class="auth-footnote">Şifreniz güvenli biçimde saklanır ve kayıt sırasında iki şifrenin eşleşmesi zorunludur.</p>
                </form>
            @else
                <div class="auth-heading">
                    <h2>Hesabınıza giriş yapın</h2>
                    <p>Kayıtlı e-posta adresiniz ve şifrenizle devam edin.</p>
                </div>
                <form class="auth-form" action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="email">E-posta adresi</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
                        @error('email')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="login-password">Şifre</label>
                        <div class="password-control">
                            <input id="login-password" name="password" type="password" autocomplete="current-password" required>
                            <button class="password-toggle" type="button" aria-label="Şifreyi göster" aria-pressed="false" data-password-toggle="login-password">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-3 3.5M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 6 9.5 6c1.1 0 2.1-.2 3-.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                        @error('password')<span class="auth-error">{{ $message }}</span>@enderror
                    </div>
                    <label class="remember-row" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1">
                        Oturumumu açık tut
                    </label>
                    <button class="auth-submit" type="submit">Giriş yap</button>
                    <p class="auth-footnote">Henüz hesabınız yok mu? <a href="{{ route('register') }}" style="color:var(--green);font-weight:700">Kayıt olun</a></p>
                </form>
            @endif
        </section>
    </div>
</main>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-pressed', String(!isVisible));
            button.setAttribute('aria-label', isVisible ? 'Şifreyi göster' : 'Şifreyi gizle');
            button.querySelectorAll('svg').forEach((icon, index) => {
                icon.hidden = isVisible ? index === 1 : index === 0;
            });
        });
    });
</script>
@endsection
