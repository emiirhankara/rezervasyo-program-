@extends('anatema')

@section('title', 'Rezervasyon | Anatema')

@section('content')
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Anatema ana sayfa">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M4 18 12 5l8 13M7 13h10M9.5 18l2.5-4 2.5 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        ENTUR
        </a>
        <div class="header-note">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="1.7"/></svg>
            <span>Konaklamanızı kolayca planlayın</span>
        </div>
    </div>
</header>

<main>
    <section class="intro">
        <span class="eyebrow">Rezervasyon</span>
        <h1>Bir sonraki konaklamanızı planlayın.</h1>
        <p>Konaklama bilgilerinizi girin, size uygun seçenekleri birlikte oluşturalım.</p>
    </section>

    <div class="booking-layout">
        <form id="reservation-form">
            <section class="form-section">
                <div class="section-heading"><span class="step">1</span><h2>Konaklama bilgileri</h2></div>
                <div class="fields">
                    <div class="field full">
                        <label for="destination">Şehir veya konaklama yeri</label>
                        <input id="destination" name="destination" type="text" placeholder="Örn. İstanbul" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <label for="checkin">Giriş tarihi</label>
                        <input id="checkin" name="checkin" type="date" required>
                    </div>
                    <div class="field">
                        <label for="checkout">Çıkış tarihi</label>
                        <input id="checkout" name="checkout" type="date" required>
                    </div>
                    <div class="field">
                        <label for="guests">Misafir sayısı</label>
                        <select id="guests" name="guests" required>
                            <option value="1">1 misafir</option>
                            <option value="2" selected>2 misafir</option>
                            <option value="3">3 misafir</option>
                            <option value="4">4 misafir</option>
                            <option value="5">5 misafir</option>
                            <option value="6">6 misafir</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="room">Oda tercihi</label>
                        <select id="room" name="room" required>
                            <option value="Standart oda" selected>Standart oda</option>
                            <option value="Deluxe oda">Deluxe oda</option>
                            <option value="Aile odası">Aile odası</option>
                            <option value="Suit oda">Suit oda</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="section-heading"><span class="step">2</span><h2>İletişim bilgileriniz</h2></div>
                <div class="fields">
                    <div class="field">
                        <label for="name">Ad soyad</label>
                        <input id="name" name="name" type="text" placeholder="Adınız ve soyadınız" autocomplete="name" required>
                    </div>
                    <div class="field">
                        <label for="email">E-posta adresi</label>
                        <input id="email" name="email" type="email" placeholder="ornek@eposta.com" autocomplete="email" required>
                    </div>
                    <div class="field full">
                        <label for="phone">Telefon numarası</label>
                        <input id="phone" name="phone" type="tel" placeholder="05XX XXX XX XX" autocomplete="tel" required>
                    </div>
                </div>
            </section>
        </form>

        <aside class="aside" aria-label="Rezervasyon özeti">
            <div class="aside-top">
                <div><div class="aside-kicker">Rezervasyon özeti</div><div class="aside-title">Konaklama detayları</div></div>
                <span class="badge">Ücretsiz iptal</span>
            </div>
            <div class="summary-list">
                <div class="summary-row"><span>Konum</span><strong id="summary-destination">Henüz seçilmedi</strong></div>
                <div class="summary-row"><span>Tarihler</span><strong id="summary-dates">Tarih seçilmedi</strong></div>
                <div class="summary-row"><span>Misafir</span><strong id="summary-guests">2 misafir</strong></div>
                <div class="summary-row"><span>Oda</span><strong id="summary-room">Standart oda</strong></div>
            </div>
            <div class="summary-total"><span>Fiyat bilgisi</span><strong>Teklif alın</strong></div>
            <p class="summary-caption">Konaklama ücretiniz, seçtiğiniz yere ve tarihlere göre belirlenir.</p>
            <button class="submit-button" type="submit" form="reservation-form">
                Rezervasyon talebi gönder
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="secure-note">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6"/></svg>
                Bilgileriniz güvende
            </div>
            <div id="form-notice" class="notice" role="status"></div>
        </aside>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-inner"><span>© {{ date('Y') }} Anatema</span><span>Konaklamanız için buradayız.</span></div>
</footer>

<script>
    const form = document.getElementById('reservation-form');
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');
    const today = new Date().toISOString().slice(0, 10);
    checkin.min = today;
    checkout.min = today;

    checkin.addEventListener('change', () => {
        checkout.min = checkin.value || today;
        if (checkout.value && checkout.value <= checkin.value) checkout.value = '';
        updateSummary();
    });

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        const notice = document.getElementById('form-notice');
        notice.textContent = 'Rezervasyon talebiniz hazır. Talebiniz alındığında sizinle iletişime geçeceğiz.';
        notice.classList.add('visible');
    });

    function updateSummary() {
        const destination = document.getElementById('destination').value.trim();
        const guestCount = document.getElementById('guests').value;
        const room = document.getElementById('room').value;
        document.getElementById('summary-destination').textContent = destination || 'Henüz seçilmedi';
        document.getElementById('summary-guests').textContent = `${guestCount} misafir`;
        document.getElementById('summary-room').textContent = room;

        if (checkin.value && checkout.value) {
            const formatDate = (value) => new Date(`${value}T00:00:00`).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' });
            document.getElementById('summary-dates').textContent = `${formatDate(checkin.value)} - ${formatDate(checkout.value)}`;
        } else {
            document.getElementById('summary-dates').textContent = 'Tarih seçilmedi';
        }
    }
</script>
@endsection