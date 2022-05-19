<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Server;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $server = Website::create([
            'user_id' => 1,
            'sub_domain' => 'codyvolz',
            'server_ip' => '172.26.5.10',
            'sftp_username' => Str::random(10),
            'sftp_password' => Str::random(10),
            'sftp_host' => 'moonnetic.com',
            'sftp_port' => '22',
            'php_host' => 'moonnetic.com',
            'php_database' => 'db_' . Str::random(10),
            'php_username' => Str::random(10),
            'php_password' => Str::random(10),
            'php_version' => '7.4'
        ]);
        $server = Website::create([
            'user_id' => 2,
            'sub_domain' => 'testaccount',
            'server_ip' => '172.26.5.10',
            'sftp_username' => Str::random(10),
            'sftp_password' => Str::random(10),
            'sftp_host' => 'moonnetic.com',
            'sftp_port' => '22',
            'php_host' => 'moonnetic.com',
            'php_database' => 'db_' . Str::random(10),
            'php_username' => Str::random(10),
            'php_password' => Str::random(10),
            'php_version' => '8.0'
        ]);
    }
}
