<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 24)->default('customer')->index();
            }
            if (! Schema::hasColumn('users', 'organizer_status')) {
                $table->string('organizer_status', 24)->default('not_applicable')->index();
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('reservations', 'user_id')) {
                $table->unsignedInteger('user_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('reservations', 'organizer_id')) {
                $table->unsignedInteger('organizer_id')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('reservations', 'listing_id')) {
                $table->unsignedBigInteger('listing_id')->nullable()->after('organizer_id');
            }
            if (! Schema::hasColumn('reservations', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('reservations', 'status')) {
                $table->string('status', 24)->default('confirmed')->index();
            }
            if (! Schema::hasColumn('reservations', 'payment_status')) {
                $table->string('payment_status', 24)->default('pending')->index();
            }
        });

        Schema::table('user_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('user_messages', 'user_id')) {
                $table->unsignedInteger('user_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('user_messages', 'status')) {
                $table->string('status', 24)->default('new')->index();
            }
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organizer_id');
            $table->foreign('organizer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('category', 40)->index();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('location', 180);
            $table->text('description');
            $table->string('image_url', 500)->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('reserved_count')->default(0);
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 24);
            $table->string('status', 24)->default('pending')->index();
            $table->string('coupon_code', 60)->nullable();
            $table->string('provider_reference', 160)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('reservation_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->unsignedInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('reason', 500);
            $table->string('status', 24)->default('requested')->index();
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('organizer_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organizer_id');
            $table->foreign('organizer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('category', 80);
            $table->decimal('amount', 12, 2);
            $table->date('expense_date')->index();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('discount_type', 16);
            $table->decimal('discount_value', 12, 2);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('organizer_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organizer_id');
            $table->foreign('organizer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('annual_amount', 12, 2);
            $table->string('status', 24)->default('unpaid')->index();
            $table->unsignedTinyInteger('installment_count')->default(1);
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['organizer_id', 'year']);
        });

        Schema::create('organizer_fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_fee_id')->constrained('organizer_fees')->cascadeOnDelete();
            $table->unsignedTinyInteger('installment_no');
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->string('status', 24)->default('unpaid')->index();
            $table->date('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['organizer_fee_id', 'installment_no'], 'fee_installment_no_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_fee_installments');
        Schema::dropIfExists('organizer_fees');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('organizer_expenses');
        Schema::dropIfExists('reservation_refunds');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('listings');

        Schema::table('user_messages', function (Blueprint $table) {
            if (Schema::hasColumn('user_messages', 'user_id')) $table->dropColumn('user_id');
            if (Schema::hasColumn('user_messages', 'status')) $table->dropColumn('status');
        });

        Schema::table('reservations', function (Blueprint $table) {
            foreach (['listing_id', 'organizer_id', 'user_id', 'total_amount', 'status', 'payment_status'] as $column) {
                if (Schema::hasColumn('reservations', $column)) $table->dropColumn($column);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'organizer_status']);
        });
    }
};