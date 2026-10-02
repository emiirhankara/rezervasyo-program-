@extends('admin.layout')

@section('title', 'Organizatör Paneli | ENTUR')

@section('content')
<main class="admin-container">
    <div class="dashboard-header">
        <div>
            <h1>Organizatör Yönetim Paneli</h1>
            <p>Etkinlik ve konaklama ilanlarınızı yönetin, kontenjan ve fiyat belirleyin, katılımcıları ve bilet satışlarınızı takip edin.</p>
        </div>
        <nav class="period-selector" aria-label="Raporlama Dönemi">
            <a class="period-btn {{ $period === 'day' ? 'active' : '' }}" href="?period=day">Bugün (Günlük)</a>
            <a class="period-btn {{ $period === 'month' ? 'active' : '' }}" href="?period=month">Bu Ay (Aylık)</a>
            <a class="period-btn {{ $period === 'year' ? 'active' : '' }}" href="?period=year">Bu Yıl (Yıllık)</a>
        </nav>
    </div>

    <!-- Annual Membership Fee Status Banner -->
    @if ($fee)
        <div class="alert-box {{ $fee->status === 'paid' ? 'success' : 'info' }}" style="margin-bottom:24px">
            <div style="flex:1">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                    <strong>
                        {{ now()->year }} Yılı Platform Üyeliği: ₺{{ number_format($fee->annual_amount, 2, ',', '.') }}
                        <span class="status-badge {{ $fee->status === 'paid' ? 'paid' : 'pending' }}" style="margin-left:8px">
                            {{ $fee->status === 'paid' ? '✓ Üyelik Aktif (Ödendi)' : 'Taksit Ödemeleri Devam Ediyor' }}
                        </span>
                    </strong>
                    <span style="font-size:12px;color:var(--muted)">
                        {{ $installments->where('status', 'paid')->count() }} / {{ $installments->count() }} Taksit Tamamlandı
                    </span>
                </div>
                @if ($fee->status !== 'paid')
                    <p style="margin-top:6px;font-size:12px">
                        Yeni oluşturduğunuz ilanlar sistem yöneticisi onayının ardından yayınlanacaktır. Taksitlerinizi vadesinde ödemeyi unutmayın.
                    </p>
                @endif
            </div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Bilet / Satış Cirosu</span>
                <div class="metric-icon-wrap" style="color:var(--green);background:var(--green-light)">💰</div>
            </div>
            <div class="metric-val">₺{{ number_format($revenue, 2, ',', '.') }}</div>
            <div class="metric-meta">{{ $period === 'day' ? 'Bugünkü' : ($period === 'year' ? 'Bu yılki' : 'Bu ayki') }} bilet ve rezervasyon geliri</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Operasyon Giderleri</span>
                <div class="metric-icon-wrap" style="color:var(--amber);background:var(--amber-light)">📉</div>
            </div>
            <div class="metric-val">₺{{ number_format($expenses, 2, ',', '.') }}</div>
            <div class="metric-meta">Seçili dönemde kaydettiğiniz giderler</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Net Kazanç</span>
                <div class="metric-icon-wrap" style="color:var(--primary);background:var(--primary-light)">📈</div>
            </div>
            <div class="metric-val">₺{{ number_format($net, 2, ',', '.') }}</div>
            <div class="metric-meta">Ciro - Giderler</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Satılan Bilet / Katılımcı</span>
                <div class="metric-icon-wrap">🎟️</div>
            </div>
            <div class="metric-val">{{ $ticketsSold }}</div>
            <div class="metric-meta">{{ $reservationCount }} rezervasyonda kayıtlı misafir</div>
        </div>
    </div>

    <!-- CORE REQUIREMENT: Add Listing & Define Branch / Category -->
    <section class="admin-card">
        <div class="card-header">
            <h2>
                <span>✨ Yeni Etkinlik, Otel veya Özel Branş Tanımla</span>
            </h2>
            <span style="font-size:12px;color:var(--muted)">Kendi branşınızı oluşturun, bilet fiyatı ve kontenjan belirleyin</span>
        </div>
        <div class="card-body">
            <form class="form-grid" action="{{ route('organizer.listings.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="listingCategory">Branş / Kategori</label>
                    <select class="form-control" id="listingCategory" name="category" required onchange="handleCategoryChange(this.value)">
                        <option value="etkinlikler">Etkinlikler (Konser, Atölye, Festival, vb.)</option>
                        <option value="oteller">Oteller & Butik Konaklama</option>
                        <option value="kiralik-villalar">Kiralık Villalar & Tatil Evleri</option>
                        <option value="diger">Diğer Deneyimler</option>
                        <option value="custom">✨ Yeni Özel Branş Tanımla...</option>
                    </select>
                </div>

                <div class="form-group" id="customCategoryWrapper" style="display:none">
                    <label for="customCategory">Yeni Özel Branş Adı</label>
                    <input class="form-control" id="customCategory" name="custom_category" placeholder="Örn: Yat & Tekne Turu, Doğa Kampı, Yoga İnzivası">
                </div>

                <div class="form-group">
                    <label for="listingTitle">İlan / Hizmet Başlığı</label>
                    <input class="form-control" id="listingTitle" name="title" required maxlength="180" placeholder="Örn: Boğaz Gün Batımı Caz Konseri">
                </div>

                <div class="form-group">
                    <label for="listingLocation">Konum / Şehir</label>
                    <input class="form-control" id="listingLocation" name="location" required maxlength="180" placeholder="Örn: Beşiktaş, İstanbul">
                </div>

                <div class="form-group">
                    <label for="listingPrice">Bilet / Gecelik Fiyatı (₺)</label>
                    <input class="form-control" id="listingPrice" name="price" type="number" min="0.01" step="0.01" required placeholder="1250">
                </div>

                <div class="form-group">
                    <label for="listingCapacity">Toplam Kontenjan (Kişi Sayısı)</label>
                    <input class="form-control" id="listingCapacity" name="capacity" type="number" min="1" required placeholder="50">
                </div>

                <div class="form-group full">
                    <label for="listingImage">Tanıtım Görseli URL (İsteğe Bağlı)</label>
                    <input class="form-control" id="listingImage" name="image_url" type="url" maxlength="500" placeholder="https://images.unsplash.com/photo-...">
                </div>

                <div class="form-group full">
                    <label for="listingDesc">Açıklama & Detaylar</label>
                    <textarea class="form-control" id="listingDesc" name="description" required maxlength="5000" placeholder="Etkinlik programı, konaklama imkanları, dahil olan hizmetler vb."></textarea>
                </div>

                <div class="form-group full" style="margin-top:6px">
                    <button class="btn btn-teal" type="submit" style="width:100%">
                        İlanı & Branşı Kaydet (Onaya Gönder)
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- CORE REQUIREMENT: Listings with Capacity & Price Management -->
    <section class="admin-card">
        <div class="card-header">
            <h2>
                <span>📋 İlanlarım, Bilet Satışları & Kontenjan Yönetimi</span>
                <span class="card-header-badge">{{ $listings->count() }} İlan</span>
            </h2>
            <div style="font-size:12px;color:var(--muted)">Kontenjan doluluk oranlarını ve bilet hasılatını inceleyin, fiyatları anında güncelleyin</div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>İlan</th>
                        <th>Branş</th>
                        <th>Kontenjan & Doluluk</th>
                        <th>Toplam Bilet Hasılatı</th>
                        <th>Fiyat / Kontenjan Düzenle</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listings as $listing)
                        <tr>
                            <td>
                                <strong style="font-size:14px;color:var(--ink)">{{ $listing->title }}</strong>
                                <div style="font-size:11px;color:var(--muted)">📍 {{ $listing->location }}</div>
                            </td>
                            <td>
                                <span class="status-badge" style="background:#f1f5f9;color:var(--ink)">
                                    {{ ucfirst(str_replace('-', ' ', $listing->category)) }}
                                </span>
                            </td>
                            <td style="min-width:180px">
                                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                                    <strong>{{ $listing->reserved_count }} Satıldı</strong>
                                    <span style="color:var(--muted)">Kalan: {{ max(0, $listing->capacity - $listing->reserved_count) }}</span>
                                </div>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill" style="width: {{ $listing->capacity > 0 ? min(100, round(($listing->reserved_count / $listing->capacity) * 100)) : 0 }}%"></div>
                                </div>
                                <div style="font-size:10px;color:var(--muted);margin-top:3px">Toplam Kapasite: {{ $listing->capacity }} kişi</div>
                            </td>
                            <td>
                                <strong style="color:var(--primary);font-size:14px">₺{{ number_format($listing->total_revenue, 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <form action="{{ route('organizer.listings.update', $listing->id) }}" method="POST" style="display:flex;gap:6px;align-items:center">
                                    @csrf
                                    <div style="display:flex;flex-direction:column;gap:2px">
                                        <input class="form-control" style="width:90px;min-height:34px;padding:4px 8px;font-size:12px" name="price" type="number" min="0.01" step="0.01" value="{{ $listing->price }}" title="Birim Fiyat (₺)">
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:2px">
                                        <input class="form-control" style="width:75px;min-height:34px;padding:4px 8px;font-size:12px" name="capacity" type="number" min="{{ max(1, $listing->reserved_count) }}" value="{{ $listing->capacity }}" title="Kontenjan">
                                    </div>
                                    <button class="btn btn-teal btn-sm" type="submit">Güncelle</button>
                                </form>
                            </td>
                            <td>
                                <span class="status-badge {{ $listing->is_published ? 'confirmed' : 'pending' }}">
                                    {{ $listing->is_published ? '✓ Yayında' : 'Onay Bekliyor' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">Henüz oluşturulmuş bir ilanınız yok. Yukarıdaki formdan ilk ilanınızı yayınlayabilirsiniz.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- CORE REQUIREMENT: Attendees & Reservations -->
    <section class="admin-card">
        <div class="card-header">
            <h2>
                <span>🎟️ Gelen Rezervasyonlar & Katılımcı Listesi</span>
                <span class="card-header-badge">{{ $reservations->count() }} Katılımcı</span>
            </h2>
            <div style="font-size:12px;color:var(--muted)">Katılımcı isimleri, iletişim bilgileri, bilet adetleri ve ödeme durumları</div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hizmet / Etkinlik</th>
                        <th>Katılımcı Ad Soyad</th>
                        <th>İletişim Bilgileri</th>
                        <th>Tarih</th>
                        <th>Bilet / Kişi</th>
                        <th>Toplam Tutar</th>
                        <th>Ödeme Durumu</th>
                        <th>Rezervasyon</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $res)
                        <tr>
                            <td>
                                <strong style="font-size:13px">{{ $res->item }}</strong>
                                <div style="font-size:11px;color:var(--muted)">#{{ $res->id }} · {{ $res->category }}</div>
                            </td>
                            <td>
                                <strong style="color:var(--ink)">{{ $res->customer_name ?? $res->name }}</strong>
                            </td>
                            <td style="font-size:12px">
                                <div><a href="mailto:{{ $res->customer_email ?? $res->email }}" style="color:var(--primary)">{{ $res->customer_email ?? $res->email }}</a></div>
                                <div style="color:var(--muted)">{{ $res->customer_phone ?? $res->phone }}</div>
                            </td>
                            <td style="font-size:12px">
                                {{ $res->start_date }}
                                @if ($res->end_date && $res->end_date !== $res->start_date)
                                    – {{ $res->end_date }}
                                @endif
                            </td>
                            <td>
                                <span class="status-badge" style="background:#f1f5f9;color:var(--ink)">
                                    🎟️ {{ $res->guests }} Kişi
                                </span>
                            </td>
                            <td>
                                <strong style="color:var(--primary)">₺{{ number_format($res->total_amount, 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="status-badge {{ $res->payment_status === 'paid' ? 'paid' : 'pending' }}">
                                    {{ $res->payment_status === 'paid' ? 'Ödendi' : 'Beklemede' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $res->status === 'confirmed' ? 'confirmed' : ($res->status === 'refunded' ? 'refunded' : 'pending') }}">
                                    {{ $res->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">Henüz gelen rezervasyon veya katılımcı kaydı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Two Columns: Expenses & Messages -->
    <div class="admin-cols">
        <!-- Expense Management -->
        <section class="admin-card">
            <div class="card-header">
                <h2>➕ Yeni Operasyonel Gider Kaydı</h2>
            </div>
            <div class="card-body">
                <form class="form-grid" action="{{ route('organizer.expenses.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="expTitle">Gider Açıklaması</label>
                        <input class="form-control" id="expTitle" name="title" required maxlength="180" placeholder="Örn: Mekan Kirası, Ses Sistemi">
                    </div>
                    <div class="form-group">
                        <label for="expCategory">Gider Türü</label>
                        <input class="form-control" id="expCategory" name="category" required maxlength="80" placeholder="Ulaşım, Ekipman, Catering, Reklam">
                    </div>
                    <div class="form-group">
                        <label for="expAmount">Tutar (₺)</label>
                        <input class="form-control" id="expAmount" name="amount" type="number" min="0.01" step="0.01" required placeholder="3500">
                    </div>
                    <div class="form-group">
                        <label for="expDate">Gider Tarihi</label>
                        <input class="form-control" id="expDate" name="expense_date" type="date" max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="form-group full">
                        <label for="expNote">Not (İsteğe Bağlı)</label>
                        <textarea class="form-control" id="expNote" name="note" placeholder="Fatura numarası veya ek bilgi"></textarea>
                    </div>
                    <div class="form-group full" style="margin-top:6px">
                        <button class="btn btn-teal" type="submit" style="width:100%">
                            Gideri Kaydet
                        </button>
                    </div>
                </form>

                @if ($recentExpenses->isNotEmpty())
                    <div style="margin-top:24px;border-top:1px solid var(--line);padding-top:16px">
                        <strong style="font-size:13px;display:block;margin-bottom:10px">Son Kaydedilen Giderler</strong>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Gider</th>
                                        <th>Kategori</th>
                                        <th>Tutar</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentExpenses->take(5) as $exp)
                                        <tr>
                                            <td><strong>{{ $exp->title }}</strong></td>
                                            <td><span class="status-badge" style="background:#f1f5f9;color:var(--muted)">{{ $exp->category }}</span></td>
                                            <td><strong style="color:var(--amber)">₺{{ number_format($exp->amount, 2, ',', '.') }}</strong></td>
                                            <td style="font-size:11px;color:var(--muted)">{{ $exp->expense_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Customer Messages -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>💬 İlanlarıma Gelen Müşteri Mesajları</span>
                    <span class="card-header-badge">{{ $messages->count() }} Mesaj</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Mesaj</th>
                            <th>Tarih / Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $msg)
                            <tr>
                                <td>
                                    <strong>{{ $msg->name }}</strong>
                                    <div style="font-size:11px;color:var(--muted)">
                                        <a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a>
                                    </div>
                                    @if ($msg->phone)
                                        <div style="font-size:11px;color:var(--muted)">{{ $msg->phone }}</div>
                                    @endif
                                </td>
                                <td style="font-size:12px;line-height:1.5;max-width:280px">
                                    {{ $msg->message }}
                                </td>
                                <td>
                                    <div style="font-size:11px;color:var(--muted)">{{ $msg->created_at }}</div>
                                    <span class="status-badge {{ $msg->status === 'replied' ? 'confirmed' : 'pending' }}" style="font-size:10px;margin-top:4px">
                                        {{ $msg->status === 'replied' ? 'Yanıtlandı' : 'Bekliyor' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px;flex-direction:column">
                                        <a class="btn btn-outline btn-sm" href="mailto:{{ $msg->email }}?subject=ENTUR Bilgilendirme" style="font-size:11px;padding:3px 7px">
                                            E-posta İle Yanıtla
                                        </a>
                                        @if ($msg->status !== 'replied')
                                            <form action="{{ route('organizer.messages.status', $msg->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="replied">
                                                <button class="btn btn-teal btn-sm" type="submit" style="font-size:10px;padding:3px 7px;width:100%">
                                                    ✓ Yanıtlandı İşaretle
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">İlanlarınıza ait müşteri mesajı bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Refunds & Installments Grid -->
    <div class="admin-cols">
        <!-- Refunds Review -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>↩️ İlanlarıma Ait İade Talepleri</span>
                    <span class="card-header-badge">{{ $refunds->count() }} Talep</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Rezervasyon</th>
                            <th>İade Tutarı</th>
                            <th>Gerekçe</th>
                            <th>Karar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($refunds as $rf)
                            <tr>
                                <td><strong>#{{ $rf->reservation_id }}</strong></td>
                                <td><strong style="color:var(--red)">₺{{ number_format($rf->amount, 2, ',', '.') }}</strong></td>
                                <td style="font-size:12px;color:var(--ink-soft)">{{ $rf->reason }}</td>
                                <td>
                                    @if ($rf->status === 'requested')
                                        <form action="{{ route('organizer.refunds.review', $rf->id) }}" method="POST" style="display:flex;gap:6px">
                                            @csrf
                                            <button class="btn btn-teal btn-sm" name="decision" value="approved" type="submit">Onayla</button>
                                            <button class="btn btn-outline btn-sm" name="decision" value="rejected" type="submit" style="color:var(--red)">Reddet</button>
                                        </form>
                                    @else
                                        <span class="status-badge {{ $rf->status === 'approved' ? 'paid' : 'rejected' }}">
                                            {{ $rf->status === 'approved' ? 'İade Onaylandı' : 'İade Reddedildi' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">İlanlarınıza ait açık iade talebi bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Installments Schedule -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>💳 Yıllık Üyelik Taksit Çizelgesi</span>
                    <span class="card-header-badge">{{ $installments->count() }} Taksit</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Taksit No</th>
                            <th>Tutar</th>
                            <th>Vade Tarihi</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($installments as $inst)
                            <tr>
                                <td><strong>Taksit #{{ $inst->installment_no }}</strong></td>
                                <td><strong>₺{{ number_format($inst->amount, 2, ',', '.') }}</strong></td>
                                <td style="font-size:12px;color:var(--muted)">{{ $inst->due_date }}</td>
                                <td>
                                    <span class="status-badge {{ $inst->status === 'paid' ? 'paid' : 'pending' }}">
                                        {{ $inst->status === 'paid' ? '✓ Ödendi' : 'Ödeme Bekliyor' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">Tanımlı üyelik taksidi bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<script>
    function handleCategoryChange(val) {
        const wrapper = document.getElementById('customCategoryWrapper');
        const input = document.getElementById('customCategory');
        if (val === 'custom') {
            wrapper.style.display = 'flex';
            input.required = true;
            input.focus();
        } else {
            wrapper.style.display = 'none';
            input.required = false;
        }
    }
</script>
@endsection
