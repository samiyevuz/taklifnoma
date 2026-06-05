<?php

use App\Models\RsvpResponse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->boolean('is_attending')->default(true)->after('guest_name');
            $table->index(['invitation_id', 'is_attending', 'created_at'], 'rsvp_invitation_attending_created_idx');
        });

        RsvpResponse::query()->orderBy('id')->each(function (RsvpResponse $response) {
            $response->forceFill([
                'is_attending' => $response->status !== RsvpResponse::STATUS_DECLINED,
            ])->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('rsvp_responses', function (Blueprint $table) {
            $table->dropIndex('rsvp_invitation_attending_created_idx');
            $table->dropColumn('is_attending');
        });
    }
};
