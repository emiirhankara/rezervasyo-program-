<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Admin User
        $systemAdmin = User::updateOrCreate(
            ['email' => 'admin@entur.com'],
            [
                'name' => 'Sistem Yöneticisi',
                'phone' => '0555 111 22 33',
                'role' => 'system_admin',
                'organizer_status' => 'not_applicable',
                'password' => Hash::make('Admin123456!'),
            ]
        );

        // 2. Active Organizer User
        $organizer = User::updateOrCreate(
            ['email' => 'organizer@entur.com'],
            [
                'name' => 'Serkan Kaya (Kaya Etkinlik & Turizm)',
                'phone' => '0532 999 88 77',
                'role' => 'organizer',
                'organizer_status' => 'active',
                'password' => Hash::make('Organizer123456!'),
            ]
        );

        // 3. Organizer's Annual Fee & Installments
        $fee = DB::table('organizer_fees')->where('organizer_id', $organizer->id)->where('year', now()->year)->first();
        if (! $fee) {
            $feeId = DB::table('organizer_fees')->insertGetId([
                'organizer_id' => $organizer->id,
                'year' => now()->year,
                'annual_amount' => 12000.00,
                'status' => 'paid',
                'installment_count' => 3,
                'paid_at' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('organizer_fee_installments')->insert([
                [
                    'organizer_fee_id' => $feeId,
                    'installment_no' => 1,
                    'amount' => 4000.00,
                    'due_date' => now()->subMonths(2)->toDateString(),
                    'status' => 'paid',
                    'paid_at' => now()->subMonths(2)->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'organizer_fee_id' => $feeId,
                    'installment_no' => 2,
                    'amount' => 4000.00,
                    'due_date' => now()->subMonth()->toDateString(),
                    'status' => 'paid',
                    'paid_at' => now()->subMonth()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'organizer_fee_id' => $feeId,
                    'installment_no' => 3,
                    'amount' => 4000.00,
                    'due_date' => now()->addMonth()->toDateString(),
                    'status' => 'unpaid',
                    'paid_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 4. Sample Organizer Listings
        $jazzListingId = DB::table('listings')->where('slug', 'bogazda-caz-aksami')->value('id');
        if (! $jazzListingId) {
            $jazzListingId = DB::table('listings')->insertGetId([
                'organizer_id' => $organizer->id,
                'category' => 'etkinlikler',
                'title' => 'Boğaz’da Caz Akşamı',
                'slug' => 'bogazda-caz-aksami',
                'location' => 'Ortaköy, İstanbul',
                'description' => 'Boğaz manzarası eşliğinde gün batımında canlı caz performansı ve özel ikramlar.',
                'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=900&q=80',
                'price' => 1250.00,
                'capacity' => 60,
                'reserved_count' => 18,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $hotelListingId = DB::table('listings')->where('slug', 'alacati-tas-konak-butik')->value('id');
        if (! $hotelListingId) {
            $hotelListingId = DB::table('listings')->insertGetId([
                'organizer_id' => $organizer->id,
                'category' => 'oteller',
                'title' => 'Alaçatı Taş Konak Butik Otel',
                'slug' => 'alacati-tas-konak-butik',
                'location' => 'Alaçatı, İzmir',
                'description' => 'Tarihi Rum mimarisi, begonvillerle süslü avlu ve gurme Ege kahvaltısı ile unutulmaz bir tatil.',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
                'price' => 3900.00,
                'capacity' => 12,
                'reserved_count' => 4,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Custom Branch Listing (Diğer / Özel Branş: Yat Turu)
        $boatListingId = DB::table('listings')->where('slug', 'mavi-tur-gulet-gezisi')->value('id');
        if (! $boatListingId) {
            $boatListingId = DB::table('listings')->insertGetId([
                'organizer_id' => $organizer->id,
                'category' => 'yat-ve-tekne-turu',
                'title' => 'Göcek Koyları Özel Gulet Turu',
                'slug' => 'mavi-tur-gulet-gezisi',
                'location' => 'Göcek, Fethiye',
                'description' => 'Akdeniz’in berrak sularında günlük özel tekne turu, öğle yemeği ve şnorkel deneyimi.',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80',
                'price' => 2800.00,
                'capacity' => 20,
                'reserved_count' => 6,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Draft Listing awaiting System Admin approval
        if (! DB::table('listings')->where('slug', 'kapadokya-balon-festivali')->exists()) {
            DB::table('listings')->insert([
                'organizer_id' => $organizer->id,
                'category' => 'etkinlikler',
                'title' => 'Kapadokya Sıcak Hava Balon Festivali',
                'slug' => 'kapadokya-balon-festivali',
                'location' => 'Göreme, Nevşehir',
                'description' => 'Peri bacaları üzerinde gün doğumunda sıcak hava balonu uçuşu ve festival alanı aktiviteleri.',
                'image_url' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=900&q=80',
                'price' => 4500.00,
                'capacity' => 40,
                'reserved_count' => 0,
                'is_published' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Customer User
        $customer = User::updateOrCreate(
            ['email' => 'musteri@entur.com'],
            [
                'name' => 'Emirhan Kara',
                'phone' => '0544 333 22 11',
                'role' => 'customer',
                'organizer_status' => 'not_applicable',
                'password' => Hash::make('Musteri123456!'),
            ]
        );

        // 6. Sample Customer Reservations & Payments
        if (DB::table('reservations')->where('user_id', $customer->id)->count() === 0) {
            // Reservation 1: Paid via Credit Card
            $res1 = DB::table('reservations')->insertGetId([
                'user_id' => $customer->id,
                'organizer_id' => $organizer->id,
                'listing_id' => $jazzListingId,
                'category' => 'etkinlikler',
                'item' => 'Boğaz’da Caz Akşamı',
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => null,
                'guests' => 2,
                'message' => 'Ön sıra masa tercih ediyoruz.',
                'total_amount' => 2500.00,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            DB::table('payments')->insert([
                'reservation_id' => $res1,
                'user_id' => $customer->id,
                'amount' => 2500.00,
                'method' => 'credit_card',
                'status' => 'paid',
                'provider_reference' => 'Kredi Kartı (**** 4242)',
                'note' => 'Kredi kartı tahsilatı başarıyla gerçekleşti.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            // Reservation 2: Bank Transfer awaiting approval
            $res2 = DB::table('reservations')->insertGetId([
                'user_id' => $customer->id,
                'organizer_id' => $organizer->id,
                'listing_id' => $hotelListingId,
                'category' => 'oteller',
                'item' => 'Alaçatı Taş Konak Butik Otel',
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'start_date' => now()->addDays(12)->toDateString(),
                'end_date' => now()->addDays(14)->toDateString(),
                'guests' => 2,
                'message' => 'Geç giriş yapacağız.',
                'total_amount' => 7800.00,
                'status' => 'awaiting_payment',
                'payment_status' => 'pending',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ]);

            DB::table('payments')->insert([
                'reservation_id' => $res2,
                'user_id' => $customer->id,
                'amount' => 7800.00,
                'method' => 'bank_transfer',
                'status' => 'pending',
                'provider_reference' => 'Dekont No: 20261002-TR98',
                'note' => 'Havale/EFT dekontu gönderildi, sistem yöneticisi onayı bekleniyor.',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ]);
        }

        // 7. Organizer Expenses
        if (DB::table('organizer_expenses')->where('organizer_id', $organizer->id)->count() === 0) {
            DB::table('organizer_expenses')->insert([
                [
                    'organizer_id' => $organizer->id,
                    'title' => 'Ortaköy Tekne & İskele Kirası',
                    'category' => 'Mekan & Konum',
                    'amount' => 4500.00,
                    'expense_date' => now()->subDays(3)->toDateString(),
                    'note' => 'Caz gecesi için tekne platform kiralama bedeli.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'organizer_id' => $organizer->id,
                    'title' => 'Akustik Ses Sistemi & Işık Ekipmanı',
                    'category' => 'Teknik Ekipman',
                    'amount' => 2200.00,
                    'expense_date' => now()->subDay()->toDateString(),
                    'note' => 'Konser ses sistemi kiralama faturası #8821.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 8. Customer Inquiries / Messages
        if (DB::table('user_messages')->where('user_id', $customer->id)->count() === 0) {
            DB::table('user_messages')->insert([
                [
                    'user_id' => $customer->id,
                    'organizer_id' => $organizer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'message' => 'Boğaz Caz gecesinde yağmur ihtimali olursa etkinlik kapalı alana alınıyor mu?',
                    'status' => 'new',
                    'created_at' => now()->subHours(12),
                    'updated_at' => now()->subHours(12),
                ],
            ]);
        }

        // 9. Promotional Coupons
        DB::table('coupons')->updateOrInsert(
            ['code' => 'HOSGELDIN'],
            [
                'discount_type' => 'percent',
                'discount_value' => 20.00,
                'max_uses' => 500,
                'uses_count' => 12,
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('coupons')->updateOrInsert(
            ['code' => 'ENTUR100'],
            [
                'discount_type' => 'fixed',
                'discount_value' => 100.00,
                'max_uses' => 200,
                'uses_count' => 5,
                'is_active' => true,
                'expires_at' => now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('coupons')->updateOrInsert(
            ['code' => 'UCRETSIZ'],
            [
                'discount_type' => 'percent',
                'discount_value' => 100.00,
                'max_uses' => 100,
                'uses_count' => 1,
                'is_active' => true,
                'expires_at' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
