<?php

namespace Database\Seeders;

use App\Models\User;
use App\RoleEnum;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->create([
                'email' => 'admin@email.com',
                'password' => '123456789'
            ])->assignRole(RoleEnum::ADMIN->value);

        User::factory()
            ->create([
                'email' => 'customer@email.com',
                'password' => '123456789'
            ])->assignRole(RoleEnum::CUSTOMER->value);
    }
}
