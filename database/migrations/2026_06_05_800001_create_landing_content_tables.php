<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('blade', 80);
            $table->string('visual', 80);
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('price_amount')->default(0);
            $table->string('preview_route', 60)->default('template.preview');
            $table->string('preview_param', 40);
            $table->json('translations');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('landing_faqs', function (Blueprint $table) {
            $table->id();
            $table->json('translations');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('landing_faqs');
        Schema::dropIfExists('event_templates');
    }
};
