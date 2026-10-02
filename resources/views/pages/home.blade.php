@extends('anatema')

@section('title', 'ENTUR | Yolculuğunuz burada başlar')

@section('content')
<style>
    .home-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 30px 0 74px; }
    .home-hero { min-height: 470px; position: relative; display: flex; align-items: flex-end; overflow: hidden; border-radius: 5px; color: #fff; background: #28443b; animation: rise .6s ease both; }
    .hero-slides, .hero-shade { position: absolute; inset: 0; }
    .hero-slide { position: absolute; inset: 0; overflow: hidden; opacity: 0; transform: scale(1.025); transition: opacity .9s ease, transform 6.5s ease; }
    .hero-slide.active { opacity: 1; transform: scale(1); }
    .hero-slide img { width: 100%; height: 100%; display: block; object-fit: cover; }
    .hero-shade { z-index: 1; background: linear-gradient(90deg, rgb(11 31 25 / 76%), rgb(11 31 25 / 18%) 77%), linear-gradient(0deg, rgb(11 31 25 / 27%), transparent 60%); pointer-events: none; }
    .hero-copy { position: relative; z-index: 2; width: min(670px, 100%); padding: 52px 58px 86px; }
    .hero-copy .eyebrow { color: #e1f1e9; }
    .hero-copy h1 { max-width: 610px; min-height: 104px; display: flex; align-items: end; margin: 15px 0 14px; font: 700 46px/1.12 'Manrope', sans-serif; }
    .hero-copy h1 span { display: block; }
    .hero-copy p { max-width: 480px; color: rgb(255 255 255 / 85%); font-size: 15px; line-height: 1.7; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 11px; margin-top: 25px; }
    .hero-button { min-height: 48px; display: inline-flex; align-items: center; justify-content: center; padding: 0 18px; border: 1px solid transparent; border-radius: 4px; background: #fff; color: #1e3f35; font-size: 13px; font-weight: 700; transition: transform .18s, background .18s; }
    .hero-button:hover { transform: translateY(-2px); background: #eaf4ee; }
    .hero-button.secondary { border-color: rgb(255 255 255 / 65%); background: transparent; color: #fff; }
    .hero-button.secondary:hover { background: rgb(255 255 255 / 13%); }
    .hero-controls { position: absolute; z-index: 3; right: 34px; bottom: 31px; display: flex; align-items: center; gap: 10px; }
    .hero-arrow { width: 36px; height: 36px; display: grid; place-items: center; border: 1px solid rgb(255 255 255 / 60%); border-radius: 50%; background: rgb(12 34 29 / 26%); color: #fff; cursor: pointer; transition: background .18s, transform .18s; }
    .hero-arrow:hover { transform: scale(1.05); background: rgb(12 34 29 / 65%); }
    .hero-arrow svg { width: 17px; height: 17px; }
    .hero-dots { display: flex; align-items: center; gap: 7px; margin: 0 3px; }
    .hero-dot { width: 8px; height: 8px; padding: 0; border: 1px solid #fff; border-radius: 50%; background: transparent; cursor: pointer; transition: width .2s, border-radius .2s, background .2s; }
    .hero-dot[aria-current="true"] { width: 23px; border-radius: 5px; background: #fff; }
    .hero-arrow:focus-visible, .hero-dot:focus-visible { outline: 2px solid #fff; outline-offset: 3px; }
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
    @media(max-width: 720px) { .home-main { width: min(100% - 32px, 1120px); padding-top: 20px; } .home-hero { min-height: 460px; } .hero-copy { padding: 34px 26px 86px; } .hero-copy h1 { min-height: 112px; font-size: 36px; } .hero-controls { right: 20px; bottom: 24px; gap: 8px; } .hero-arrow { width: 34px; height: 34px; } .hero-category-grid { grid-template-columns: 1fr; gap: 25px; } .home-category-grid { grid-template-columns: 1fr; gap: 25px; } .home-category img { height: 230px; } .home-section { padding-top: 48px; } .section-top { display: block; } .section-top p { margin-top: 10px; } .home-note { grid-template-columns: 1fr; gap: 8px; margin-top: 50px; } }
    @media(prefers-reduced-motion: reduce) { .hero-slide, .hero-arrow, .hero-dot { transition: none; animation: none; } }
</style>
<main class="home-main">
    <section class="home-hero" aria-label="ENTUR ile seyahat keşfi" aria-roledescription="slayt gösterisi">
        <div class="hero-slides" aria-hidden="true">
            <div class="hero-slide active"><img src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1518837695005-2083093ee35b?auto=format&fit=crop&w=2000&q=85" alt=""></div>
            <div class="hero-slide"><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2000&q=85" alt=""></div>
        </div>
        <div class="hero-shade" aria-hidden="true"></div>
        <div class="hero-copy">
            <span class="eyebrow">Daha çok keşif, daha güzel anılar</span>
            <h1 aria-live="polite"><span id="hero-title">Yolculuğunuz burada başlar.</span></h1>
            <p>Konaklamadan şehir deneyimlerine, size iyi gelecek kaçamağı tek bir yerden planlayın.</p>
            <div class="hero-actions">
                <a class="hero-button" href="{{ route('categories.index') }}">Keşfetmeye başla</a>
                <a class="hero-button secondary" href="{{ route('about') }}">ENTUR’ü tanıyın</a>
            </div>
        </div>
        <div class="hero-controls" aria-label="Slayt kontrolleri">
            <button class="hero-arrow" type="button" data-hero-direction="previous" aria-label="Önceki görsel">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="hero-dots" role="group" aria-label="Görsel seçimi">
                @foreach (range(0, 6) as $slideIndex)
                    <button class="hero-dot" type="button" data-hero-slide="{{ $slideIndex }}" aria-label="{{ $slideIndex + 1 }}. görsel" aria-current="{{ $slideIndex === 0 ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
            <button class="hero-arrow" type="button" data-hero-direction="next" aria-label="Sonraki görsel">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
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
                @php($imageUrl = !empty($item['image_url']) ? $item['image_url'] : (!empty($item['image']) ? 'https://images.unsplash.com/'.$item['image'].'?auto=format&fit=crop&w=900&q=80' : 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=900&q=80'))
                <article class="home-category">
                    <a class="category-image-link" href="{{ route('categories.show', $key) }}" aria-label="{{ $category['title'] }} seçeneklerini gör">
                        <img src="{{ $imageUrl }}" alt="{{ $item['location'] }} konaklama ve gezi atmosferi" loading="lazy">
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
<script>
    (() => {
        const hero = document.querySelector('.home-hero');
        if (!hero) return;

        const titles = [
            'Yolculuğunuz burada başlar.',
            'Yeni manzaralara doğru yola çıkın.',
            'Şehrin ritmini keşfedin.',
            'Doğada kendinize yer açın.',
            'Göl kıyısında yavaşlayın.',
            'Deniz havasıyla yenilenin.',
            'Bir sonraki kaçamağınızı bulun.',
        ];
        const slides = [...hero.querySelectorAll('.hero-slide')];
        const dots = [...hero.querySelectorAll('.hero-dot')];
        const title = hero.querySelector('#hero-title');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let activeIndex = 0;
        let timer;

        const showSlide = (index) => {
            activeIndex = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => slide.classList.toggle('active', slideIndex === activeIndex));
            dots.forEach((dot, dotIndex) => dot.setAttribute('aria-current', String(dotIndex === activeIndex)));
            title.textContent = titles[activeIndex];
        };

        const stopAutoplay = () => window.clearInterval(timer);
        const startAutoplay = () => {
            stopAutoplay();
            if (!reduceMotion.matches) timer = window.setInterval(() => showSlide(activeIndex + 1), 6000);
        };

        hero.querySelectorAll('[data-hero-slide]').forEach((dot) => {
            dot.addEventListener('click', () => {
                showSlide(Number(dot.dataset.heroSlide));
                startAutoplay();
            });
        });
        hero.querySelectorAll('[data-hero-direction]').forEach((button) => {
            button.addEventListener('click', () => {
                showSlide(activeIndex + (button.dataset.heroDirection === 'next' ? 1 : -1));
                startAutoplay();
            });
        });
        hero.addEventListener('mouseenter', stopAutoplay);
        hero.addEventListener('mouseleave', startAutoplay);
        hero.addEventListener('focusin', stopAutoplay);
        hero.addEventListener('focusout', (event) => {
            if (!hero.contains(event.relatedTarget)) startAutoplay();
        });
        reduceMotion.addEventListener('change', startAutoplay);
        startAutoplay();
    })();
</script>
@endsection
