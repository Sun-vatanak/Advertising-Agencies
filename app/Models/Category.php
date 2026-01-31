<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Get all projects for this category.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
