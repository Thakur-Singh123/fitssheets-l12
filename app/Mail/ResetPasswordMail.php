<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $email;

    //Function for construct
    public function __construct($otp, $email = null) {
        $this->otp = $otp;
        $this->email = $email;
    }
    //Function for build email
    public function build() {
        return $this->subject('Your OTP to reset password')
            ->view('email.custom_reset')
            ->with([
                'otp' => $this->otp,
                'email' => $this->email,
            ]);
    }
}
