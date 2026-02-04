<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['title', 'description', 'image', 'button_link'];

    public function features()
    {
        return $this->hasMany(ServiceFeature::class);
    }
}
