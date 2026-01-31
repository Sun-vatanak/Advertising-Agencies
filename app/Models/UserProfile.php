<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'gender_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
