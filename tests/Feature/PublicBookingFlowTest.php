<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->post(route('contact.store'), [
            'name' => 'Test Kullanıcısı',
            'email' => 'test@example.com',
            'phone' => '05001234567',
            'message' => 'Rezervasyon seçenekleri hakkında bilgi almak istiyorum.',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('user_messages', [
            'email' => 'test@example.com',
            'message' => 'Rezervasyon seçenekleri hakkında bilgi almak istiyorum.',
        ]);
    }

    public function test_reservation_request_is_saved_for_selected_item(): void
    {
        $this->post(route('reservations.store', 'oteller'), [
            'item' => 'pera-house',
            'name' => 'Test Kullanıcısı',
            'email' => 'test@example.com',
            'phone' => '05001234567',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'guests' => 2,
            'message' => 'Sessiz oda tercihimdir.',
        ])->assertRedirect(route('categories.show', ['category' => 'oteller', 'secim' => 'pera-house']));

        $this->assertDatabaseHas('reservations', [
            'category' => 'oteller',
            'item' => 'Pera House',
            'email' => 'test@example.com',
            'guests' => 2,
        ]);
    }

    public function test_reservation_rejects_an_item_from_another_category(): void
    {
        $this->from(route('categories.show', 'oteller'))
            ->post(route('reservations.store', 'oteller'), [
                'item' => 'caz-aksami',
                'name' => 'Test Kullanıcısı',
                'email' => 'test@example.com',
                'phone' => '05001234567',
                'start_date' => now()->addDays(5)->toDateString(),
                'guests' => 2,
            ])
            ->assertSessionHasErrors('item');

        $this->assertDatabaseCount('reservations', 0);
    }
}
