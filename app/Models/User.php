<?php

namespace App\Models;

use App\Traits\User\AccountStatus;
use App\Traits\User\HasApiTokens;
use App\Traits\User\HasRole;
use App\Traits\User\UserPermission;
use App\Traits\User\UserRole;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens,
        Notifiable,
        HasRole,
        UserRole,
        UserPermission,
        AccountStatus,
        SoftDeletes;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
        'activated_token',
        'is',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        //
    ];

    /**
     * User address
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get all user media
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * User avatar
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function avatar()
    {
        return $this->media()->where('type', 'avatar')->first();
    }
}
