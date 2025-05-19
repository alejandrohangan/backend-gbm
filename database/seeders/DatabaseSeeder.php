<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\TicketHistory;
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
        User::factory(10)->create();
        $this->call(PrioritySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(TicketSeeder::class);
        Attachment::factory(5)->create();
        TicketHistory::factory(10)->create();
        Conversation::factory(1)->create();
        Message::factory(1)->create();
    }
}