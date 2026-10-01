@extends('anatema')

@section('title', 'İletişim | ENTUR')

@section('content')
<style>
    .contact-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 62px 0 80px; }
    .contact-heading { max-width: 660px; margin-bottom: 34px; }
    .contact-heading h1 { margin: 14px 0 10px; font: 700 40px/1.16 'Manrope', sans-serif; }
    .contact-heading p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.7; }
    .contact-layout { display: grid; grid-template-columns: .8fr 1.2fr; gap: 70px; padding-top: 30px; border-top: 1px solid var(--line); }
    .contact-info h2, .contact-form h2 { margin: 0 0 18px; font: 700 17px 'Manrope', sans-serif; }
    .contact-detail { padding: 17px 0; border-bottom: 1px solid var(--line); }
    .contact-detail span { display: block; margin-bottom: 6px; color: var(--muted); font-size: 12px; }
    .contact-detail a { color: var(--green-dark); font: 600 16px 'Manrope', sans-serif; }
    .contact-detail a:hover { text-decoration: underline; text-underline-offset: 3px; }
    .contact-hours { margin-top: 24px; color: var(--muted); font-size: 13px; line-height: 1.7; }
    .contact-form .fields { gap: 15px; }
    .contact-form textarea { width: 100%; min-height: 150px; padding: 13px 14px; resize: vertical; border: 1px solid #dce4e0; border-radius: 5px; outline: none; color: var(--ink); font: inherit; }
    .contact-form textarea:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgb(23 107 87 / 10%); }
    .contact-form input { min-height: 46px; }
    .contact-submit { min-height: 48px; margin-top: 8px; padding: 0 18px; border: 0; border-radius: 4px; background: var(--green); color: #fff; font-weight: 700; cursor: pointer; }
    .contact-submit:hover { background: var(--green-dark); }
    .form-status { margin-bottom: 20px; padding: 13px 15px; border-left: 3px solid var(--green); background: var(--mint); color: var(--green-dark); font-size: 13px; }
    .field-error { color: #b94335; font-size: 12px; }
    @media(max-width: 700px) { .contact-main { width: min(100% - 32px, 1120px); padding: 40px 0 55px; } .contact-heading h1 { font-size: 34px; } .contact-layout { grid-template-columns: 1fr; gap: 34px; } }
</style>
<main class="contact-main">
    <section class="contact-heading">
        <span class="eyebrow">İletişim</span>
        <h1>Size nasıl yardımcı olabiliriz?</h1>
        <p>Sorunuz veya yolculuk planınız için bize yazın. Ekibimiz mesajınızı inceleyip sizinle iletişime geçsin.</p>
    </section>
    <div class="contact-layout">
        <aside class="contact-info">
            <h2>Bize ulaşın</h2>
            <div class="contact-detail"><span>Telefon</span><a href="tel:+902125550123">+90 (212) 555 01 23</a></div>
            <div class="contact-detail"><span>E-posta</span><a href="mailto:merhaba@entur.com">merhaba@entur.com</a></div>
            <p class="contact-hours">Telefon desteği<br>Hafta içi, 09.00–18.00</p>
        </aside>
        <section class="contact-form">
            <h2>Mesajınızı gönderin</h2>
            @if (session('status'))
                <div class="form-status" role="status">{{ session('status') }}</div>
            @endif
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="fields">
                    <div class="field">
                        <label for="name">Ad soyad</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field">
                        <label for="email">E-posta adresi</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field full">
                        <label for="phone">Telefon numarası <span style="font-weight:400;color:#87938e">(isteğe bağlı)</span></label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}">
                        @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field full">
                        <label for="message">Mesajınız</label>
                        <textarea id="message" name="message" placeholder="Size nasıl yardımcı olabiliriz?" required>{{ old('message') }}</textarea>
                        @error('message')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <button class="contact-submit" type="submit">Mesajı gönder <span aria-hidden="true">→</span></button>
            </form>
        </section>
    </div>
</main>
@endsection
