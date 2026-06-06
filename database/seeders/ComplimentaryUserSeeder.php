<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\ComplimentaryAccess;
use Illuminate\Database\Seeder;

class ComplimentaryUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ComplimentaryAccess::emails() as $email) {
            User::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($email)])
                ->update(['is_complimentary' => true]);
        }
    }
}
