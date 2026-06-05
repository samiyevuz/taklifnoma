<?php

use App\Support\InvitationEventData;
use App\Support\TemplateCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('template_slug', 80)->nullable()->after('template');
            $table->string('custom_slug', 120)->nullable()->unique()->after('slug');
            $table->json('event_data')->nullable()->after('profile_meta');
            $table->timestamp('expires_at')->nullable()->after('published_at');

            $table->index(['template_slug', 'status']);
            $table->index(['user_id', 'template_slug']);
        });

        DB::table('invitations')->orderBy('id')->lazy()->each(function (object $row) {
            $template = TemplateCatalog::findByBlade($row->template);
            $templateSlug = $template['slug'] ?? 'nikoh';
            $status = $row->status === 'published' ? 'active' : $row->status;

            $eventData = InvitationEventData::pack([
                'profile_meta' => json_decode($row->profile_meta ?? 'null', true) ?: [],
                'event_at' => $row->event_at,
                'event_city' => $row->event_city,
                'venue_name' => $row->venue_name,
                'venue_address' => $row->venue_address,
                'map_lat' => $row->map_lat,
                'map_lng' => $row->map_lng,
                'invitation_text_1' => $row->invitation_text_1,
                'invitation_text_2' => $row->invitation_text_2,
                'family_signature' => $row->family_signature,
                'music_url' => $row->music_url,
                'template' => $row->template,
                'dress_colors' => json_decode($row->dress_colors ?? '[]', true) ?: [],
                'rsvp_enabled' => (bool) ($row->rsvp_enabled ?? true),
            ]);

            DB::table('invitations')->where('id', $row->id)->update([
                'uuid' => $row->uuid ?: (string) Str::uuid(),
                'template_slug' => $row->template_slug ?: $templateSlug,
                'custom_slug' => $row->custom_slug ?: $row->slug,
                'status' => $status,
                'event_data' => json_encode($eventData),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['template_slug', 'status']);
            $table->dropIndex(['user_id', 'template_slug']);
            $table->dropColumn(['uuid', 'template_slug', 'custom_slug', 'event_data', 'expires_at']);
        });
    }
};
