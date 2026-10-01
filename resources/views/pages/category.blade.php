@extends('anatema')

@section('title', $category['title'] . ' rezervasyonu | ENTUR')

@section('content')
<style>
    .category-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 58px 0 78px; }
    .category-heading { max-width: 690px; margin-bottom: 28px; }
    .category-heading h1 { margin: 14px 0 10px; font: 700 39px/1.16 'Manrope', sans-serif; }
    .category-heading p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.7; }
    .offer-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
    .offer-card { min-width: 0; border: 1px solid var(--line); border-radius: 5px; overflow: hidden; background: #fff; }
    .offer-card img { width: 100%; height: 190px; display: block; object-fit: cover; }
    .offer-content { padding: 16px; }
    .offer-location { color: var(--muted); font-size: 11px; }
    .offer-content h2 { margin: 7px 0; font: 700 17px 'Manrope', sans-serif; }
    .offer-content p { min-height: 62px; margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }
    .offer-bottom { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 16px; padding-top: 13px; border-top: 1px solid var(--line); }
    .offer-price { color: var(--green-dark); font-size: 12px; font-weight: 700; }
    .offer-select { color: var(--green); font-size: 12px; font-weight: 700; }
    .offer-select:hover { text-decoration: underline; text-underline-offset: 3px; }
    .reservation-section { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 42px; align-items: start; margin-top: 58px; padding-top: 34px; border-top: 1px solid var(--line); }
    .reservation-section h2 { margin: 0 0 7px; font: 700 23px 'Manrope', sans-serif; }
    .reservation-section > div > p { margin: 0 0 24px; color: var(--muted); font-size: 13px; line-height: 1.7; }
    .reservation-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
    .reservation-form .field.full { grid-column: 1 / -1; }
    .reservation-form textarea { width: 100%; min-height: 90px; padding: 12px 14px; border: 1px solid #dce4e0; border-radius: 5px; color: var(--ink); font: inherit; resize: vertical; }
    .reservation-form textarea:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgb(23 107 87 / 10%); }
    .reservation-aside { position: sticky; top: 24px; padding: 22px; border: 1px solid var(--line); border-radius: 5px; background: #f8fbf9; }
    .reservation-aside strong { display: block; margin: 10px 0; font: 700 16px 'Manrope', sans-serif; }
    .reservation-aside p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }
    .category-submit { min-height: 48px; grid-column: 1 / -1; padding: 0 16px; border: 0; border-radius: 4px; background: var(--green); color: #fff; font-weight: 700; cursor: pointer; }
    .category-submit:hover { background: var(--green-dark); }
    .form-status { margin: 0 0 20px; padding: 13px 15px; border-left: 3px solid var(--green); background: var(--mint); color: var(--green-dark); font-size: 13px; line-height: 1.6; }
    .field-error { color: #b94335; font-size: 12px; }
    @media(max-width: 800px) { .offer-grid { grid-template-columns: 1fr 1fr; } .reservation-section { grid-template-columns: 1fr; gap: 20px; } .reservation-aside { position: static; } }
    @media(max-width: 560px) { .category-main { width: min(100% - 32px, 1120px); padding: 38px 0 52px; } .category-heading h1 { font-size: 33px; } .offer-grid { grid-template-columns: 1fr; } .offer-card img { height: 220px; } .reservation-form { grid-template-columns: 1fr; } .reservation-form .field.full, .category-submit { grid-column: auto; } }
</style>
<main class="category-main">
    <section class="category-heading">
        <span class="eyebrow">{{ $category['eyebrow'] }}</span>
        <h1>{{ $category['title'] }} ile yeni yerler keşfedin.</h1>
        <p>{{ $category['description'] }} Beğendiğiniz seçeneği işaretleyin, talebinizi aşağıdaki formdan iletin.</p>
    </section>

    <section class="offer-grid" aria-label="{{ $category['title'] }} seçenekleri">
        @foreach ($category['items'] as $item)
            <article class="offer-card">
                <img src="https://images.unsplash.com/{{ $item['image'] }}?auto=format&fit=crop&w=900&q=80" alt="{{ $item['name'] }} - {{ $item['location'] }}" loading="lazy">
                <div class="offer-content">
                    <span class="offer-location">{{ $item['location'] }}</span>
                    <h2>{{ $item['name'] }}</h2>
                    <p>{{ $item['description'] }}</p>
                    <div class="offer-bottom">
                        <span class="offer-price">{{ $item['price'] }}</span>
                        <a class="offer-select" href="{{ route('categories.show', ['category' => $categoryKey, 'secim' => $item['slug']]) }}#request-form">Bu seçeneği seç <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="reservation-section" id="request-form">
        <div>
            <h2>Rezervasyon talebi</h2>
            <p>Seçiminizi ve iletişim bilgilerinizi paylaşın. Talebinizi aldıktan sonra ayrıntıları sizinle netleştireceğiz.</p>
            @if (session('status'))
                <div class="form-status" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="form-status" role="alert">Lütfen formdaki bilgileri kontrol edip tekrar deneyin.</div>
            @endif
            <form class="reservation-form" action="{{ route('reservations.store', $categoryKey) }}" method="POST">
                @csrf
                <div class="field full">
                    <label for="item">Seçtiğiniz {{ strtolower($category['title']) }}</label>
                    <select id="item" name="item" required>
                        <option value="">Bir seçenek belirleyin</option>
                        @foreach ($category['items'] as $item)
                            <option value="{{ $item['slug'] }}" @selected(old('item', $selectedItem['slug'] ?? '') === $item['slug'])>{{ $item['name'] }} · {{ $item['location'] }}</option>
                        @endforeach
                    </select>
                    @error('item')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label for="start_date">Başlangıç tarihi</label>
                    <input id="start_date" name="start_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('start_date') }}" required>
                    @error('start_date')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label for="end_date">Bitiş tarihi <span style="font-weight:400;color:#87938e">(isteğe bağlı)</span></label>
                    <input id="end_date" name="end_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('end_date') }}">
                    @error('end_date')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label for="guests">Misafir / katılımcı sayısı</label>
                    <input id="guests" name="guests" type="number" min="1" max="20" value="{{ old('guests', 2) }}" required>
                    @error('guests')<span class="field-error">{{ $message }}</span>@enderror
                </div>
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
                <div class="field">
                    <label for="phone">Telefon numarası</label>
                    <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" required>
                    @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="field full">
                    <label for="message">Eklemek istedikleriniz <span style="font-weight:400;color:#87938e">(isteğe bağlı)</span></label>
                    <textarea id="message" name="message" placeholder="Özel bir isteğiniz varsa yazabilirsiniz.">{{ old('message') }}</textarea>
                    @error('message')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <button class="category-submit" type="submit">Rezervasyon talebini gönder <span aria-hidden="true">→</span></button>
            </form>
        </div>
        <aside class="reservation-aside">
            <span class="eyebrow">Sonraki adım</span>
            <strong>Talebiniz ekibimize ulaşır.</strong>
            <p>Ekibimiz seçiminizi ve tarihlerinizi inceleyerek uygunluk ve fiyat bilgisi için sizinle iletişime geçer. Bu form henüz kesin rezervasyon veya ödeme oluşturmaz.</p>
        </aside>
    </section>
</main>
@endsection
