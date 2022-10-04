<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompileContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate notification config based on contract';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $notification_data = json_decode(Storage::disk('contracts')->get("notifications.json"));

        $replace = collect($notification_data)
            ->map(function ($data) {
                return sprintf("'%s' => \"%s\",", $data->code, $data->link);
            })
            ->implode("\n    ");

        $stub = Storage::disk('stubs')->get('notification-link.txt');

        $new_file = str_replace('$ARGS$', $replace, $stub);

        Storage::disk('root')->put('config/notification-link.php', $new_file);
        $this->info('Notification contract generated to config/notification-link.php');
    }
}
