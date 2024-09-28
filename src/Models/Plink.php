<?php

namespace BenBjurstrom\Plink\Models;

use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Support\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property PlinkStatus $status
 * @property bool $remember
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * */
class Plink extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PlinkStatus::class,
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
