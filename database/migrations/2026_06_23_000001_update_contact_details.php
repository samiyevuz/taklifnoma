<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteSetting::setMany([
            'contact.email' => 'info@theuzsoft.uz',
            'contact.phone' => '+998 88 222 22 87',
            'contact.whatsapp' => 'https://wa.me/998882222287',
        ]);
    }

    public function down(): void
    {
        // Contact details are managed via admin; no rollback needed.
    }
};
