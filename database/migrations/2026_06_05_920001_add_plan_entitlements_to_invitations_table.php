<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->string('plan_tier', 20)->nullable()->after('template_variant');
            $table->unsignedSmallInteger('guest_limit')->nullable()->after('plan_tier');
            $table->string('custom_domain', 120)->nullable()->after('guest_limit');
            $table->index('custom_domain');
        });

        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->string('template_variant', 80)->nullable()->after('template_slug');
            $table->string('plan_tier', 20)->nullable()->after('template_variant');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['custom_domain']);
            $table->dropColumn(['plan_tier', 'guest_limit', 'custom_domain']);
        });

        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->dropColumn(['template_variant', 'plan_tier']);
        });
    }
};
