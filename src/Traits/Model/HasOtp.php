<?php

namespace Core\Traits\Model;

use Carbon\Carbon;
use Core\Models\Otp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Messaging\Facades\Messaging;

trait HasOtp
{
    /**
     * otp morph relation
     *
     * @return MorphOne
     * @author WeSSaM
     */
    public function otp()
    {
        return $this->morphOne(Otp::class, "model")->active();
    }

    /**
     *
     *
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function sendOtp(): mixed
    {
        $otp = $this->otp ?? $this->generateOtp();
        Messaging::sms($this->mobile, __("Core::messages.otp_sms_message", ["otp" => $otp->otp]));
        return $otp;
    }


    /**
     * inserting new otp morph record
     *
     * @return Model|mixed
     * @author WeSSaM
     */
    public function generateOtp()
    {
        return $this->otp()->create(['otp' => rand(10000, 99999), 'username' => $this->mobile, 'expired_at' => Carbon::now()]);
    }


    /**
     * return otp code
     *
     * @return mixed
     * @author WeSSaM
     */
    public function getOtpCode()
    {
        return $this?->otp?->otp;
    }
}
