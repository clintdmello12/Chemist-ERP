<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'role_id', 'phone', 'city', 'state',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relations

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    /**
     * Check if user has an specific role
     * @param  string  $role
     * @return boolean
     */
    public function hasRole($role)
    {
        return ($this->role->nameLower() === $role);
    }
}
