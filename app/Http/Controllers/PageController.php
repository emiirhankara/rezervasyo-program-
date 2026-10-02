<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class PageController extends Controller
{
    private const CATEGORIES = [
        'oteller' => [
            'title' => 'Oteller',
            'eyebrow' => 'Konaklama',
            'description' => 'Şehrin ritmine yakın, konforunuza uygun otelleri keşfedin.',
            'items' => [
                ['slug' => 'pera-house', 'name' => 'Pera House', 'location' => 'Beyoğlu, İstanbul', 'description' => 'Tarihi Pera atmosferinde, şehir keşifleri için merkezi bir konaklama.', 'price' => '₺3.450 / gece', 'amount' => 3450, 'image' => 'photo-1566073771259-6a8506099945'],
                ['slug' => 'kiyi-otel', 'name' => 'Kıyı Otel', 'location' => 'Konyaaltı, Antalya', 'description' => 'Denize birkaç adım mesafede, ferah odalar ve sakin sabahlar.', 'price' => '₺4.200 / gece', 'amount' => 4200, 'image' => 'photo-1571896349842-33c89424de2d'],
                ['slug' => 'tas-konak', 'name' => 'Taş Konak', 'location' => 'Alaçatı, İzmir', 'description' => 'Taş sokakların içinde, avlulu ve samimi bir Ege kaçamağı.', 'price' => '₺3.900 / gece', 'amount' => 3900, 'image' => 'photo-1611892440504-42a792e24d32'],
            ],
        ],
        'etkinlikler' => [
            'title' => 'Etkinlikler',
            'eyebrow' => 'Şehrin içinde',
            'description' => 'Konserlerden atölyelere, takviminize güzel bir an ekleyin.',
            'items' => [
                ['slug' => 'caz-aksami', 'name' => 'Boğaz’da Caz Akşamı', 'location' => 'İstanbul', 'description' => 'Canlı caz, gün batımı ve Boğaz manzarasıyla özel bir akşam.', 'price' => '₺1.250 / kişi', 'amount' => 1250, 'image' => 'photo-1514525253161-7a46d19cd819'],
                ['slug' => 'seramik-atolyesi', 'name' => 'Seramik Atölyesi', 'location' => 'Kadıköy, İstanbul', 'description' => 'Temel teknikleri öğrenin ve kendi seramik parçanızı üretin.', 'price' => '₺850 / kişi', 'amount' => 850, 'image' => 'photo-1565193566173-7a0ee3dbe261'],
                ['slug' => 'antik-kent-yuruyusu', 'name' => 'Antik Kent Yürüyüşü', 'location' => 'Efes, İzmir', 'description' => 'Uzman rehber eşliğinde tarihin izlerini adım adım takip edin.', 'price' => '₺1.600 / kişi', 'amount' => 1600, 'image' => 'photo-1603565816030-6b389eeb23cb'],
            ],
        ],
        'kiralik-villalar' => [
            'title' => 'Kiralık Villalar',
            'eyebrow' => 'Kendinize ait bir yer',
            'description' => 'Birlikte geçirilen uzun sabahlar için size özel villalar.',
            'items' => [
                ['slug' => 'zeytinlik-villa', 'name' => 'Zeytinlik Villa', 'location' => 'Dalyan, Muğla', 'description' => 'Özel havuzu ve geniş bahçesiyle doğanın içinde bir tatil evi.', 'price' => '₺8.500 / gece', 'amount' => 8500, 'image' => 'photo-1613977257363-707ba9348227'],
                ['slug' => 'mavi-teras', 'name' => 'Mavi Teras', 'location' => 'Kaş, Antalya', 'description' => 'Deniz manzaralı terasta gün batımını izleyebileceğiniz sakin bir villa.', 'price' => '₺10.200 / gece', 'amount' => 10200, 'image' => 'photo-1600607687939-ce8a6c25118c'],
                ['slug' => 'tas-ev', 'name' => 'Taş Ev', 'location' => 'Urla, İzmir', 'description' => 'Bağların arasında, doğal taş dokulu ve geniş avlulu bir ev.', 'price' => '₺7.800 / gece', 'amount' => 7800, 'image' => 'photo-1600210492486-724fe5c67fb0'],
            ],
        ],
        'diger' => [
            'title' => 'Diğer Deneyimler',
            'eyebrow' => 'Size özel',
            'description' => 'Organizatörlerin hazırladığı farklı etkinlik ve deneyimleri keşfedin.',
            'items' => [],
        ],
    ];

    public function home(): View
    {
        return view('pages.home', ['categories' => $this->availableCategories()]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'contactListings' => Schema::hasTable('listings') ? DB::table('listings')->where('is_published', true)->orderBy('title')->get(['id', 'title']) : collect(),
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return to_route('login')->with('status', 'Mesaj gönderebilmek için lütfen giriş yapın veya kayıt olun.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
            'listing_id' => ['nullable', Rule::exists('listings', 'id')->where('is_published', true)],
        ]);

        $listing = ! empty($validated['listing_id']) ? DB::table('listings')->where('id', $validated['listing_id'])->first() : null;
        unset($validated['listing_id']);

        DB::table('user_messages')->insert([
            ...$validated,
            'user_id' => Auth::id(),
            'organizer_id' => $listing->organizer_id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('contact')->with('status', 'Mesajınız bize ulaştı. En kısa sürede sizinle iletişime geçeceğiz.');
    }

    public function categories(): View
    {
        return view('pages.categories', ['categories' => $this->availableCategories()]);
    }

    public function category(string $category): View
    {
        $details = self::CATEGORIES[$category] ?? null;

        $listings = Schema::hasTable('listings')
            ? DB::table('listings')->where('category', $category)->where('is_published', true)->get()
            : collect();

        if (! $details) {
            abort_unless($listings->isNotEmpty(), 404);
            $details = [
                'title' => ucfirst(str_replace('-', ' ', $category)),
                'eyebrow' => 'Özel Branş',
                'description' => 'Organizatörlerimiz tarafından sunulan deneyim ve rezervasyon seçenekleri.',
                'items' => [],
            ];
        }

        $listedItems = $listings->map(fn ($listing) => [
            'slug' => 'listing:'.$listing->id,
            'name' => $listing->title,
            'location' => $listing->location,
            'description' => $listing->description,
            'price' => '₺'.number_format($listing->price, 2, ',', '.').(in_array($category, ['oteller', 'kiralik-villalar'], true) ? ' / gece' : ' / kişi'),
            'amount' => (float) $listing->price,
            'listing_id' => $listing->id,
            'image_url' => $listing->image_url,
            'image' => null,
        ])->all();

        $details['items'] = [...$details['items'], ...$listedItems];
        $selectedItem = collect($details['items'])->firstWhere('slug', request('secim'));

        return view('pages.category', [
            'categoryKey' => $category,
            'category' => $details,
            'selectedItem' => $selectedItem,
        ]);
    }

    public function storeReservation(Request $request, string $category): RedirectResponse
    {
        if (! Auth::check()) {
            return to_route('login')->with('status', 'Rezervasyon yapabilmek için lütfen giriş yapın veya kayıt olun.');
        }

        abort_unless(Auth::user()->role === 'customer', 403, 'Rezervasyon oluşturmak için müşteri hesabı gerekir.');

        $baseItems = self::CATEGORIES[$category]['items'] ?? [];
        $publishedListings = DB::table('listings')->where('category', $category)->where('is_published', true)->get();
        $allowedItems = [...array_column($baseItems, 'slug'), ...$publishedListings->map(fn ($listing) => 'listing:'.$listing->id)->all()];

        abort_if(empty($allowedItems), 404);

        $validated = $request->validate([
            'item' => ['required', Rule::in($allowedItems)],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'guests' => ['required', 'integer', 'between:1,20'],
            'message' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', Rule::in(['credit_card', 'bank_transfer', 'coupon'])],
            'coupon_code' => ['nullable', 'string', 'max:60'],
            'card_holder' => ['nullable', 'string', 'max:120'],
            'card_number' => ['nullable', 'string', 'max:30'],
            'card_expiry' => ['nullable', 'string', 'max:10'],
            'card_cvv' => ['nullable', 'string', 'max:4'],
            'sender_name' => ['nullable', 'string', 'max:120'],
            'transfer_reference' => ['nullable', 'string', 'max:60'],
        ]);

        $listing = null;
        if (str_starts_with($validated['item'], 'listing:')) {
            $listingId = (int) substr($validated['item'], 8);
            $listing = $publishedListings->firstWhere('id', $listingId);
            abort_unless($listing !== null, 404);
            $item = ['name' => $listing->title, 'amount' => (float) $listing->price, 'listing_id' => $listing->id];
            abort_if($listing->capacity - $listing->reserved_count < (int) $validated['guests'], 422, 'Seçilen kontenjan artık müsait değil.');
            $organizerId = $listing->organizer_id;
        } else {
            $item = collect($baseItems)->firstWhere('slug', $validated['item']);
            $organizerId = null;
        }

        $periodCount = in_array($category, ['etkinlikler', 'diger'], true)
            ? (int) $validated['guests']
            : max(1, Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'] ?? $validated['start_date'])));
        $gross = round($item['amount'] * $periodCount, 2);
        $discount = 0.0;
        $coupon = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = DB::table('coupons')->whereRaw('UPPER(code) = ?', [strtoupper($validated['coupon_code'])])->where('is_active', true)->lockForUpdate()->first();
            if (! $coupon || ($coupon->expires_at && Carbon::parse($coupon->expires_at)->isPast()) || ($coupon->max_uses !== null && $coupon->uses_count >= $coupon->max_uses)) {
                return back()->withErrors(['coupon_code' => 'Kupon geçersiz, süresi dolmuş veya kullanım limiti dolmuş.'])->withInput();
            }
            $discount = $coupon->discount_type === 'percent' ? $gross * min(100, (float) $coupon->discount_value) / 100 : min($gross, (float) $coupon->discount_value);
        }

        if ($validated['payment_method'] === 'coupon' && $discount < $gross) {
            return back()->withErrors(['coupon_code' => 'Kuponla ödeme için kuponun toplam tutarı karşılaması gerekir.'])->withInput();
        }

        $total = max(0, round($gross - $discount, 2));

        // Credit card or zero-amount coupon = instant paid & confirmed!
        // Bank transfer = pending admin approval
        $paymentStatus = ($validated['payment_method'] === 'credit_card' || $total === 0) ? 'paid' : 'pending';
        $reservationStatus = $paymentStatus === 'paid' ? 'confirmed' : 'awaiting_payment';

        $providerRef = null;
        $paymentNote = null;
        if ($validated['payment_method'] === 'credit_card') {
            $digitsOnly = preg_replace('/\D/', '', $request->input('card_number', ''));
            $last4 = strlen($digitsOnly) >= 4 ? substr($digitsOnly, -4) : '4242';
            $providerRef = 'Kredi Kartı (**** ' . $last4 . ')';
            $paymentNote = 'Kredi kartı tahsilatı başarıyla gerçekleşti (Kart Sahibi: ' . ($request->input('card_holder') ?: Auth::user()->name) . ')';
        } elseif ($validated['payment_method'] === 'bank_transfer') {
            $providerRef = $request->input('transfer_reference') ? 'Dekont/Ref: ' . $request->input('transfer_reference') : 'Havale/EFT Bildirimi';
            $paymentNote = 'Havale/EFT ödeme onayı bekleniyor. Gönderen: ' . ($request->input('sender_name') ?: Auth::user()->name);
        } else {
            $providerRef = 'Kupon (' . ($coupon->code ?? 'KUPON') . ')';
            $paymentNote = 'Kupon ile tam tutarı karşılanan rezervasyon.';
        }

        DB::transaction(function () use ($validated, $category, $item, $listing, $organizerId, $total, $discount, $coupon, $paymentStatus, $reservationStatus, $providerRef, $paymentNote) {
            $customer = Auth::user();
            $reservationId = DB::table('reservations')->insertGetId([
                'user_id' => $customer->id,
                'organizer_id' => $organizerId,
                'listing_id' => $item['listing_id'] ?? null,
                'category' => $category,
                'item' => $item['name'],
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'guests' => $validated['guests'],
                'message' => $validated['message'] ?? null,
                'total_amount' => $total,
                'status' => $reservationStatus,
                'payment_status' => $paymentStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payments')->insert([
                'reservation_id' => $reservationId,
                'user_id' => $customer->id,
                'amount' => $total,
                'method' => $validated['payment_method'],
                'status' => $paymentStatus,
                'coupon_code' => $coupon->code ?? null,
                'provider_reference' => $providerRef,
                'note' => $paymentNote,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($coupon && $discount > 0) {
                DB::table('coupons')->where('id', $coupon->id)->increment('uses_count');
            }
            if ($listing) {
                DB::table('listings')->where('id', $listing->id)->increment('reserved_count', (int) $validated['guests']);
            }
        });

        $message = match ($validated['payment_method']) {
            'credit_card' => 'Kredi kartı ödemeniz başarıyla tamamlandı ve rezervasyonunuz kesinleşti!',
            'coupon' => 'Kupon kodunuz uygulandı ve rezervasyonunuz ücretsiz olarak onaylandı!',
            default => 'Rezervasyon talebiniz oluşturuldu. Havale/EFT kontrolünden sonra rezervasyonunuz kesinleşecektir.',
        };

        return to_route('categories.show', ['category' => $category, 'secim' => $validated['item']])
            ->with('status', $message);
    }

    private function availableCategories(): array
    {
        $categories = self::CATEGORIES;
        unset($categories['diger']);
        if (! Schema::hasTable('listings')) return $categories;
        $listings = DB::table('listings')->where('is_published', true)->get();

        foreach ($listings as $listing) {
            if (! isset($categories[$listing->category])) {
                $categories[$listing->category] = [
                    'title' => ucfirst(str_replace('-', ' ', $listing->category)),
                    'description' => 'Organizatörlerin yayınladığı seçenekleri keşfedin.',
                    'items' => [],
                ];
            }

            if ($categories[$listing->category]['items'] === []) {
                $categories[$listing->category]['items'][] = [
                    'slug' => 'listing:'.$listing->id,
                    'name' => $listing->title,
                    'location' => $listing->location,
                    'description' => $listing->description,
                    'price' => '₺'.number_format($listing->price, 2, ',', '.'),
                    'amount' => (float) $listing->price,
                    'image' => null,
                    'image_url' => $listing->image_url,
                ];
            }
        }

        return $categories;
    }
}