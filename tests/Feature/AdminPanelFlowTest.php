<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_requires_the_selected_role_to_match_the_account(): void
    {
        User::factory()->create([
            'email' => 'sysadmin@example.com',
            'password' => 'StrongAdminPass123',
            'role' => 'system_admin',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'role' => 'organizer',
                'email' => 'sysadmin@example.com',
                'password' => 'StrongAdminPass123',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_organizer_cannot_login_as_system_admin(): void
    {
        User::factory()->create([
            'email' => 'org@example.com',
            'password' => 'StrongOrgPass123',
            'role' => 'organizer',
            'organizer_status' => 'active',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'role' => 'system_admin',
                'email' => 'org@example.com',
                'password' => 'StrongOrgPass123',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_system_admin_and_organizer_can_only_open_their_own_panel(): void
    {
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $organizer = User::factory()->create(['role' => 'organizer', 'organizer_status' => 'active']);
        \Illuminate\Support\Facades\DB::table('organizer_fees')->insert([
            'organizer_id' => $organizer->id,
            'year' => now()->year,
            'annual_amount' => 12000,
            'status' => 'paid',
            'installment_count' => 1,
            'paid_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($systemAdmin)->get(route('system.dashboard'))->assertOk();
        $this->actingAs($systemAdmin)->get(route('organizer.dashboard'))->assertForbidden();
        $this->actingAs($organizer)->get(route('organizer.dashboard'))->assertOk();
        $this->actingAs($organizer)->get(route('system.dashboard'))->assertForbidden();
    }

    public function test_system_admin_can_create_organizer_and_installment_plan(): void
    {
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);

        $this->actingAs($systemAdmin)->post(route('system.organizers.store'), [
            'name' => 'Organizatör Test',
            'email' => 'organizer@example.com',
            'phone' => '05001239876',
            'password' => 'TemporaryAdminPass123',
            'annual_amount' => 12000,
            'installment_count' => 3,
        ])->assertRedirect(route('system.dashboard'));

        $organizer = User::where('email', 'organizer@example.com')->firstOrFail();
        $this->assertSame('organizer', $organizer->role);
        $this->assertSame('pending', $organizer->organizer_status);
        $this->assertTrue(Hash::check('TemporaryAdminPass123', $organizer->password));
        $this->assertDatabaseCount('organizer_fee_installments', 3);
        $this->assertDatabaseHas('organizer_fees', ['organizer_id' => $organizer->id, 'status' => 'unpaid']);
    }

    public function test_system_admin_can_approve_a_bank_transfer_and_confirm_its_reservation(): void
    {
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $customer = User::factory()->create();
        $reservationId = \Illuminate\Support\Facades\DB::table('reservations')->insertGetId([
            'user_id' => $customer->id,
            'category' => 'oteller',
            'item' => 'Pera House',
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'start_date' => now()->addDays(3)->toDateString(),
            'guests' => 2,
            'total_amount' => 6900,
            'status' => 'awaiting_payment',
            'payment_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $paymentId = \Illuminate\Support\Facades\DB::table('payments')->insertGetId([
            'reservation_id' => $reservationId,
            'user_id' => $customer->id,
            'amount' => 6900,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($systemAdmin)->post(route('system.payments.approve', $paymentId))->assertRedirect(route('system.dashboard'));

        $this->assertDatabaseHas('payments', ['id' => $paymentId, 'status' => 'paid']);
        $this->assertDatabaseHas('reservations', ['id' => $reservationId, 'status' => 'confirmed', 'payment_status' => 'paid']);
    }

    public function test_organizer_listing_requires_admin_approval_before_customer_booking(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer', 'organizer_status' => 'active']);
        \Illuminate\Support\Facades\DB::table('organizer_fees')->insert([
            'organizer_id' => $organizer->id,
            'year' => now()->year,
            'annual_amount' => 9000,
            'status' => 'paid',
            'installment_count' => 1,
            'paid_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($organizer)->post(route('organizer.listings.store'), [
            'category' => 'etkinlikler',
            'title' => 'Test Konseri',
            'location' => 'İstanbul',
            'description' => 'Canlı müzik etkinliği',
            'price' => 750,
            'capacity' => 30,
        ])->assertRedirect(route('organizer.dashboard'));

        $listing = \Illuminate\Support\Facades\DB::table('listings')->where('title', 'Test Konseri')->first();
        $this->assertNotNull($listing);
        $this->assertFalse((bool) $listing->is_published);
        $this->actingAs($organizer)->post(route('organizer.listings.update', $listing->id), [
            'price' => 800,
            'capacity' => 40,
        ])->assertRedirect(route('organizer.dashboard'));
        $this->assertDatabaseHas('listings', ['id' => $listing->id, 'price' => 800, 'capacity' => 40]);

        $this->get(route('categories.show', 'etkinlikler'))->assertDontSee('Test Konseri');

        $systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $this->actingAs($systemAdmin)->post(route('system.listings.publish', $listing->id))->assertRedirect(route('system.dashboard'));
        $this->get(route('categories.show', 'etkinlikler'))->assertSee('Test Konseri');

        $customer = User::factory()->create();
        $this->actingAs($customer)->post(route('reservations.store', 'etkinlikler'), [
            'item' => 'listing:'.$listing->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'guests' => 2,
            'payment_method' => 'bank_transfer',
        ])->assertRedirect(route('categories.show', ['category' => 'etkinlikler', 'secim' => 'listing:'.$listing->id]));

        $this->assertDatabaseHas('reservations', ['listing_id' => $listing->id, 'organizer_id' => $organizer->id, 'user_id' => $customer->id]);
        $this->assertDatabaseHas('payments', ['method' => 'bank_transfer', 'status' => 'pending']);
    }

    public function test_organizer_can_create_listing_with_custom_branch(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer', 'organizer_status' => 'active']);
        \Illuminate\Support\Facades\DB::table('organizer_fees')->insert([
            'organizer_id' => $organizer->id,
            'year' => now()->year,
            'annual_amount' => 12000,
            'status' => 'paid',
            'installment_count' => 1,
            'paid_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($organizer)->post(route('organizer.listings.store'), [
            'category' => 'custom',
            'custom_category' => 'Yat ve Tekne Turu',
            'title' => 'Göcek Özel Gulet Turu',
            'location' => 'Göcek, Muğla',
            'description' => 'Eşsiz koylarda mavi yolculuk',
            'price' => 3500,
            'capacity' => 15,
        ])->assertRedirect(route('organizer.dashboard'));

        $this->assertDatabaseHas('listings', [
            'organizer_id' => $organizer->id,
            'category' => 'yat-ve-tekne-turu',
            'title' => 'Göcek Özel Gulet Turu',
            'is_published' => false,
        ]);
    }

    public function test_system_admin_dashboard_shows_per_organizer_financial_breakdown(): void
    {
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $organizer = User::factory()->create([
            'name' => 'Kaya Turizm Ltd',
            'role' => 'organizer',
            'organizer_status' => 'active',
        ]);
        $customer = User::factory()->create();

        $feeId = \Illuminate\Support\Facades\DB::table('organizer_fees')->insertGetId([
            'organizer_id' => $organizer->id,
            'year' => now()->year,
            'annual_amount' => 15000,
            'status' => 'paid',
            'installment_count' => 3,
            'paid_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('organizer_fee_installments')->insert([
            'organizer_fee_id' => $feeId,
            'installment_no' => 1,
            'amount' => 5000,
            'due_date' => now()->toDateString(),
            'status' => 'paid',
            'paid_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resId = \Illuminate\Support\Facades\DB::table('reservations')->insertGetId([
            'user_id' => $customer->id,
            'organizer_id' => $organizer->id,
            'category' => 'oteller',
            'item' => 'Alaçatı Konak',
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => '05001234567',
            'start_date' => now()->addDays(2)->toDateString(),
            'guests' => 3,
            'total_amount' => 4500,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('payments')->insert([
            'reservation_id' => $resId,
            'user_id' => $customer->id,
            'amount' => 4500,
            'method' => 'credit_card',
            'status' => 'paid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('organizer_expenses')->insert([
            'organizer_id' => $organizer->id,
            'title' => 'Rehberlik Hizmeti',
            'category' => 'Personel',
            'amount' => 1500,
            'expense_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($systemAdmin)->get(route('system.dashboard'));
        $response->assertOk();
        $response->assertSee('Kaya Turizm Ltd');
        $response->assertSee('4.500,00'); // Sales
        $response->assertSee('1.500,00'); // Expenses
        $response->assertSee('3.000,00'); // Net profit
    }

    public function test_system_admin_can_toggle_organizer_active_status(): void
    {
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $organizer = User::factory()->create(['role' => 'organizer', 'organizer_status' => 'active']);

        $this->actingAs($systemAdmin)->post(route('system.organizers.toggleStatus', $organizer->id))
            ->assertRedirect();
        $this->assertSame('suspended', $organizer->fresh()->organizer_status);

        $this->actingAs($systemAdmin)->post(route('system.organizers.toggleStatus', $organizer->id))
            ->assertRedirect();
        $this->assertSame('active', $organizer->fresh()->organizer_status);
    }

    public function test_customer_can_pay_with_credit_card_and_view_in_reservations(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post(route('reservations.store', 'oteller'), [
            'item' => 'pera-house',
            'start_date' => now()->addDays(5)->toDateString(),
            'guests' => 2,
            'payment_method' => 'credit_card',
            'card_holder' => 'Musteri Kart',
            'card_number' => '4543 1234 5678 9012',
            'card_expiry' => '12/28',
            'card_cvv' => '123',
        ])->assertRedirect(route('categories.show', ['category' => 'oteller', 'secim' => 'pera-house']));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $customer->id,
            'category' => 'oteller',
            'item' => 'Pera House',
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $customer->id,
            'method' => 'credit_card',
            'status' => 'paid',
        ]);

        $this->actingAs($customer)->get(route('customer.reservations'))
            ->assertOk()
            ->assertSee('Pera House')
            ->assertSee('Kredi Kartı');
    }
}
