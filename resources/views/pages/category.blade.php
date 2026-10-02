@extends('anatema')

@section('title', $category['title'] . ' Rezervasyonu | ENTUR')

@section('content')
<style>
    .category-main { width: min(1180px, calc(100% - 48px)); margin: 0 auto; padding: 50px 0 80px; }
    .category-heading { max-width: 720px; margin-bottom: 32px; }
    .category-heading h1 { margin: 14px 0 10px; font: 800 38px/1.2 'Manrope', sans-serif; color: var(--ink); }
    .category-heading p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.7; }
    
    .offer-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; margin-bottom: 50px; }
    .offer-card { border: 1px solid var(--line); border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; }
    .offer-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.08); }
    .offer-card img { width: 100%; height: 210px; display: block; object-fit: cover; }
    .offer-content { padding: 20px; display: flex; flex-direction: column; flex: 1; }
    .offer-location { color: var(--green); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .offer-content h2 { margin: 8px 0; font: 700 18px 'Manrope', sans-serif; color: var(--ink); }
    .offer-content p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.6; flex: 1; }
    .offer-bottom { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line); }
    .offer-price { color: var(--green-dark); font-size: 14px; font-weight: 800; font-family: 'Manrope', sans-serif; }
    .offer-select { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 6px; background: var(--mint); color: var(--green-dark); font-size: 12px; font-weight: 700; transition: background 0.15s; }
    .offer-select:hover { background: #d1fae5; }

    /* Reservation Booking Section */
    .reservation-section { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 40px; align-items: start; padding-top: 40px; border-top: 2px solid var(--line); }
    .section-title { font: 800 26px 'Manrope', sans-serif; color: var(--ink); margin-bottom: 6px; }
    .section-subtitle { color: var(--muted); font-size: 14px; margin-bottom: 24px; line-height: 1.5; }
    
    .form-status { margin: 0 0 20px; padding: 14px 18px; border-radius: 8px; font-size: 13px; line-height: 1.5; }
    .form-status.success { background: var(--mint); border: 1px solid #a7f3d0; color: var(--green-dark); }
    .form-status.error { background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; }

    /* Locked Guest Access Card */
    .locked-guest-box {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 40px 30px;
        text-align: center;
        margin-bottom: 30px;
    }
    .locked-icon { font-size: 38px; margin-bottom: 12px; }
    .locked-guest-box h3 { font: 700 20px 'Manrope', sans-serif; margin-bottom: 8px; color: var(--ink); }
    .locked-guest-box p { color: var(--muted); font-size: 14px; max-width: 500px; margin: 0 auto 22px; line-height: 1.6; }
    .locked-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-login-prompt { padding: 10px 22px; border-radius: 8px; background: var(--green); color: #fff; font-weight: 700; font-size: 14px; }
    .btn-login-prompt:hover { background: var(--green-dark); }
    .btn-register-prompt { padding: 10px 22px; border-radius: 8px; border: 1px solid var(--line); background: #fff; color: var(--ink); font-weight: 700; font-size: 14px; }
    .btn-register-prompt:hover { background: #f1f5f9; }

    /* Booking Form Fields */
    .booking-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .booking-field { display: flex; flex-direction: column; gap: 7px; }
    .booking-field.full { grid-column: 1 / -1; }
    .booking-field label { color: #334155; font-size: 13px; font-weight: 600; }
    .booking-field input, .booking-field select, .booking-field textarea { width: 100%; min-height: 46px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff; transition: border-color 0.15s, box-shadow 0.15s; }
    .booking-field input:focus, .booking-field select:focus, .booking-field textarea:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15); }
    .booking-field textarea { min-height: 80px; resize: vertical; }

    /* Payment Tabs Section */
    .payment-options-wrap { grid-column: 1 / -1; margin-top: 10px; padding: 22px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fafbfc; }
    .payment-tabs-header { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 18px; }
    .pay-tab-btn { padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; text-align: center; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .pay-tab-btn.active { border-color: var(--green); background: var(--mint); color: var(--green-dark); box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.1); }
    
    .pay-pane { display: none; }
    .pay-pane.active { display: block; animation: fadeIn 0.2s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

    /* Credit Card Mockup & Inputs */
    .card-inputs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
    .card-preview-box { background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 12px; padding: 20px; color: #fff; margin-bottom: 16px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15); position: relative; }
    .card-chip { width: 36px; height: 26px; background: linear-gradient(135deg, #fbbf24, #d97706); border-radius: 5px; margin-bottom: 14px; }
    .card-mock-number { font-family: monospace; font-size: 17px; letter-spacing: 2px; margin-bottom: 14px; color: #f8fafc; }
    .card-mock-bottom { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; text-transform: uppercase; }

    /* Bank Transfer Box */
    .bank-info-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 14px; font-size: 13px; line-height: 1.6; }
    .bank-info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; }
    .bank-info-row:last-child { border-bottom: 0; }
    .bank-info-label { color: var(--muted); }
    .bank-info-val { font-weight: 700; color: var(--ink); }

    /* Aside Summary */
    .booking-aside { position: sticky; top: 24px; padding: 24px; border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
    .aside-heading { font: 700 17px 'Manrope', sans-serif; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
    .aside-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--muted); margin-bottom: 10px; }
    .aside-row strong { color: var(--ink); }
    .aside-total { display: flex; justify-content: space-between; margin-top: 14px; padding-top: 14px; border-top: 2px dashed var(--line); font-size: 16px; font-weight: 800; font-family: 'Manrope', sans-serif; }
    .aside-total strong { color: var(--green-dark); font-size: 22px; }

    .btn-submit-booking { width: 100%; min-height: 50px; border: 0; border-radius: 8px; background: var(--green); color: #fff; font-family: 'Manrope', sans-serif; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 18px; transition: background 0.15s, transform 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-submit-booking:hover { background: var(--green-dark); transform: translateY(-1px); }

    @media (max-width: 900px) {
        .offer-grid { grid-template-columns: 1fr 1fr; }
        .reservation-section { grid-template-columns: 1fr; }
        .booking-aside { position: static; }
    }
    @media (max-width: 600px) {
        .offer-grid { grid-template-columns: 1fr; }
        .booking-form { grid-template-columns: 1fr; }
        .payment-tabs-header { grid-template-columns: 1fr; }
        .card-inputs-grid { grid-template-columns: 1fr; }
    }
</style>

<main class="category-main">
    <section class="category-heading">
        <span class="eyebrow">{{ $category['eyebrow'] }}</span>
        <h1>{{ $category['title'] }} Seçenekleri</h1>
        <p>{{ $category['description'] }} Beğendiğiniz seçeneği belirleyin, online ödeme adımını tamamlayarak yerinizi anında ayırtın.</p>
    </section>

    <!-- Listings Grid -->
    <section class="offer-grid" aria-label="{{ $category['title'] }} seçenekleri">
        @forelse ($category['items'] as $item)
            @php($imageUrl = !empty($item['image_url']) ? $item['image_url'] : (!empty($item['image']) ? 'https://images.unsplash.com/'.$item['image'].'?auto=format&fit=crop&w=900&q=80' : 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=80'))
            <article class="offer-card">
                <img src="{{ $imageUrl }}" alt="{{ $item['name'] }} - {{ $item['location'] }}" loading="lazy">
                <div class="offer-content">
                    <span class="offer-location">📍 {{ $item['location'] }}</span>
                    <h2>{{ $item['name'] }}</h2>
                    <p>{{ $item['description'] }}</p>
                    <div class="offer-bottom">
                        <span class="offer-price">{{ $item['price'] }}</span>
                        <a class="offer-select" href="{{ route('categories.show', ['category' => $categoryKey, 'secim' => $item['slug']]) }}#request-form">
                            Seç & Rezerve Et →
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: #f8fafc; border-radius: 12px; color: var(--muted)">
                Bu kategoride henüz yayınlanmış seçenek bulunmuyor.
            </div>
        @endforelse
    </section>

    <!-- Reservation Booking Section -->
    <section class="reservation-section" id="request-form">
        <div>
            <h2 class="section-title">Rezervasyon & Güvenli Ödeme</h2>
            <p class="section-subtitle">Tarihlerinizi belirleyin, ödeme yönteminizi seçin ve rezervasyonunuzu anında tamamlayın.</p>

            @if (session('status'))
                <div class="form-status success" role="status">✓ {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="form-status error" role="alert">⚠️ {{ $errors->first() }}</div>
            @endif

            @guest
                <!-- REQUIREMENT: Guest Access Restriction -->
                <div class="locked-guest-box">
                    <div class="locked-icon">🔒</div>
                    <h3>Rezervasyon Yapmak İçin Giriş Yapmalısınız</h3>
                    <p>
                        Seçtiğiniz etkinlik veya otel için kontenjan ayırtmak, rezervasyonunuzu oluşturmak ve ödeme adımını tamamlamak için lütfen oturum açın veya birkaç saniyede ücretsiz hesap oluşturun.
                    </p>
                    <div class="locked-actions">
                        <a class="btn-login-prompt" href="{{ route('login') }}">Giriş Yap</a>
                        <a class="btn-register-prompt" href="{{ route('register') }}">Ücretsiz Kayıt Ol</a>
                    </div>
                </div>
            @else
                @if (auth()->user()->role !== 'customer')
                    <div class="alert-box info" style="margin-bottom:24px">
                        <div>
                            <strong>Yönetici Hesabıyla Giriş Yapılmış</strong>
                            <p style="margin-top:4px;font-size:12px">Rezervasyon oluşturma alanı müşteri hesapları için tasarlanmıştır. Yönetim panelinize gitmek için <a href="{{ auth()->user()->role === 'system_admin' ? route('system.dashboard') : route('organizer.dashboard') }}" style="text-decoration:underline;font-weight:700">buraya tıklayın</a>.</p>
                        </div>
                    </div>
                @endif

                <form class="booking-form" action="{{ route('reservations.store', $categoryKey) }}" method="POST">
                    @csrf
                    
                    <div class="booking-field full">
                        <label for="item">Seçilen Hizmet / İlan</label>
                        <select id="item" name="item" required onchange="updateSummaryPrice()">
                            <option value="">Bir seçenek belirleyin</option>
                            @foreach ($category['items'] as $item)
                                <option value="{{ $item['slug'] }}" data-amount="{{ $item['amount'] }}" data-name="{{ $item['name'] }}" @selected(old('item', $selectedItem['slug'] ?? '') === $item['slug'])>
                                    {{ $item['name'] }} · {{ $item['location'] }} ({{ $item['price'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="booking-field">
                        <label for="start_date">Başlangıç Tarihi</label>
                        <input id="start_date" name="start_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('start_date', now()->addDays(2)->toDateString()) }}" required onchange="updateSummaryPrice()">
                    </div>

                    <div class="booking-field">
                        <label for="end_date">Bitiş Tarihi (Konaklama İçin)</label>
                        <input id="end_date" name="end_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('end_date') }}" onchange="updateSummaryPrice()">
                    </div>

                    <div class="booking-field">
                        <label for="guests">Misafir / Bilet Adedi</label>
                        <input id="guests" name="guests" type="number" min="1" max="20" value="{{ old('guests', 2) }}" required onchange="updateSummaryPrice()">
                    </div>

                    <div class="booking-field">
                        <label for="name">Ad Soyad</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name', auth()->user()->name) }}" required readonly style="background:#f8fafc">
                    </div>

                    <div class="booking-field">
                        <label for="email">E-posta Adresi</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', auth()->user()->email) }}" required readonly style="background:#f8fafc">
                    </div>

                    <div class="booking-field">
                        <label for="phone">Telefon Numarası</label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', auth()->user()->phone ?? '') }}" required placeholder="0500 000 00 00">
                    </div>

                    <div class="booking-field full">
                        <label for="message">Özel İstek veya Not (İsteğe Bağlı)</label>
                        <textarea id="message" name="message" placeholder="Örn: Sessiz oda tercihimdir, vejetaryen menü rica ediyorum">{{ old('message') }}</textarea>
                    </div>

                    <!-- Payment Section -->
                    <div class="payment-options-wrap">
                        <label style="display:block;font-size:14px;font-weight:700;color:var(--ink);margin-bottom:12px">
                            💳 Güvenli Ödeme Yöntemi
                        </label>

                        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="{{ old('payment_method', 'credit_card') }}">

                        <div class="payment-tabs-header" role="tablist">
                            <button type="button" class="pay-tab-btn {{ old('payment_method', 'credit_card') === 'credit_card' ? 'active' : '' }}" id="tabBtnCard" onclick="switchPaymentTab('credit_card')">
                                <span>💳 Kredi / Banka Kartı</span>
                            </button>
                            <button type="button" class="pay-tab-btn {{ old('payment_method') === 'bank_transfer' ? 'active' : '' }}" id="tabBtnTransfer" onclick="switchPaymentTab('bank_transfer')">
                                <span>🏦 Havale / EFT</span>
                            </button>
                            <button type="button" class="pay-tab-btn {{ old('payment_method') === 'coupon' ? 'active' : '' }}" id="tabBtnCoupon" onclick="switchPaymentTab('coupon')">
                                <span>🎟️ Kupon ile Ödeme</span>
                            </button>
                        </div>

                        <!-- 1. Credit Card Pane -->
                        <div class="pay-pane {{ old('payment_method', 'credit_card') === 'credit_card' ? 'active' : '' }}" id="paneCard">
                            <div class="card-preview-box">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                                    <div class="card-chip"></div>
                                    <span style="font-weight:800;font-size:15px;letter-spacing:1px">ENTUR PAY</span>
                                </div>
                                <div class="card-mock-number" id="mockCardNumber">•••• •••• •••• ••••</div>
                                <div class="card-mock-bottom">
                                    <div>
                                        <div style="font-size:9px">KART SAHİBİ</div>
                                        <div style="color:#fff;font-weight:700;font-size:13px" id="mockCardHolder">{{ strtoupper(auth()->user()->name) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px">SON KULLANMA</div>
                                        <div style="color:#fff;font-weight:700;font-size:13px" id="mockCardExpiry">12/28</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-inputs-grid">
                                <div class="booking-field full">
                                    <label for="card_holder">Kart Üzerindeki İsim</label>
                                    <input id="card_holder" name="card_holder" value="{{ old('card_holder', auth()->user()->name) }}" placeholder="Kartın ön yüzündeki isim" oninput="document.getElementById('mockCardHolder').textContent = this.value.toUpperCase() || 'AD SOYAD'">
                                </div>
                                <div class="booking-field full">
                                    <label for="card_number">Kart Numarası</label>
                                    <input id="card_number" name="card_number" maxlength="19" value="{{ old('card_number') }}" placeholder="0000 0000 0000 0000" oninput="formatCardNumber(this)">
                                </div>
                                <div class="booking-field">
                                    <label for="card_expiry">Son Kullanma Tarihi</label>
                                    <input id="card_expiry" name="card_expiry" maxlength="5" value="{{ old('card_expiry') }}" placeholder="AA/YY" oninput="formatExpiry(this)">
                                </div>
                                <div class="booking-field">
                                    <label for="card_cvv">CVV Güvenlik Kodu</label>
                                    <input id="card_cvv" name="card_cvv" maxlength="4" type="password" value="{{ old('card_cvv') }}" placeholder="•••">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Bank Transfer Pane -->
                        <div class="pay-pane {{ old('payment_method') === 'bank_transfer' ? 'active' : '' }}" id="paneTransfer">
                            <div class="bank-info-box">
                                <div class="bank-info-row">
                                    <span class="bank-info-label">Banka:</span>
                                    <span class="bank-info-val">T.C. Ziraat Bankası</span>
                                </div>
                                <div class="bank-info-row">
                                    <span class="bank-info-label">Alıcı Ünvanı:</span>
                                    <span class="bank-info-val">ENTUR Turizm & Rezervasyon A.Ş.</span>
                                </div>
                                <div class="bank-info-row">
                                    <span class="bank-info-label">IBAN:</span>
                                    <span class="bank-info-val" style="font-family:monospace;font-size:13px">TR56 0006 2000 0001 2345 6789 01</span>
                                </div>
                                <div class="bank-info-row">
                                    <span class="bank-info-label">Açıklama:</span>
                                    <span class="bank-info-val">{{ auth()->user()->name }} - Rezervasyon</span>
                                </div>
                            </div>
                            <div class="card-inputs-grid">
                                <div class="booking-field">
                                    <label for="sender_name">Havale Gönderen Adı Soyadı</label>
                                    <input id="sender_name" name="sender_name" value="{{ old('sender_name', auth()->user()->name) }}" placeholder="Hesap sahibinin adı">
                                </div>
                                <div class="booking-field">
                                    <label for="transfer_reference">Dekont / İşlem No (İsteğe Bağlı)</label>
                                    <input id="transfer_reference" name="transfer_reference" value="{{ old('transfer_reference') }}" placeholder="Örn: 20261002-8921">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Coupon Pane -->
                        <div class="pay-pane {{ old('payment_method') === 'coupon' ? 'active' : '' }}" id="paneCoupon">
                            <div style="background:#f1f5f9;border-radius:8px;padding:14px;font-size:13px;color:var(--ink-soft);margin-bottom:12px">
                                Elinizde tam değerde bir promosyon veya hediye kuponu varsa kodu girerek rezervasyonunuzu ücretsiz tamamlayabilirsiniz.
                            </div>
                            <div class="booking-field">
                                <label for="coupon_code">Kupon Kodu</label>
                                <input id="coupon_code" name="coupon_code" type="text" value="{{ old('coupon_code') }}" placeholder="Örn: UCRETSIZ veya HOSGELDIN" style="text-transform:uppercase">
                            </div>
                        </div>

                        <!-- Discount Coupon applied to any method -->
                        <div style="margin-top:14px;padding-top:12px;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:10px" id="generalCouponWrap">
                            <span style="font-size:12px;color:var(--muted)">İndirim kuponunuz mu var?</span>
                            <input style="max-width:180px;min-height:36px;padding:5px 10px;font-size:12px;border:1px solid var(--line);border-radius:6px;text-transform:uppercase" placeholder="Kupon Kodu" id="sideCouponInput" oninput="syncCoupon(this.value)">
                        </div>
                    </div>

                    <button class="btn-submit-booking" type="submit" id="submitBookingBtn">
                        <span>Rezervasyonu & Ödemeyi Tamamla</span>
                        <span aria-hidden="true">→</span>
                    </button>
                </form>
            @endguest
        </div>

        <!-- Aside Summary -->
        <aside class="booking-aside">
            <h3 class="aside-heading">Sipariş Özeti</h3>
            <div class="aside-row">
                <span>Kategori</span>
                <strong id="summaryCategory">{{ $category['title'] }}</strong>
            </div>
            <div class="aside-row">
                <span>Seçim</span>
                <strong id="summaryItemName">{{ $selectedItem['name'] ?? 'Henüz seçilmedi' }}</strong>
            </div>
            <div class="aside-row">
                <span>Birim Tutar</span>
                <strong id="summaryUnitAmount">{{ $selectedItem['price'] ?? '–' }}</strong>
            </div>
            <div class="aside-row">
                <span>Bilet / Misafir</span>
                <strong id="summaryGuests">2 Kişi</strong>
            </div>
            <div class="aside-total">
                <span>Toplam Tutar</span>
                <strong id="summaryTotal">₺0,00</strong>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:10px;line-height:1.5">
                🔒 Tüm ödemeler 256-bit SSL güvenlik protokolü ile korunmakta ve veritabanına kayıt altına alınmaktadır.
            </div>
        </aside>
    </section>
</main>

<script>
    function switchPaymentTab(method) {
        document.getElementById('selectedPaymentMethod').value = method;
        
        document.getElementById('tabBtnCard').classList.toggle('active', method === 'credit_card');
        document.getElementById('tabBtnTransfer').classList.toggle('active', method === 'bank_transfer');
        document.getElementById('tabBtnCoupon').classList.toggle('active', method === 'coupon');

        document.getElementById('paneCard').classList.toggle('active', method === 'credit_card');
        document.getElementById('paneTransfer').classList.toggle('active', method === 'bank_transfer');
        document.getElementById('paneCoupon').classList.toggle('active', method === 'coupon');

        const btnText = document.querySelector('#submitBookingBtn span');
        if (method === 'credit_card') btnText.textContent = 'Kartla Öde & Rezervasyonu Kesinleştir';
        else if (method === 'bank_transfer') btnText.textContent = 'Havale Bildirimi & Rezervasyon Oluştur';
        else btnText.textContent = 'Kuponla Rezervasyonu Onayla';
    }

    function formatCardNumber(input) {
        let val = input.value.replace(/\D/g, '').substring(0, 16);
        let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
        input.value = formatted;
        document.getElementById('mockCardNumber').textContent = formatted || '•••• •••• •••• ••••';
    }

    function formatExpiry(input) {
        let val = input.value.replace(/\D/g, '').substring(0, 4);
        if (val.length >= 3) {
            val = val.substring(0, 2) + '/' + val.substring(2);
        }
        input.value = val;
        document.getElementById('mockCardExpiry').textContent = val || '12/28';
    }

    function syncCoupon(val) {
        const couponInput = document.getElementById('coupon_code');
        if (couponInput) couponInput.value = val.toUpperCase();
    }

    function updateSummaryPrice() {
        const itemSelect = document.getElementById('item');
        if (!itemSelect) return;
        const selected = itemSelect.options[itemSelect.selectedIndex];
        const guestsInput = document.getElementById('guests');
        const guests = guestsInput ? parseInt(guestsInput.value) || 1 : 1;

        document.getElementById('summaryGuests').textContent = guests + ' Kişi';

        if (selected && selected.value) {
            const unitAmount = parseFloat(selected.dataset.amount) || 0;
            const name = selected.dataset.name || selected.text;
            document.getElementById('summaryItemName').textContent = name;
            document.getElementById('summaryUnitAmount').textContent = '₺' + unitAmount.toLocaleString('tr-TR', {minimumFractionDigits: 2});
            
            const total = unitAmount * guests;
            document.getElementById('summaryTotal').textContent = '₺' + total.toLocaleString('tr-TR', {minimumFractionDigits: 2});
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSummaryPrice();
    });
</script>
@endsection
