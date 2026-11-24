<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        // Emails are always sent in English.
        app()->setLocale('en');

        return $this->subject('Reset Your Password')
                    ->view('emails.reset-password', [
                        'resetUrl' => route('customer.password.reset', ['token' => $this->token]),
                    ]);
    }
}
