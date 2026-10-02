@extends('anatema')

@section('title', 'Rezervasyonlarım | ENTUR')

@section('content')
<style>
    .customer-main { width: min(1140px, calc(100% - 48px)); margin: 0 auto; padding: 48px 0 80px; }
    .customer-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
    .customer-header h1 { font: 800 32px 'Manrope', sans-serif; color: var(--ink); margin-bottom: 6px; }
    .customer-lead { color: var(--muted); font-size: 14px; margin: 0; }
    
    .booking-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        padding: 24px;
        margin-bottom: 20px;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.07);
    }

    .booking-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .booking-title {
        font: 700 20px 'Manrope', sans-serif;
        color: var(--ink);
        margin-bottom: 6px;
    }

    .booking-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: var(--muted);
        flex-wrap: wrap;
    }

    .booking-price {
        font: 800 22px 'Manrope', sans-serif;
        color: var(--green-dark);
        white-space: nowrap;
        text-align: right;
    }

    .booking-badges-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .tag-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    .tag-badge.paid { background: #dcfce7; color: #15803d; }
    .tag-badge.pending { background: #fef3c7; color: #b45309; }
    .tag-badge.confirmed { background: var(--mint); color: var(--green-dark); }
    .tag-badge.refunded { background: #fee2e2; color: #b91c1c; }
    .tag-badge.default { background: #f1f5f9; color: #475569; }

    .refund-section {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px dashed var(--line);
    }

    .refund-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .refund-form {
        display: flex;
        gap: 10px;
        width: 100%;
        margin-top: 10px;
    }

    .refund-input {
        flex: 1;
        min-height: 42px;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        outline: none;
    }
    .refund-input:focus { border-color: var(--green); }

    .btn-refund {
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid #fca5a5;
        border-radius: 6px;
        background: #fff;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .btn-refund:hover { background: #fee2e2; }

    .empty-box {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
    }
    .empty-icon { font-size: 48px; margin-bottom: 14px; }
    .empty-box h3 { font: 700 20px 'Manrope', sans-serif; color: var(--ink); margin-bottom: 8px; }
    .empty-box p { color: var(--muted); font-size: 14px; margin-bottom: 20px; }
    .btn-discover { display: inline-flex; padding: 10px 22px; border-radius: 8px; background: var(--green); color: #fff; font-weight: 700; font-size: 14px; }
    .btn-discover:hover { background: var(--green-dark); }
</style>

<main class="customer-main">
    <div class="customer-header">
        <div>
            <h1>Rezervasyonlarım</h1>
            <p class="customer-lead">Katılacağınız etkinlikler, otel konaklamalarınız, biletleriniz ve ödeme durumlarınız.</p>
        </div>
        <a class="btn-discover" href="{{ route('categories.index') }}">
            + Yeni Rezervasyon Keşfet
        </a>
    </div>

    @if (session('status'))
        <div style="padding:14px 18px;background:var(--mint);border:1px solid #a7f3d0;border-radius:8px;color:var(--green-dark);font-size:13px;margin-bottom:24px">
            ✓ {{ session('status') }}
        </div>
    @endif

    @forelse ($reservations as $res)
        <article class="booking-card">
            <div class="booking-head">
                <div>
                    <h2 class="booking-title">{{ $res->item }}</h2>
                    <div class="booking-meta">
                        <span>🏷️ {{ ucfirst(str_replace('-', ' ', $res->category)) }}</span>
                        <span>•</span>
                        <span>📅 {{ $res->start_date }} @if ($res->end_date && $res->end_date !== $res->start_date)– {{ $res->end_date }}@endif</span>
                        <span>•</span>
                        <span>👥 {{ $res->guests }} Katılımcı / Bilet</span>
                        @if ($res->organizer_name)
                            <span>•</span>
                            <span>🏢 Organizatör: {{ $res->organizer_name }}</span>
                        @endif
                    </div>
                </div>
                <div class="booking-price">
                    ₺{{ number_format($res->total_amount, 2, ',', '.') }}
                </div>
            </div>

            <div class="booking-badges-row">
                <!-- Payment Method Badge -->
                <span class="tag-badge default">
                    @if ($res->payment_method === 'credit_card')
                        💳 Kredi Kartı
                    @elseif ($res->payment_method === 'bank_transfer')
                        🏦 Havale / EFT
                    @elseif ($res->payment_method === 'coupon')
                        🎟️ Kupon ile Ödendi
                    @else
                        {{ $res->payment_method }}
                    @endif
                </span>

                <!-- Payment Status Badge -->
                <span class="tag-badge {{ $res->payment_status === 'paid' ? 'paid' : ($res->payment_status === 'refunded' ? 'refunded' : 'pending') }}">
                    Ödeme: {{ $res->payment_status === 'paid' ? 'Tahsil Edildi' : ($res->payment_status === 'refunded' ? 'İade Edildi' : 'Onay Bekliyor') }}
                </span>

                <!-- Reservation Status Badge -->
                <span class="tag-badge {{ $res->status === 'confirmed' ? 'confirmed' : ($res->status === 'refunded' ? 'refunded' : 'pending') }}">
                    Rezervasyon: {{ $res->status === 'confirmed' ? 'Kesinleşti' : ($res->status === 'refunded' ? 'İptal / İade' : 'İşlemde') }}
                </span>

                @if ($res->provider_reference)
                    <span style="font-size:12px;color:var(--muted);margin-left:4px">
                        {{ $res->provider_reference }}
                    </span>
                @endif
            </div>

            <!-- Refund Request Section -->
            <div class="refund-section">
                @if ($res->refund)
                    <div class="refund-box">
                        <div>
                            <strong>İade Talebi Durumu:</strong>
                            <span class="tag-badge {{ $res->refund->status === 'approved' ? 'paid' : ($res->refund->status === 'rejected' ? 'refunded' : 'pending') }}" style="margin-left:6px">
                                {{ $res->refund->status === 'approved' ? '✓ İade Onaylandı' : ($res->refund->status === 'rejected' ? '✕ İade Reddedildi' : '⏳ Değerlendiriliyor') }}
                            </span>
                            <div style="font-size:12px;color:var(--muted);margin-top:4px">Gerekçe: {{ $res->refund->reason }}</div>
                        </div>
                        <div style="font-weight:700;color:var(--red)">
                            ₺{{ number_format($res->refund->amount, 2, ',', '.') }}
                        </div>
                    </div>
                @elseif (! in_array($res->status, ['cancelled', 'refunded'], true))
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                        <span style="font-size:12px;color:var(--muted)">Rezervasyonunuzu iptal etmek veya ücret iadesi istemek için gerekçenizi belirterek talep oluşturabilirsiniz.</span>
                    </div>
                    <form class="refund-form" action="{{ route('customer.refunds.store', $res->id) }}" method="POST">
                        @csrf
                        <input class="refund-input" name="reason" required minlength="5" maxlength="500" placeholder="İade talep etme nedeninizi yazın (en az 5 karakter)...">
                        <button class="btn-refund" type="submit">İade Talebi Oluştur</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="empty-box">
            <div class="empty-icon">🎟️</div>
            <h3>Henüz Rezervasyonunuz Bulunmuyor</h3>
            <p>Konserlerden sakin sahil otellerine kadar özenle seçilmiş deneyimleri inceleyebilir ve hemen rezervasyon oluşturabilirsiniz.</p>
            <a class="btn-discover" href="{{ route('categories.index') }}">Seçenekleri Keşfet</a>
        </div>
    @endforelse
</main>
@endsection
