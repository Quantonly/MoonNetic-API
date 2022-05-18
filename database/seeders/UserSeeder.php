<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Server;
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
        $guest = Role::create([
            'name' => 'Guest',
            'description' => 'Guest'
        ]);
        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator'
        ]);
        $user = User::create([
            'name' => 'Cody Volz',
            'email' => 'cody.volz@hotmail.com',
            'password' => Hash::make('test1234'),
        ]);
        $user->roles()->attach($admin);
        $user = User::create([
            'name' => 'Test Account',
            'email' => 'test.test@test.com',
            'password' => Hash::make('test1234'),
        ]);
        $user->roles()->attach($admin);
        $user->roles()->attach($guest);
        
        $server = Server::create([
            'user_id' => 1,
            'server_ip' => '172.26.5.10',
            'sftp_username' => 'slag',
            'sftp_password' => Hash::make('slagen'),
            'sftp_host' => '172.26.5.10',
            'sftp_port' => 22,
            'php_host' => '172.26.5.10',
            'php_username' => 'phpslag',
            'php_password' => Hash::make('phpslagen'),
        ]);

        $server = Server::create([
            'user_id' => 2,
            'server_ip' => '172.26.5.10',
            'sftp_username' => 'pallettekop',
            'sftp_password' => Hash::make('voelpallettekop'),
            'sftp_host' => '172.26.5.10',
            'sftp_port' => 22,
            'php_host' => '172.26.5.10',
            'php_username' => 'phppallettekop',
            'php_password' => Hash::make('phpvoelpallettekop'),
        ]);
    }
}
