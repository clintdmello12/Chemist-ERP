<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * All default roles
         * @var array
         */
        $roles = [
            'Admin',
            'Manager',
            'Accountant',
            'Customer'
        ];

        foreach ($roles as $role) {
            App\Role::create([
                'name' => $role,
            ]);
        }
    }
}
