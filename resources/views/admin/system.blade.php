@extends('admin.layout')

@section('title', 'Sistem Yönetimi & Finans | ENTUR')

@section('content')
<main class="admin-container">
    <div class="dashboard-header">
        <div>
            <h1>Sistem Yönetim Portalı</h1>
            <p>Platform geneli satışlar, organizatör aidatları, operasyonel onaylar ve finansal denetim.</p>
        </div>
        <nav class="period-selector" aria-label="Raporlama Dönemi">
            <a class="period-btn {{ $period === 'day' ? 'active' : '' }}" href="?period=day">Bugün (Günlük)</a>
            <a class="period-btn {{ $period === 'month' ? 'active' : '' }}" href="?period=month">Bu Ay (Aylık)</a>
            <a class="period-btn {{ $period === 'year' ? 'active' : '' }}" href="?period=year">Bu Yıl (Yıllık)</a>
        </nav>
    </div>

    <!-- Platform Overview Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Rezervasyon Hasılatı</span>
                <div class="metric-icon-wrap" style="color:var(--primary);background:var(--primary-light)">💰</div>
            </div>
            <div class="metric-val">₺{{ number_format($revenue, 2, ',', '.') }}</div>
            <div class="metric-meta">{{ $period === 'day' ? 'Günlük' : ($period === 'year' ? 'Yıllık' : 'Aylık') }} ödenmiş tahsilat</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Organizatör Aidatları</span>
                <div class="metric-icon-wrap" style="color:var(--green);background:var(--green-light)">🏢</div>
            </div>
            <div class="metric-val">₺{{ number_format($feesPaid, 2, ',', '.') }}</div>
            <div class="metric-meta">Tahsil edilen yıllık üyelikler</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Organizatör Giderleri</span>
                <div class="metric-icon-wrap" style="color:var(--amber);background:var(--amber-light)">📉</div>
            </div>
            <div class="metric-val">₺{{ number_format($expenses, 2, ',', '.') }}</div>
            <div class="metric-meta">Dönem içi operasyonel giderler</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Platform Net Akışı</span>
                <div class="metric-icon-wrap" style="color:var(--purple);background:var(--purple-light)">📈</div>
            </div>
            <div class="metric-val">₺{{ number_format($net, 2, ',', '.') }}</div>
            <div class="metric-meta">Tahsilat + Aidat - Giderler</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Organizatörler</span>
                <div class="metric-icon-wrap">👥</div>
            </div>
            <div class="metric-val">{{ $organizerCount }}</div>
            <div class="metric-meta">Sistemde kayıtlı organizatör</div>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <span class="metric-label">Onay Bekleyenler</span>
                <div class="metric-icon-wrap" style="color:var(--red);background:var(--red-light)">⏳</div>
            </div>
            <div class="metric-val">{{ $pendingPayments->count() + $draftListings->count() + $refunds->count() }}</div>
            <div class="metric-meta">{{ $pendingPayments->count() }} Havale · {{ $draftListings->count() }} İlan · {{ $refunds->count() }} İade</div>
        </div>
    </div>

    <!-- CORE REQUIREMENT: Per-Organizer Financials & Membership Status -->
    <section class="admin-card">
        <div class="card-header">
            <h2>
                <span>🏢 Organizatör Finans & Satış Denetim Tablosu</span>
                <span class="card-header-badge">{{ $period === 'day' ? 'Günlük Veriler' : ($period === 'year' ? 'Yıllık Veriler' : 'Aylık Veriler') }}</span>
            </h2>
            <div style="font-size:12px;color:var(--muted)">Tüm organizatörlerin satışları, giderleri, net gelirleri ve taksitli yıllık aidat durumu</div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Organizatör</th>
                        <th>Dönemsel Satış</th>
                        <th>Dönemsel Gider</th>
                        <th>Net Gelir</th>
                        <th>Bilet / Katılımcı</th>
                        <th>{{ now()->year }} Yıllık Platform Aidatı</th>
                        <th>Taksit Durumu</th>
                        <th>Hesap Durumu</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($organizers as $organizer)
                        <tr>
                            <td>
                                <strong style="font-size:14px;color:var(--ink)">{{ $organizer->name }}</strong>
                                <div style="font-size:11px;color:var(--muted)">{{ $organizer->email }} · {{ $organizer->phone }}</div>
                            </td>
                            <td>
                                <strong style="color:var(--primary);font-size:14px">₺{{ number_format($organizer->sales, 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span style="color:var(--amber);font-weight:600">₺{{ number_format($organizer->expenses, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                <strong style="color:{{ $organizer->net >= 0 ? 'var(--green-dark)' : 'var(--red)' }};font-size:14px">
                                    ₺{{ number_format($organizer->net, 2, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <span class="status-badge" style="background:#f1f5f9;color:var(--ink)">🎟️ {{ $organizer->ticket_count }} bilet</span>
                            </td>
                            <td>
                                @if ($organizer->annual_amount)
                                    <div><strong>₺{{ number_format($organizer->annual_amount, 2, ',', '.') }}</strong></div>
                                    <span class="status-badge {{ $organizer->fee_status === 'paid' ? 'paid' : 'pending' }}">
                                        {{ $organizer->fee_status === 'paid' ? '✓ Ödendi' : 'Ödeme Bekliyor' }}
                                    </span>
                                @else
                                    <span class="status-badge" style="background:#f1f5f9;color:var(--muted)">Plan Tanımlanmadı</span>
                                @endif
                            </td>
                            <td>
                                @if ($organizer->fee_id)
                                    <div style="font-weight:600;font-size:12px">
                                        {{ $organizer->paid_installments_count }} / {{ $organizer->total_installments_count }} Taksit Ödendi
                                    </div>
                                    @if ($organizer->remaining_fee_amount > 0)
                                        <div style="font-size:11px;color:var(--red);margin-top:2px">Kalan: ₺{{ number_format($organizer->remaining_fee_amount, 2, ',', '.') }}</div>
                                    @else
                                        <div style="font-size:11px;color:var(--green);margin-top:2px">Borcu Yok</div>
                                    @endif
                                    <button class="btn btn-outline btn-sm" type="button" style="margin-top:6px;padding:3px 8px;font-size:11px" onclick="toggleInstallmentsModal({{ $organizer->id }})">
                                        Taksit Detayları ({{ $organizer->installments->count() }})
                                    </button>
                                @else
                                    <span style="color:var(--muted);font-size:12px">–</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $organizer->organizer_status === 'active' ? 'active' : 'suspended' }}">
                                    {{ $organizer->organizer_status === 'active' ? 'Aktif' : 'Askıya Alındı' }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                                    <form action="{{ route('system.organizers.toggleStatus', $organizer->id) }}" method="POST" style="margin:0">
                                        @csrf
                                        @if ($organizer->organizer_status === 'active')
                                            <button class="btn btn-outline btn-sm" type="submit" style="color:var(--red);border-color:#fca5a5" title="Organizatörü askıya al">
                                                Askıya Al
                                            </button>
                                        @else
                                            <button class="btn btn-teal btn-sm" type="submit" title="Organizatörü etkinleştir">
                                                Aktif Et
                                            </button>
                                        @endif
                                    </form>

                                    @if (! $organizer->fee_id)
                                        <button class="btn btn-primary btn-sm" type="button" onclick="openRenewalModal({{ $organizer->id }}, '{{ addslashes($organizer->name) }}')">
                                            Plan Aç
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Hidden Installment Schedule Sub-Row for this Organizer -->
                        @if ($organizer->fee_id)
                            <tr id="inst-row-{{ $organizer->id }}" style="display:none;background:#f8fafc">
                                <td colspan="9" style="padding:16px 24px">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                                        <strong style="font-size:13px;color:var(--ink)">{{ $organizer->name }} · {{ now()->year }} Yılı Taksitlendirme Çizelgesi</strong>
                                        <button class="btn btn-outline btn-sm" type="button" onclick="toggleInstallmentsModal({{ $organizer->id }})" style="padding:2px 8px;font-size:11px">Kapat</button>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:10px">
                                        @foreach ($organizer->installments as $inst)
                                            <div style="background:#fff;border:1px solid var(--line);border-radius:8px;padding:10px 12px;font-size:12px">
                                                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                                                    <strong>Taksit #{{ $inst->installment_no }}</strong>
                                                    <span class="status-badge {{ $inst->status === 'paid' ? 'paid' : 'pending' }}" style="font-size:10px;padding:2px 6px">
                                                        {{ $inst->status === 'paid' ? 'Ödendi' : 'Bekliyor' }}
                                                    </span>
                                                </div>
                                                <div style="font-weight:700;font-size:14px;color:var(--ink)">₺{{ number_format($inst->amount, 2, ',', '.') }}</div>
                                                <div style="color:var(--muted);font-size:11px;margin-top:2px">Vade: {{ $inst->due_date }}</div>
                                                @if ($inst->status === 'unpaid')
                                                    <form action="{{ route('system.installments.paid', $inst->id) }}" method="POST" style="margin-top:8px">
                                                        @csrf
                                                        <button class="btn btn-teal btn-sm" type="submit" style="width:100%;padding:4px 8px;font-size:11px">
                                                            ✓ Ödendi İşaretle
                                                        </button>
                                                    </form>
                                                @else
                                                    <div style="color:var(--green);font-size:10px;margin-top:6px">Ödendi: {{ $inst->paid_at }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">Henüz kayıtlı organizatör bulunmuyor. Aşağıdaki formdan ilk organizatörü tanımlayabilirsiniz.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Two Columns: Approvals -->
    <div class="admin-cols">
        <!-- Bank Transfer Approvals -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>🏦 Bekleyen Havale / EFT Ödemeleri</span>
                    <span class="card-header-badge">{{ $pendingPayments->count() }}</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Rezervasyon</th>
                            <th>Tutar</th>
                            <th>Açıklama / Dekont</th>
                            <th>Onay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingPayments as $payment)
                            <tr>
                                <td>
                                    <strong>{{ $payment->customer_name }}</strong>
                                    <div style="font-size:11px;color:var(--muted)">{{ $payment->customer_email }}</div>
                                </td>
                                <td>{{ $payment->item }}</td>
                                <td><strong style="color:var(--primary)">₺{{ number_format($payment->amount, 2, ',', '.') }}</strong></td>
                                <td style="font-size:11px;color:var(--muted)">
                                    {{ $payment->provider_reference ?? 'Referans yok' }}
                                </td>
                                <td>
                                    <form action="{{ route('system.payments.approve', $payment->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-teal btn-sm" type="submit">
                                            ✓ Onayla & Kesinleştir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">Onay bekleyen havale/EFT ödemesi bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Draft Listings Awaiting Publish Approval -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>📢 Yayına Alınmayı Bekleyen İlanlar</span>
                    <span class="card-header-badge">{{ $draftListings->count() }}</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>İlan Başlığı</th>
                            <th>Organizatör</th>
                            <th>Fiyat / Kontenjan</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($draftListings as $listing)
                            <tr>
                                <td>
                                    <strong>{{ $listing->title }}</strong>
                                    <div style="font-size:11px;color:var(--muted)">{{ $listing->location }} · {{ $listing->category }}</div>
                                </td>
                                <td>{{ $listing->organizer_name }}</td>
                                <td>
                                    ₺{{ number_format($listing->price, 2, ',', '.') }} / {{ $listing->capacity }} kişi
                                </td>
                                <td>
                                    <form action="{{ route('system.listings.publish', $listing->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            ✓ Yayına Al
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">Onay bekleyen taslak ilan bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Two Columns: Creation Forms -->
    <div class="admin-cols">
        <!-- Add New Organizer & Installment Plan Form -->
        <section class="admin-card">
            <div class="card-header">
                <h2>➕ Yeni Organizatör & Yıllık Ücret Planı Tanımla</h2>
            </div>
            <div class="card-body">
                <form class="form-grid" action="{{ route('system.organizers.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="orgName">Yetkili Adı Soyadı</label>
                        <input class="form-control" id="orgName" name="name" required maxlength="160" placeholder="Örn: Ahmet Yılmaz">
                    </div>
                    <div class="form-group">
                        <label for="orgEmail">E-posta Adresi</label>
                        <input class="form-control" id="orgEmail" name="email" type="email" required maxlength="190" placeholder="ahmet@turizm.com">
                    </div>
                    <div class="form-group">
                        <label for="orgPhone">Telefon Numarası</label>
                        <input class="form-control" id="orgPhone" name="phone" required maxlength="30" placeholder="0532 123 45 67">
                    </div>
                    <div class="form-group">
                        <label for="orgPassword">Geçici Şifre (En az 12 karakter)</label>
                        <input class="form-control" id="orgPassword" name="password" type="password" required minlength="12" placeholder="GüçlüŞifre123!*">
                    </div>
                    <div class="form-group">
                        <label for="annualAmount">Yıllık Üyelik Tutarı (₺)</label>
                        <input class="form-control" id="annualAmount" name="annual_amount" type="number" min="1" step="0.01" required placeholder="12000">
                    </div>
                    <div class="form-group">
                        <label for="installmentCount">Taksitlendirme Seçeneği</label>
                        <select class="form-control" id="installmentCount" name="installment_count">
                            <option value="1">Peşin (1 Taksit)</option>
                            @foreach (range(2, 12) as $count)
                                <option value="{{ $count }}">{{ $count }} Eşit Taksit</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group full" style="margin-top:6px">
                        <button class="btn btn-primary" type="submit" style="width:100%">
                            Organizatör Hesabını & Taksit Planını Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Coupon Generator -->
        <section class="admin-card">
            <div class="card-header">
                <h2>🎟️ Yeni İndirim Kuponu Tanımla</h2>
            </div>
            <div class="card-body">
                <form class="form-grid" action="{{ route('system.coupons.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="couponCode">Kupon Kodu</label>
                        <input class="form-control" id="couponCode" name="code" required maxlength="60" placeholder="YAZ2026" style="text-transform:uppercase">
                    </div>
                    <div class="form-group">
                        <label for="discountType">İndirim Türü</label>
                        <select class="form-control" id="discountType" name="discount_type">
                            <option value="percent">Yüzde İndirimi (%)</option>
                            <option value="fixed">Sabit Tutar İndirimi (₺)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discountValue">İndirim Miktarı</label>
                        <input class="form-control" id="discountValue" name="discount_value" type="number" step="0.01" min="0.01" required placeholder="20">
                    </div>
                    <div class="form-group">
                        <label for="maxUses">Kullanım Limiti (İsteğe Bağlı)</label>
                        <input class="form-control" id="maxUses" name="max_uses" type="number" min="1" placeholder="Sınırsız için boş bırakın">
                    </div>
                    <div class="form-group full">
                        <label for="expiresAt">Son Geçerlilik Tarihi (İsteğe Bağlı)</label>
                        <input class="form-control" id="expiresAt" name="expires_at" type="datetime-local">
                    </div>
                    <div class="form-group full" style="margin-top:6px">
                        <button class="btn btn-teal" type="submit" style="width:100%">
                            Kuponu Oluştur & Aktifleştir
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Active Coupons Table & Pending Refunds -->
    <div class="admin-cols">
        <!-- Refund Requests -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>↩️ Müşteri İade Talepleri</span>
                    <span class="card-header-badge">{{ $refunds->count() }}</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Rezervasyon</th>
                            <th>Tutar</th>
                            <th>İade Nedeni</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($refunds as $refund)
                            <tr>
                                <td>
                                    <strong>{{ $refund->customer_name }}</strong>
                                    <div style="font-size:11px;color:var(--muted)">{{ $refund->customer_email }}</div>
                                </td>
                                <td>{{ $refund->item }}</td>
                                <td><strong style="color:var(--red)">₺{{ number_format($refund->amount, 2, ',', '.') }}</strong></td>
                                <td style="font-size:12px;color:var(--ink-soft)">{{ $refund->reason }}</td>
                                <td>
                                    <form action="{{ route('system.refunds.review', $refund->id) }}" method="POST" style="display:flex;gap:6px">
                                        @csrf
                                        <button class="btn btn-teal btn-sm" name="decision" value="approved" type="submit">Onayla</button>
                                        <button class="btn btn-outline btn-sm" name="decision" value="rejected" type="submit" style="color:var(--red)">Reddet</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">Bekleyen iade talebi yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Coupons Table -->
        <section class="admin-card">
            <div class="card-header">
                <h2>
                    <span>🎟️ Sistemdeki Kuponlar</span>
                    <span class="card-header-badge">{{ $coupons->count() }}</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>İndirim</th>
                            <th>Kullanım Sayısı</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td><strong style="font-family:monospace;font-size:14px;color:var(--primary)">{{ $coupon->code }}</strong></td>
                                <td>
                                    {{ $coupon->discount_type === 'percent' ? '%'.$coupon->discount_value : '₺'.number_format($coupon->discount_value, 2, ',', '.') }}
                                </td>
                                <td>
                                    {{ $coupon->uses_count }} / {{ $coupon->max_uses ?? '∞' }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $coupon->is_active ? 'active' : 'suspended' }}">
                                        {{ $coupon->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">Tanımlı kupon bulunmuyor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Customer Messages -->
    <section class="admin-card">
        <div class="card-header">
            <h2>
                <span>💬 Son Müşteri İletişim Mesajları</span>
                <span class="card-header-badge">{{ $messages->count() }}</span>
            </h2>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gönderen</th>
                        <th>İletişim</th>
                        <th>Mesaj</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $msg)
                        <tr>
                            <td><strong>{{ $msg->name }}</strong></td>
                            <td style="font-size:12px">
                                <div><a href="mailto:{{ $msg->email }}" style="color:var(--primary)">{{ $msg->email }}</a></div>
                                <div style="color:var(--muted)">{{ $msg->phone ?? '-' }}</div>
                            </td>
                            <td style="font-size:13px;max-width:400px;line-height:1.5">{{ $msg->message }}</td>
                            <td style="font-size:11px;color:var(--muted);white-space:nowrap">{{ $msg->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">Gelen iletişim mesajı bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
    function toggleInstallmentsModal(orgId) {
        const row = document.getElementById('inst-row-' + orgId);
        if (row) {
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
    }

    function openRenewalModal(orgId, orgName) {
        const amount = prompt(orgName + " için bu yılın yıllık üyelik tutarını girin (₺):", "12000");
        if (!amount) return;
        const count = prompt("Taksit sayısı (1-12):", "3");
        if (!count) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/yonetim/sistem/organizatör/' + orgId + '/yillik-plan';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        const inputAmount = document.createElement('input');
        inputAmount.type = 'hidden';
        inputAmount.name = 'annual_amount';
        inputAmount.value = amount;
        form.appendChild(inputAmount);

        const inputCount = document.createElement('input');
        inputCount.type = 'hidden';
        inputCount.name = 'installment_count';
        inputCount.value = count;
        form.appendChild(inputCount);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection
