<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_messages') && ! Schema::hasTable('user_messages')) {
            Schema::rename('contact_messages', 'user_messages');
        }

        if (Schema::hasTable('reservation_requests') && ! Schema::hasTable('reservations')) {
            Schema::rename('reservation_requests', 'reservations');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_messages') && ! Schema::hasTable('contact_messages')) {
            Schema::rename('user_messages', 'contact_messages');
        }

        if (Schema::hasTable('reservations') && ! Schema::hasTable('reservation_requests')) {
            Schema::rename('reservations', 'reservation_requests');
        }
    }
};