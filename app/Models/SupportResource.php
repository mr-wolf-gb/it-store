<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportResource extends Model
{
    public const TYPE_OPTIONS = ['app', 'script', 'document', 'other'];

    public const VISIBILITY_OPTIONS = ['public', 'private'];

    public const SOURCE_OPTIONS = ['upload', 'link'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'resource_type',
        'visibility',
        'source_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'link_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
