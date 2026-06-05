<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Support\InvitationDefaults;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        Invitation::query()->where('slug', 'ali-vali')->delete();

        Invitation::query()->updateOrCreate(
            ['slug' => 'farhod-shirin'],
            InvitationDefaults::demoAttributes()
        );
    }
}
