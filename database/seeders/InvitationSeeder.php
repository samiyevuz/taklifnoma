<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Support\InvitationDefaults;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        Invitation::query()->updateOrCreate(
            ['slug' => 'ali-vali'],
            InvitationDefaults::demoAttributes()
        );
    }
}
