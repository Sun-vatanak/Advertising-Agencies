<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'client_name',
        'description', 'thumbnail_url', 'tech_stack', 'is_featured'
    ];

    protected $casts = [
        'tech_stack' => 'array', // Automatically turns JSON into a PHP array
        'is_featured' => 'boolean',
    ];

    /**
     * Get the category that owns the project.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all media files (gallery) for this project.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class);
    }
}
