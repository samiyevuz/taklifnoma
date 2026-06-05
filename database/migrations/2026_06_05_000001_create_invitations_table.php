<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('template')->default('nikoh-premium');
            $table->string('status')->default('draft');
            $table->string('groom_name');
            $table->string('bride_name');
            $table->string('event_type')->default('Nikoh To\'yi');
            $table->dateTime('event_at');
            $table->string('event_city')->nullable();
            $table->string('venue_name');
            $table->string('venue_address');
            $table->decimal('map_lat', 10, 7)->nullable();
            $table->decimal('map_lng', 10, 7)->nullable();
            $table->text('invitation_text_1');
            $table->text('invitation_text_2')->nullable();
            $table->string('family_signature')->nullable();
            $table->json('dress_colors');
            $table->string('music_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
