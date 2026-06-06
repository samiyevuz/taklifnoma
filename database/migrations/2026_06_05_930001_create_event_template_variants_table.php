<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event_template_variants')) {
            $this->createVariantsTable();

            return;
        }

        $this->ensureVariantsIndex();
    }

    public function down(): void
    {
        Schema::dropIfExists('event_template_variants');
    }

    private function createVariantsTable(): void
    {
        Schema::create('event_template_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_template_id')->constrained('event_templates')->cascadeOnDelete();
            $table->string('variant_key', 80)->unique();
            $table->string('title', 120);
            $table->string('subtitle', 255)->nullable();
            $table->unsignedInteger('price_amount')->default(0);
            $table->string('theme', 20)->default('premium');
            $table->string('blade', 80)->nullable();
            $table->string('cover_path')->nullable();
            $table->string('badge', 40)->nullable();
            $table->unsignedSmallInteger('guest_limit')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['event_template_id', 'is_active', 'sort_order'], 'etv_template_active_sort_idx');
        });
    }

    private function ensureVariantsIndex(): void
    {
        $indexes = collect(Schema::getConnection()->select(
            'SHOW INDEX FROM event_template_variants WHERE Key_name = ?',
            ['etv_template_active_sort_idx']
        ));

        if ($indexes->isNotEmpty()) {
            return;
        }

        Schema::table('event_template_variants', function (Blueprint $table) {
            $table->index(['event_template_id', 'is_active', 'sort_order'], 'etv_template_active_sort_idx');
        });
    }
};
