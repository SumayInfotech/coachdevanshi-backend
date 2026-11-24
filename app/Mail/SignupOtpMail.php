<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;
    public $type;
    public $minutes;

    // $minutes must match the otp_expires_at window set in AuthController.
    public function __construct($otp, $user, $type = 'signup', $minutes = 2)
    {
        $this->otp     = $otp;
        $this->user    = $user;
        $this->type    = $type;
        $this->minutes = $minutes;
    }

    public function build()
    {
        // Emails are always sent in English.
        app()->setLocale('en');

        // $type = $this->type === 'signup' ? 'Signup' : 'Login';
        $subjects = [
            'signup'          => 'Your OTP for Signup',
            'login'           => 'Your OTP for Login',
            'forgot_password' => 'Your OTP for Forgot Password',
        ];

        $subject = $subjects[$this->type] ?? 'Your OTP';
        return $this->subject($subject)->view('emails.signup-otp');
    }
}
