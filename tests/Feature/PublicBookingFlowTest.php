<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Tests\TestCase;

class PublicBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_and_reservation_categories_render(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Yolculuğunuz burada başlar.');
        $this->get(route('about'))->assertOk()->assertSee('Hakkımızda');
        $this->get(route('contact'))->assertOk()->assertSee('Mesajınızı gönderin');
        $this->get(route('categories.index'))->assertOk()->assertSee('Kiralık Villalar');
        $this->get(route('categories.show', 'oteller'))->assertOk()->assertSee('Pera House');
        $this->get(route('categories.show', 'etkinlikler'))->assertOk()->assertSee('Seramik Atölyesi');
        $this->get(route('categories.show', 'kiralik-villalar'))->assertOk()->assertSee('Zeytinlik Villa');
    }

    public function test_contact_message_is_saved(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $this->actingAs($user)->post(route('contact.store'), [
            'name' => 'Test Kullanıcısı',
            'email' => 'test@example.com',
            'phone' => '05001234567',
            'message' => 'Rezervasyon seçenekleri hakkında bilgi almak istiyorum.',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('user_messages', [
            'user_id' => $user->id,
            'email' => 'test@example.com',
            'message' => 'Rezervasyon seçenekleri hakkında bilgi almak istiyorum.',
        ]);
    }

    public function test_reservation_request_is_saved_for_selected_item(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com', 'phone' => '05001234567']);

        $this->actingAs($user)->post(route('reservations.store', 'oteller'), [
            'item' => 'pera-house',
            'name' => 'Test Kullanıcısı',
            'email' => 'test@example.com',
            'phone' => '05001234567',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'guests' => 2,
            'message' => 'Sessiz oda tercihimdir.',
            'payment_method' => 'bank_transfer',
        ])->assertRedirect(route('categories.show', ['category' => 'oteller', 'secim' => 'pera-house']));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'category' => 'oteller',
            'item' => 'Pera House',
            'email' => 'test@example.com',
            'guests' => 2,
        ]);
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'amount' => 6900,
        ]);
    }

    public function test_reservation_rejects_an_item_from_another_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('categories.show', 'oteller'))
            ->post(route('reservations.store', 'oteller'), [
                'item' => 'caz-aksami',
                'name' => 'Test Kullanıcısı',
                'email' => 'test@example.com',
                'phone' => '05001234567',
                'start_date' => now()->addDays(5)->toDateString(),
                'guests' => 2,
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors('item');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_guest_cannot_send_contact_messages_or_create_reservations(): void
    {
        $this->post(route('contact.store'), [])->assertRedirect(route('login'));
        $this->post(route('reservations.store', 'oteller'), [])->assertRedirect(route('login'));
        $this->assertDatabaseCount('user_messages', 0);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_full_value_coupon_is_recorded_as_a_paid_reservation(): void
    {
        $user = User::factory()->create();
        DB::table('coupons')->insert([
            'code' => 'TAMINDIRIM',
            'discount_type' => 'percent',
            'discount_value' => 100,
            'uses_count' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)->post(route('reservations.store', 'oteller'), [
            'item' => 'pera-house',
            'start_date' => now()->addDays(5)->toDateString(),
            'guests' => 2,
            'payment_method' => 'coupon',
            'coupon_code' => 'tamindirim',
        ])->assertRedirect(route('categories.show', ['category' => 'oteller', 'secim' => 'pera-house']));

        $this->assertDatabaseHas('reservations', ['user_id' => $user->id, 'total_amount' => 0, 'payment_status' => 'paid']);
        $this->assertDatabaseHas('payments', ['user_id' => $user->id, 'method' => 'coupon', 'coupon_code' => 'TAMINDIRIM', 'status' => 'paid']);
        $this->assertDatabaseHas('coupons', ['code' => 'TAMINDIRIM', 'uses_count' => 1]);
    }

    public function test_contact_message_can_be_routed_to_the_selected_listing_organizer(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $customer = User::factory()->create();
        $listingId = DB::table('listings')->insertGetId([
            'organizer_id' => $organizer->id,
            'category' => 'etkinlikler',
            'title' => 'Mesaj Test Etkinliği',
            'slug' => 'mesaj-test-etkinligi',
            'location' => 'İstanbul',
            'description' => 'Mesaj eşleştirme testi',
            'price' => 500,
            'capacity' => 20,
            'reserved_count' => 0,
            'is_published' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($customer)->post(route('contact.store'), [
            'name' => $customer->name,
            'email' => $customer->email,
            'message' => 'Bu etkinliğe dair bir sorum var.',
            'listing_id' => $listingId,
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('user_messages', [
            'user_id' => $customer->id,
            'organizer_id' => $organizer->id,
            'message' => 'Bu etkinliğe dair bir sorum var.',
        ]);
    }
}
