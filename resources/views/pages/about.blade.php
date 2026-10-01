@extends('anatema')

@section('title', 'Hakkımızda | ENTUR')

@section('content')
<style>
    .about-main { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 64px 0 80px; }
    
    .about-lead { max-width: 820px; margin-bottom: 42px; animation: rise .55s ease both; }
   
    .about-lead h1 { max-width: 760px; margin: 15px 0 18px; font: 700 42px/1.16 'Manrope', sans-serif; }
    
    .about-lead p { max-width: 690px; margin: 0; color: var(--muted); font-size: 16px; line-height: 1.8; }
   
    .about-image { width: 100%; height: 390px; object-fit: cover; display: block; border-radius: 4px; }
   
    .about-story { display: grid; grid-template-columns: .8fr 1.2fr; gap: 70px; padding: 54px 0; border-bottom: 1px solid var(--line); }
   
    .about-story h2 { max-width: 320px; font: 700 27px/1.3 'Manrope', sans-serif; }
   
    .about-story p { margin: 0 0 14px; color: #64716c; font-size: 14px; line-height: 1.9; }
 
    .about-values { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; padding-top: 34px; }
 
    .about-value { padding-top: 16px; border-top: 2px solid var(--coral); }
   
    .about-value span { color: var(--green); font: 700 12px 'Manrope', sans-serif; }
    
    .about-value h3 { margin: 9px 0; font: 700 16px 'Manrope', sans-serif; }
   
    .about-value p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.7; }
    
    .about-action { display: inline-flex; align-items: center; min-height: 48px; margin-top: 35px; padding: 0 17px; border-radius: 4px; background: var(--green); color: #fff; font-size: 13px; font-weight: 700; }
    
    .about-action:hover { background: var(--green-dark); }
    @media(max-width: 700px) { .about-main { width: min(100% - 32px, 1120px); padding: 40px 0 55px; } .about-lead h1 { font-size: 34px; } .about-image { height: 280px; } .about-story { grid-template-columns: 1fr; gap: 5px; padding: 35px 0; } .about-values { grid-template-columns: 1fr; gap: 22px; } }
</style>
<main class="about-main">
    <section class="about-lead">
        <span class="eyebrow">Hakkımızda</span>
        <h1>Yolculukların en güzel yanı, size ait hissettirmesi.</h1>
        <p>ENTUR, seyahat planlamasını daha anlaşılır ve daha kişisel kılmak için yola çıktı. Konaklama ve deneyimleri bir araya getiriyor, iyi bir yolculuk için ihtiyacınız olan seçimi kolaylaştırıyoruz.</p>
    </section>
    <img class="about-image" src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1800&q=85" alt="Göl kıyısında dağ manzaralı bir yolculuk">
    <section class="about-story">
        <h2>Planlamadan güzel anılara kadar yanınızdayız.</h2>
        <div>
            <p>Her yolculuk başka bir şey aramakla başlar: bazen yeni bir şehir, bazen sevdiklerinizle sakin bir hafta sonu, bazen de uzun zamandır görmek istediğiniz bir etkinlik. ENTUR’de farklı seçenekleri kolayca karşılaştırıp size uyanı seçebilirsiniz.</p>
            <p>Talebinizi bize ilettiğinizde ayrıntıları birlikte netleştiririz. Şeffaf iletişim ve ihtiyaçlarınıza göre şekillenen destek, yolculuk yaklaşımımızın temelini oluşturur.</p>
        </div>
    </section>
    <section class="about-values" aria-label="ENTUR değerleri">
        <article class="about-value"><span>01</span><h3>Özenli seçim</h3><p>Her kategori için yolculuğunuza değer katacak seçeneklere odaklanıyoruz.</p></article>
        <article class="about-value"><span>02</span><h3>Kolay planlama</h3><p>İhtiyacınızı belirleyip talebinizi birkaç adımda bize ulaştırabilirsiniz.</p></article>
        <article class="about-value"><span>03</span><h3>İnsan odaklı destek</h3><p>Talebinizden yolculuk ayrıntılarına kadar size eşlik ediyoruz.</p></article>
    </section>
    <a class="about-action" href="{{ route('categories.index') }}">Seçenekleri keşfet <span aria-hidden="true">&nbsp;→</span></a>
</main>
@endsection
