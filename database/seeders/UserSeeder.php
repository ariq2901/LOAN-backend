<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student = User::create([
            "name" => "tim sukses student",
            "email" => "timsukses@gmail.com",
            "email_verified_at" => now()->timezone('Asia/Jakarta'),
            "password" => "timsukses",
            "parent_email" => "fakeghuroba@gmail.com"
        ]);
        $student->assignRole('student');
    }
}
