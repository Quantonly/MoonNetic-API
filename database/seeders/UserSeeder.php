<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Cody Volz',
            'email' => 'cody.volz@hotmail.com',
            'password' => Hash::make('test1234'),
        ]);
        $user = User::create([
            'name' => 'Test Account',
            'email' => 'test.test@test.com',
            'password' => Hash::make('test1234'),
        ]);
    }
}
