<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeature extends Model
{
    protected $fillable = ['service_id', 'name'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
