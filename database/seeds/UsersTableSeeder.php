<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['Admin', 'Chemist', 'admin@chemist.com', 1, 'admin']
        ];

        foreach ($users as $user) {
            App\User::create([
                'first_name' => $user[0],
                'last_name' => $user[1],
                'email' => snake_case($user[2]),
                'role_id' => $user[3],
                'password' => bcrypt($user[4]),
                'remember_token' => str_random(10),
                'phone' => '',
                'city' => '',
                'state' => '',
            ]);
        }
    }
}
