<?php

namespace Database\Seeders;

use App\Models\QaTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class QaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users  = User::all();
        $sender = $users->shift();

        // Have one user start a conversation with everyone else
        $users->each(function (User $user) use ($sender) {
            QaTopic::factory()
                   ->withUsers($sender->id, $user->id)
                   ->withMessages()
                   ->create(
                       [
                           'creator_id' => $sender->id
                       ]
                   );
        });
    }
}
