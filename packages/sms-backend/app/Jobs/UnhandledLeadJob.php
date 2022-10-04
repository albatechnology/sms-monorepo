<?php

namespace App\Jobs;

use App\Classes\ExpoMessage;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnhandledLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::join('leads', 'leads.user_id', '=', 'users.id')->where('users.type', 3)->selectRaw('users.id, users.name, count(users.id) as total_leads, (select code from notification_devices where user_id=users.id order by id desc limit 1) as code')->groupBy('users.id')->get();

        $link = config("notification-link.UnhandledLead");
        if (isset($users) && count($users) > 0) {
            foreach ($users as $user) {
                $message = ExpoMessage::create()
                    ->addRecipients($user->code)
                    ->setBadgeFor($user)
                    ->title('You Have ' . $user->total_leads . ' Unhandled Leads')
                    ->body('Hi ' . $user->name . ' you have ' . $user->total_leads . ' unhandled leads waiting to be assigned')
                    ->code('UnhandledLead')
                    ->link($link);

                app(PushNotificationService::class)->notify($message);
            }
        }
    }
}
