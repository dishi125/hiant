<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\SignupMail as registerMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Config;
use Illuminate\Support\Facades\Mail;

class SignupMail implements ShouldQueue
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
        $config = Config::where('key', Config::SIGNUP_EMAIL)->first();
        Mail::to($config->value)->send(new registerMail($this->mailData));
    }
}
