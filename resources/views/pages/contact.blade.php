@extends('anatema')

@section('title', 'İletişim | ENTUR')

@section('content')
<style>
    .site-header { position: sticky; z-index: 20; top: 0; background: rgb(255 255 255 / 96%); backdrop-filter: blur(10px); }
    .contact-main { position: relative; isolation: isolate; width: 100%; min-height: calc(100vh - 138px); margin: 0; padding: 36px max(24px, calc((100% - 1120px) / 2)) 40px; background: url('https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=2000&q=85') center 55% / cover fixed; }
    .contact-main::before { position: absolute; z-index: -1; inset: 0; content: ''; background: linear-gradient(90deg, rgb(255 255 255 / 82%), rgb(255 255 255 / 70%)); }
    .contact-heading { max-width: 660px; margin-bottom: 24px; }
    .contact-heading h1 { margin: 10px 0 8px; font: 800 36px/1.2 'Manrope', sans-serif; color: var(--ink); }
    .contact-heading p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.6; }
    .contact-layout { display: grid; grid-template-columns: .8fr 1.2fr; gap: 40px; padding: 28px; border: 1px solid rgb(228 234 231 / 90%); border-radius: 12px; background: rgb(255 255 255 / 94%); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06); }
    .contact-info h2, .contact-form h2 { margin: 0 0 14px; font: 700 18px 'Manrope', sans-serif; color: var(--ink); }
    .contact-detail { padding: 14px 0; border-bottom: 1px solid var(--line); }
    .contact-detail span { display: block; margin-bottom: 6px; color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
    .contact-detail a { color: var(--green-dark); font: 700 17px 'Manrope', sans-serif; }
    .contact-detail a:hover { text-decoration: underline; text-underline-offset: 3px; }
    .contact-hours { margin-top: 18px; color: var(--muted); font-size: 13px; line-height: 1.6; }
    
    .contact-form .fields { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .contact-form .field { display: flex; flex-direction: column; gap: 6px; }
    .contact-form .field.full { grid-column: 1 / -1; }
    .contact-form label { font-size: 13px; font-weight: 600; color: #334155; }
    .contact-form input, .contact-form select { min-height: 44px; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; }
    .contact-form input:focus, .contact-form select:focus, .contact-form textarea:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12); }
    .contact-form textarea { width: 100%; min-height: 90px; padding: 10px 12px; resize: vertical; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font: inherit; }
    .contact-submit { min-height: 46px; margin-top: 10px; padding: 0 20px; border: 0; border-radius: 6px; background: var(--green); color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; transition: background 0.15s; }
    .contact-submit:hover { background: var(--green-dark); }
    .form-status { margin-bottom: 20px; padding: 13px 16px; border-radius: 6px; background: var(--mint); color: var(--green-dark); font-size: 13px; border: 1px solid #a7f3d0; }
    .field-error { color: #dc2626; font-size: 12px; }

    /* Locked Guest Access Card */
    .locked-contact-box {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 36px 24px;
        text-align: center;
    }
    .locked-icon { font-size: 36px; margin-bottom: 12px; }
    .locked-contact-box h3 { font: 700 19px 'Manrope', sans-serif; margin-bottom: 8px; color: var(--ink); }
    .locked-contact-box p { color: var(--muted); font-size: 14px; max-width: 440px; margin: 0 auto 20px; line-height: 1.6; }
    .locked-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-login-prompt { padding: 9px 20px; border-radius: 6px; background: var(--green); color: #fff; font-weight: 700; font-size: 13px; }
    .btn-login-prompt:hover { background: var(--green-dark); }
    .btn-register-prompt { padding: 9px 20px; border-radius: 6px; border: 1px solid var(--line); background: #fff; color: var(--ink); font-weight: 700; font-size: 13px; }
    .btn-register-prompt:hover { background: #f1f5f9; }

    @media(max-width: 760px) {
        .contact-layout { grid-template-columns: 1fr; gap: 24px; padding: 20px; }
        .contact-form .fields { grid-template-columns: 1fr; }
        .contact-form .field.full { grid-column: auto; }
    }
</style>
<main class="contact-main">
    <section class="contact-heading">
        <span class="eyebrow">İletişim & Destek</span>
        <h1>Size nasıl yardımcı olabiliriz?</h1>
        <p>Sorularınız, iş ortaklığı talepleriniz veya rezervasyon ayrıntıları için bize yazın. Ekibimiz en kısa sürede geri dönüş sağlasın.</p>
    </section>

    <div class="contact-layout">
        <aside class="contact-info">
            <h2>İletişim Bilgileri</h2>
            <div class="contact-detail"><span>Telefon</span><a href="tel:+902125550123">+90 (212) 555 01 23</a></div>
            <div class="contact-detail"><span>E-posta</span><a href="mailto:merhaba@entur.com">merhaba@entur.com</a></div>
            <div class="contact-detail"><span>Merkez Ofis</span><div style="font-weight:600;margin-top:4px">Nispetiye Cad. No:24, Beşiktaş, İstanbul</div></div>
            <p class="contact-hours">Müşteri ve organizatör destek hattı<br>Haftanın her günü, 09.00 – 21.00</p>
        </aside>

        <section class="contact-form">
            <h2>Mesajınızı gönderin</h2>

            @if (session('status'))
                <div class="form-status" role="status">✓ {{ session('status') }}</div>
            @endif

            @guest
                <!-- REQUIREMENT: Guest Access Restriction -->
                <div class="locked-contact-box">
                    <div class="locked-icon">🔒</div>
                    <h3>Mesaj Göndermek İçin Giriş Yapmalısınız</h3>
                    <p>
                        Platform yetkililerine veya organizatörlerimize mesaj gönderebilmek ve destek taleplerinizi iletmek için lütfen oturum açın veya kayıt olun.
                    </p>
                    <div class="locked-actions">
                        <a class="btn-login-prompt" href="{{ route('login') }}">Giriş Yap</a>
                        <a class="btn-register-prompt" href="{{ route('register') }}">Kayıt Ol</a>
                    </div>
                </div>
            @else
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="fields">
                        <div class="field">
                            <label for="name">Ad Soyad</label>
                            <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', auth()->user()->name) }}" required readonly style="background:#f8fafc">
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label for="email">E-posta Adresi</label>
                            <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', auth()->user()->email) }}" required readonly style="background:#f8fafc">
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="field full">
                            <label for="phone">Telefon Numarası <span style="font-weight:400;color:#64748b">(İsteğe bağlı)</span></label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="0500 000 00 00">
                            @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        @if ($contactListings->isNotEmpty())
                            <div class="field full">
                                <label for="listing_id">İlgili İlan veya Etkinlik <span style="font-weight:400;color:#64748b">(İsteğe bağlı)</span></label>
                                <select id="listing_id" name="listing_id">
                                    <option value="">Genel İletişim / Platform Destek</option>
                                    @foreach ($contactListings as $listing)
                                        <option value="{{ $listing->id }}" @selected(old('listing_id') == $listing->id)>
                                            {{ $listing->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('listing_id')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        @endif
                        <div class="field full">
                            <label for="message">Mesajınız</label>
                            <textarea id="message" name="message" placeholder="Sorunuzu veya talebinizi detaylı olarak yazabilirsiniz..." required>{{ old('message') }}</textarea>
                            @error('message')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <button class="contact-submit" type="submit">Mesajı Gönder →</button>
                </form>
            @endguest
        </section>
    </div>
</main>
@endsection
