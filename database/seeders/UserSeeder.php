<?php

namespace Database\Seeders;

use App\Models\Avtar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravolt\Avatar\Facade as Avatar;

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
            "password" => Hash::make("timsukses"),
            "parent_email" => "fakeghuroba@gmail.com"
        ]);
        $student->assignRole('student');
        $picName = md5("tim sukses") . '.' . 'png';
        $avatar = Avatar::create("Tim Sukses")->getImageObject()->encode('png');
        Storage::put('public/images/avatars/' . $picName, (string) $avatar);
        $avtar = [];
        $avtar['user_id'] = 1;
        $avtar['image'] = $picName;
        Avtar::create($avtar);
    }
}
