@extends('anatema')

@section('title', 'ENTUR | Yolculuğunuz burada başlar')

@section('content')
<style>
    .home-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 30px 0 74px; }
    .home-hero { min-height: 470px; position: relative; display: flex; align-items: flex-end; overflow: hidden; border-radius: 5px; color: #fff; background: #28443b url('https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=2000&q=85') center 52% / cover; animation: rise .6s ease both; }
    .home-hero::before { position: absolute; inset: 0; content: ''; background: linear-gradient(90deg, rgb(11 31 25 / 76%), rgb(11 31 25 / 18%) 77%), linear-gradient(0deg, rgb(11 31 25 / 27%), transparent 60%); }
    .hero-copy { position: relative; max-width: 670px; padding: 52px 58px; }
    .hero-copy .eyebrow { color: #e1f1e9; }
    .hero-copy h1 { max-width: 610px; margin: 15px 0 14px; font: 700 46px/1.12 'Manrope', sans-serif; }
    .hero-copy p { max-width: 480px; color: rgb(255 255 255 / 85%); font-size: 15px; line-height: 1.7; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 11px; margin-top: 25px; }
    .hero-button { min-height: 48px; display: inline-flex; align-items: center; justify-content: center; padding: 0 18px; border: 1px solid transparent; border-radius: 4px; background: #fff; color: #1e3f35; font-size: 13px; font-weight: 700; transition: transform .18s, background .18s; }
    .hero-button:hover { transform: translateY(-2px); background: #eaf4ee; }
    .hero-button.secondary { border-color: rgb(255 255 255 / 65%); background: transparent; color: #fff; }
    .hero-button.secondary:hover { background: rgb(255 255 255 / 13%); }
    .home-section { padding-top: 66px; }
    .section-top { display: flex; justify-content: space-between; align-items: end; gap: 25px; margin-bottom: 24px; }
    .section-top h2 { margin-top: 10px; font: 700 27px 'Manrope', sans-serif; }
    .section-top p { max-width: 380px; margin: 0; color: var(--muted); font-size: 13px; line-height: 1.65; }
    .home-category-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .home-category { min-width: 0; }
    .home-category img { width: 100%; height: 220px; display: block; object-fit: cover; border-radius: 4px; transition: transform .3s; }
    .category-image-link { display: block; overflow: hidden; border-radius: 4px; }
    .category-image-link:hover img { transform: scale(1.025); }
    .home-category-meta { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-top: 14px; }
    .home-category h3 { margin: 0 0 5px; font: 700 16px 'Manrope', sans-serif; }
    .home-category p { margin: 0; color: var(--muted); font-size: 12px; }
    .category-arrow { width: 36px; height: 36px; display: grid; place-items: center; flex: 0 0 auto; border: 1px solid var(--line); border-radius: 50%; color: var(--green); transition: color .18s, background .18s; }
    .home-category-meta:hover .category-arrow { color: #fff; background: var(--green); }
    .home-note { display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 38px; margin-top: 74px; padding: 32px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
    .home-note h2 { max-width: 430px; font: 700 26px/1.3 'Manrope', sans-serif; }
    .home-note p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.8; }
    @media(max-width: 720px) { .home-main { width: min(100% - 32px, 1120px); padding-top: 20px; } .home-hero { min-height: 460px; } .hero-copy { padding: 34px 26px; } .hero-copy h1 { font-size: 36px; } .home-category-grid { grid-template-columns: 1fr; gap: 25px; } .home-category img { height: 230px; } .home-section { padding-top: 48px; } .section-top { display: block; } .section-top p { margin-top: 10px; } .home-note { grid-template-columns: 1fr; gap: 8px; margin-top: 50px; } }
</style>
<main class="home-main">
    <section class="home-hero" aria-label="ENTUR ile seyahat keşfi">
        <div class="hero-copy">
            <span class="eyebrow">Daha çok keşif, daha güzel anılar</span>
            <h1>Yolculuğunuz burada başlar.</h1>
            <p>Konaklamadan şehir deneyimlerine, size iyi gelecek kaçamağı tek bir yerden planlayın.</p>
            <div class="hero-actions">
                <a class="hero-button" href="{{ route('categories.index') }}">Keşfetmeye başla</a>
                <a class="hero-button secondary" href="{{ route('about') }}">ENTUR’ü tanıyın</a>
            </div>
        </div>
    </section>

    <section class="home-section" aria-labelledby="discover-heading">
        <div class="section-top">
            <div><span class="eyebrow">Size göre bir kaçamak</span><h2 id="discover-heading">Nasıl bir yolculuk arıyorsunuz?</h2></div>
            <p>İster yeni bir şehir, ister birlikte geçirilen sakin bir hafta sonu; size uygun seçeneklerle başlayın.</p>
        </div>
        <div class="home-category-grid">
            @foreach ($categories as $key => $category)
                @php($item = $category['items'][0])
                <article class="home-category">
                    <a class="category-image-link" href="{{ route('categories.show', $key) }}" aria-label="{{ $category['title'] }} seçeneklerini gör">
                        <img src="https://images.unsplash.com/{{ $item['image'] }}?auto=format&fit=crop&w=900&q=80" alt="{{ $item['location'] }} konaklama ve gezi atmosferi" loading="lazy">
                    </a>
                    <a class="home-category-meta" href="{{ route('categories.show', $key) }}">
                        <span><h3>{{ $category['title'] }}</h3><p>{{ $category['description'] }}</p></span>
                        <span class="category-arrow" aria-hidden="true">→</span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="home-note">
        <h2>İyi bir yolculuk, doğru planla başlar.</h2>
        <p>Seçeneklerinizi inceleyin, size uyan deneyimi belirleyin. Talebiniz bize ulaştığında ayrıntıları birlikte netleştirelim.</p>
    </section>
</main>
@endsection
