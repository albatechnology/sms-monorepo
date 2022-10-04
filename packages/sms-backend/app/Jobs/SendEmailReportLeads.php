<?php

namespace App\Jobs;

use App\Exports\ReportLeadsExport;
use App\Mail\ReportLeads;
use App\Models\User;
use App\Services\ApiReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendEmailReportLeads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public array $emails = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $emails = [])
    {
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 1. fetch users nya
        // 2. tarik data berdasarkan roles nya
        // 3. export data ke excel
        // 4. upload file excel nya
        // 5. kirim email beserta link file nya

        // $users = DB::table('users')->distinct()->join('user_companies', 'user_companies.user_id', '=', 'users.id')->whereNull('users.deleted_at')->whereNotNull('users.company_ids')->whereNotNull('users.email')->where('users.type', 4)->select('users.id', 'users.name', 'users.email')->get();

        if (isset($this->emails) && count($this->emails) > 0) {
            $users = User::distinct()->whereIn('email', $this->emails)->select('users.id', 'users.name', 'users.email', 'users.company_ids')->get();
        } else {
            $users = User::distinct()->where('users.type', 3)->where('supervisor_type_id', 2)->select('users.id', 'users.name', 'users.email', 'users.company_ids')->get();
        }
        // dd($users);
        foreach ($users as $user) {
            $files = [];
            if (count($user->company_ids) > 0) {
                foreach ($user->company_ids as $company_id) {
                    $company = DB::table('companies')->select('name')->where('id', $company_id)->first();
                    if ($company) {
                        $datas = app(ApiReportService::class)->reportLeadsNew(user_id: $user->id, is_export: true, company_id: $company_id);
                        $filePath = '/exports/reports-' . date('M-Y') . '/report-leads-' . $company->name . '-' . date('M') . '-' . date('dmY') . '-' . $user->id . '.xlsx';
                        $uploaded = Excel::store(new ReportLeadsExport($datas), $filePath, 's3', \Maatwebsite\Excel\Excel::XLSX);
                        if ($uploaded) $files[] = $filePath;
                    }
                }

                if (count($files) > 0) {
                    $date = date('Y-m-d');
                    Mail::to($user->email)->send(new ReportLeads($files, $user->name, date('01-m-Y', strtotime($date)), date('d-m-Y', strtotime($date))));
                }
            }
        }
    }
}
