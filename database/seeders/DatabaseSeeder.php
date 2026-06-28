<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'peserta@test.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'role' => 'peserta',
        ]);

        $events = [
            ['title' => 'Seminar Nasional Teknologi 2026', 'categories' => [
                ['name' => 'Regular', 'quota' => 2, 'price' => 50000],
                ['name' => 'VIP', 'quota' => 1, 'price' => 150000],
            ]],
            ['title' => 'Workshop AI untuk Pemula', 'categories' => [
                ['name' => 'Umum', 'quota' => 5, 'price' => 0],
            ]],
            ['title' => 'Kompetisi Programming 2026', 'categories' => [
                ['name' => 'Mahasiswa', 'quota' => 3, 'price' => 25000],
                ['name' => 'Umum', 'quota' => 3, 'price' => 75000],
            ]],
        ];

        foreach ($events as $data) {
            $event = \App\Models\Event::create([
                'user_id' => $admin->id,
                'title' => $data['title'],
                'description' => 'Deskripsi lengkap untuk ' . $data['title'] . '. Acara ini terbuka untuk semua kalangan dan akan menghadirkan pembicara terkemuka di bidangnya.',
                'poster_img' => 'posters/default.jpg',
                'event_date' => now()->addDays(rand(7, 30)),
                'registration_deadline' => now()->addDays(rand(3, 6)),
                'status' => 'open',
            ]);

            foreach ($data['categories'] as $cat) {
                $event->categories()->create([
                    'name' => $cat['name'],
                    'quota' => $cat['quota'],
                    'price' => $cat['price'],
                    'description' => null,
                ]);
            }
        }
    }
}
