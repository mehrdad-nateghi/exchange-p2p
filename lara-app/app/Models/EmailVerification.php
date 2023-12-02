<?php

namespace App\Models;

use App\Mail\EmailVerificationMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailVerification extends Model
{
    use HasFactory;

    protected $table = 'email_verifications';
    protected $fillable = ['email', 'code', 'expired_at'];

    /**
     * Send verification code to the destination email
     */
    public function sendCode()
    {
        try {
            // Send email with verification code
            Mail::to($this->email)->send(new EmailVerificationMail($this->code));

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending email verification code: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Check whether the verification instance is valid or not
     */
    public function isValid($code) {

        if($this->code !== $code || Carbon::now()->greaterThan($this->expired_at)) {
            return false;
        }

        return true;
    }
}
