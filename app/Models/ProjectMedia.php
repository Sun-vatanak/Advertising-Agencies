<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMedia extends Model
{
    // Point to the specific table name if it's different
    protected $table = 'project_media';

    protected $fillable = ['project_id', 'file_path', 'type', 'order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    protected $casts = [
        'tech_stack' => 'array',
        'is_featured' => 'boolean'
    ];
}
