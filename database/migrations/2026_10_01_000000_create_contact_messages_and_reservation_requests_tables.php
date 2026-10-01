<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('phone', 30)->nullable();
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('reservation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('item', 160);
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('phone', 30);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('guests');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_requests');
        Schema::dropIfExists('contact_messages');
    }
};