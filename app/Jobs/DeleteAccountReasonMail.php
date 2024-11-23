<?php

namespace App\Jobs;

use App\Mail\DeleteAccountReasonMail as reasonMail;
use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DeleteAccountReasonMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $mailData;
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $config = Config::where('key', Config::REASON_EMAIL)->first();
        if(!empty($config) && $config->value!="") {
            $emails = explode(",", $config->value);
            Mail::to($emails)->send(new reasonMail($this->mailData));
        }
    }
}
