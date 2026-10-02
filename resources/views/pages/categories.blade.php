@extends('anatema')

@section('title', 'Rezervasyon seçenekleri | ENTUR')

@section('content')
<style>
    .categories-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 62px 0 80px; }
    .categories-heading { max-width: 660px; margin-bottom: 35px; }
    .categories-heading h1 { margin: 14px 0 10px; font: 700 40px/1.16 'Manrope', sans-serif; }
    .categories-heading p { margin: 0; color: var(--muted); font-size: 15px; line-height: 1.7; }
    .categories-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .category-tile { min-width: 0; }
    .category-tile img { width: 100%; height: 270px; display: block; object-fit: cover; border-radius: 4px; transition: transform .3s; }
    .category-tile-image { display: block; overflow: hidden; border-radius: 4px; }
    .category-tile-image:hover img { transform: scale(1.025); }
    .category-tile h2 { margin: 16px 0 7px; font: 700 18px 'Manrope', sans-serif; }
    .category-tile p { min-height: 44px; margin: 0; color: var(--muted); font-size: 13px; line-height: 1.7; }
    .tile-link { display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; color: var(--green); font-size: 13px; font-weight: 700; }
    .tile-link:hover { color: var(--green-dark); }
    @media(max-width: 700px) { .categories-main { width: min(100% - 32px, 1120px); padding: 40px 0 55px; } .categories-heading h1 { font-size: 34px; } .categories-grid { grid-template-columns: 1fr; gap: 30px; } .category-tile img { height: 235px; } }
</style>
<main class="categories-main">
    <section class="categories-heading">
        <span class="eyebrow">Rezervasyon</span>
        <h1>Yolculuğunuz için bir başlangıç seçin.</h1>
        <p>Konaklayacağınız yeri veya katılacağınız deneyimi keşfedin; size uyan seçeneği belirleyip talebinizi iletin.</p>
    </section>
    <div class="categories-grid">
        @foreach ($categories as $key => $category)
            @php($item = $category['items'][0])

            @php($imageUrl = !empty($item['image_url']) ? $item['image_url'] : (!empty($item['image']) ? 'https://images.unsplash.com/'.$item['image'].'?auto=format&fit=crop&w=1000&q=80' : 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1000&q=80'))
            <article class="category-tile">
                <a class="category-tile-image" href="{{ route('categories.show', $key) }}">
                    <img src="{{ $imageUrl }}" alt="{{ $category['title'] }} için örnek seçenek" loading="lazy">
                </a>
                <h2>{{ $category['title'] }}</h2>
                <p>{{ $category['description'] }}</p>
                <a class="tile-link" href="{{ route('categories.show', $key) }}">Seçenekleri incele <span aria-hidden="true">→</span></a>
            </article>
        @endforeach
    </div>
</main>
@endsection
