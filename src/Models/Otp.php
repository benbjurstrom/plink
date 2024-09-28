<?php

namespace BenBjurstrom\Plink\Models;

use BenBjurstrom\Plink\Enums\OtpStatus;
use BenBjurstrom\Plink\Support\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property OtpStatus $status
 * @property string $code
 * @property bool $remember
 * @property int $attempts
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * */
class Otp extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => OtpStatus::class,
        'code' => 'hashed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'status',
        'ip_address',
    ];

    public function user()
    {
        $authenticatableModel = Config::getAuthenticatableModel();

        return $this->belongsTo($authenticatableModel);
    }
}
