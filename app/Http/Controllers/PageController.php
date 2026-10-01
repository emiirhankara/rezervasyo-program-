<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    private const CATEGORIES = [
        'oteller' => [
            'title' => 'Oteller',
            'eyebrow' => 'Konaklama',
            'description' => 'Şehrin ritmine yakın, konforunuza uygun otelleri keşfedin.',
            'items' => [
                ['slug' => 'pera-house', 'name' => 'Pera House', 'location' => 'Beyoğlu, İstanbul', 'description' => 'Tarihi Pera atmosferinde, şehir keşifleri için merkezi bir konaklama.', 'price' => '₺3.450 / gece', 'image' => 'photo-1566073771259-6a8506099945'],
                ['slug' => 'kiyi-otel', 'name' => 'Kıyı Otel', 'location' => 'Konyaaltı, Antalya', 'description' => 'Denize birkaç adım mesafede, ferah odalar ve sakin sabahlar.', 'price' => '₺4.200 / gece', 'image' => 'photo-1571896349842-33c89424de2d'],
                ['slug' => 'tas-konak', 'name' => 'Taş Konak', 'location' => 'Alaçatı, İzmir', 'description' => 'Taş sokakların içinde, avlulu ve samimi bir Ege kaçamağı.', 'price' => '₺3.900 / gece', 'image' => 'photo-1611892440504-42a792e24d32'],
            ],
        ],
        'etkinlikler' => [
            'title' => 'Etkinlikler',
            'eyebrow' => 'Şehrin içinde',
            'description' => 'Konserlerden atölyelere, takviminize güzel bir an ekleyin.',
            'items' => [
                ['slug' => 'caz-aksami', 'name' => 'Boğaz’da Caz Akşamı', 'location' => 'İstanbul', 'description' => 'Canlı caz, gün batımı ve Boğaz manzarasıyla özel bir akşam.', 'price' => '₺1.250 / kişi', 'image' => 'photo-1514525253161-7a46d19cd819'],
                ['slug' => 'seramik-atolyesi', 'name' => 'Seramik Atölyesi', 'location' => 'Kadıköy, İstanbul', 'description' => 'Temel teknikleri öğrenin ve kendi seramik parçanızı üretin.', 'price' => '₺850 / kişi', 'image' => 'photo-1565193566173-7a0ee3dbe261'],
                ['slug' => 'antik-kent-yuruyusu', 'name' => 'Antik Kent Yürüyüşü', 'location' => 'Efes, İzmir', 'description' => 'Uzman rehber eşliğinde tarihin izlerini adım adım takip edin.', 'price' => '₺1.600 / kişi', 'image' => 'photo-1603565816030-6b389eeb23cb'],
            ],
        ],
        'kiralik-villalar' => [
            'title' => 'Kiralık Villalar',
            'eyebrow' => 'Kendinize ait bir yer',
            'description' => 'Birlikte geçirilen uzun sabahlar için size özel villalar.',
            'items' => [
                ['slug' => 'zeytinlik-villa', 'name' => 'Zeytinlik Villa', 'location' => 'Dalyan, Muğla', 'description' => 'Özel havuzu ve geniş bahçesiyle doğanın içinde bir tatil evi.', 'price' => '₺8.500 / gece', 'image' => 'photo-1613977257363-707ba9348227'],
                ['slug' => 'mavi-teras', 'name' => 'Mavi Teras', 'location' => 'Kaş, Antalya', 'description' => 'Deniz manzaralı terasta gün batımını izleyebileceğiniz sakin bir villa.', 'price' => '₺10.200 / gece', 'image' => 'photo-1600607687939-ce8a6c25118c'],
                ['slug' => 'tas-ev', 'name' => 'Taş Ev', 'location' => 'Urla, İzmir', 'description' => 'Bağların arasında, doğal taş dokulu ve geniş avlulu bir ev.', 'price' => '₺7.800 / gece', 'image' => 'photo-1600210492486-724fe5c67fb0'],
            ],
        ],
    ];

    public function home(): View
    {
        return view('pages.home', ['categories' => self::CATEGORIES]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        DB::table('user_messages')->insert([
            ...$validated,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('contact')->with('status', 'Mesajınız bize ulaştı. En kısa sürede sizinle iletişime geçeceğiz.');
    }

    public function categories(): View
    {
        return view('pages.categories', ['categories' => self::CATEGORIES]);
    }

    public function category(string $category): View
    {
        abort_unless(isset(self::CATEGORIES[$category]), 404);

        $details = self::CATEGORIES[$category];
        $selectedItem = collect($details['items'])->firstWhere('slug', request('secim'));

        return view('pages.category', [
            'categoryKey' => $category,
            'category' => $details,
            'selectedItem' => $selectedItem,
        ]);
    }

    public function storeReservation(Request $request, string $category): RedirectResponse
    {
        abort_unless(isset(self::CATEGORIES[$category]), 404);

        $items = self::CATEGORIES[$category]['items'];
        $validated = $request->validate([
            'item' => ['required', Rule::in(array_column($items, 'slug'))],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'guests' => ['required', 'integer', 'between:1,20'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);
        $item = collect($items)->firstWhere('slug', $validated['item']);

        DB::table('reservations')->insert([
            'category' => $category,
            'item' => $item['name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'guests' => $validated['guests'],
            'message' => $validated['message'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('categories.show', ['category' => $category, 'secim' => $validated['item']])
            ->with('status', 'Rezervasyon talebiniz alındı. Detayları netleştirmek için sizinle iletişime geçeceğiz.');
    }
}