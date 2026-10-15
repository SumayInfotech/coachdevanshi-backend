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

    public function __construct($otp, $user,$type = 'signup')
    {
        $this->otp  = $otp;
        $this->user = $user;
        $this->type = $type;
    }

    public function build()
    {
        app()->setLocale(appLanguage());

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
