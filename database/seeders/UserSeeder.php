<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
        $users = [
            [
                'name' => 'Ilham Pratama',
                'email' => 'ilhampratama@gmail.com',
                'phone_number' => '0831763972663',
                'role' => 'superadmin',
                'status' => 1,
                'avatar' => 'superadmin.jpg',
                'password' => Hash::make('superadmin123')
            ],
            [
                'name' => 'Yusuf Maulana',
                'email' => 'yusufmaulana@gmail.com',
                'phone_number' => '0123456789123',
                'role' => 'admin',
                'status' => 1,
                'avatar' => 'admin.jpg',
                'password' => Hash::make('admin123')
            ]
        ];

        foreach ($users as $item) {
            User::create($item);
        }
    }
}
