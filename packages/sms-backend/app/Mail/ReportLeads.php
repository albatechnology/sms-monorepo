<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * send email report leads to all BUM and Director
 */
class ReportLeads extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public array $files = [];
    public string $name;
    public string $startDate;
    public string $endDate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($files = [], $name = '', $startDate = '', $endDate = '')
    {
        $this->files = $files;
        $this->name = isset($name) && $name != '' && $name != null ? $name : 'user';
        $this->startDate = isset($startDate) && $startDate != '' && $startDate != null ? $startDate : date('01-m-Y', strtotime(date('Y-m-d')));
        $this->endDate = isset($endDate) && $endDate != '' && $endDate != null ? $endDate : date('d-m-Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (isset($this->files) && count($this->files) > 0) {
            $email = $this->view('emails.reportLeads', ['name' => $this->name, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);

            foreach ($this->files as $file) {
                $email->attachFromStorageDisk('s3', $file);
            }
            return $email;
        } else {
            return $this->view('emails.reportLeads', ['name' => $this->name, 'startDate' => $this->startDate, 'endDate' => $this->endDate]);
        }
    }
}
