<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat data utama & simpan ke variabel agar bisa di-recycle
        $users = User::factory()->count(30)->create();
        $communities = Community::factory()->count(20)->recycle($users)->create();
        $events = Event::factory()->count(25)->recycle([$users, $communities])->create();

        // 2. Generate array mentah (raw) & insertOrIgnore agar duplikat otomatis di-skip
        $now = now();

        $members = array_map(fn ($item) => [
            ...$item,
            'created_at' => $now,
            'updated_at' => $now,
        ], CommunityMember::factory()->count(30)->raw());

        CommunityMember::insertOrIgnore($members);

        $participants = array_map(fn ($item) => [
            ...$item,
            'created_at' => $now,
            'updated_at' => $now,
        ], Participant::factory()->count(60)->raw());

        Participant::insertOrIgnore($participants);
    }
}
