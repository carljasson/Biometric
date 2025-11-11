<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Responder;

class ResponderSeeder extends Seeder
{
    public function run()
    {
        if (!Responder::where('email', 'responder1@example.com')->exists()) {
            Responder::create([
                'name' => 'Responder 1',
                'email' => 'responder1@example.com',
                'password' => Hash::make('securepassword'),
            ]);
        }
    }
}
