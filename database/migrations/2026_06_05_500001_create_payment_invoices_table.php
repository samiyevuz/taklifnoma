<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 20);
            $table->string('merchant_trans_id', 64)->unique();
            $table->string('provider_trans_id', 64)->nullable();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_tiyin');
            $table->string('currency', 3)->default('UZS');
            $table->string('template_slug', 80);
            $table->string('status', 20)->default('pending');
            $table->json('provider_state')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'status']);
            $table->index(['provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_invoices');
    }
};
