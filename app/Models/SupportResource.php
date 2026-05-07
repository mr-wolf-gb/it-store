<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportResource extends Model
{
    use HasFactory;

    public const TYPE_OPTIONS = ['app', 'script', 'document', 'other'];

    public const VISIBILITY_OPTIONS = ['public', 'private'];

    public const SOURCE_OPTIONS = ['upload', 'link'];

    public const STATUS_OPTIONS = ['draft', 'published', 'archived'];

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
        'slug',
        'version',
        'changelog',
        'status',
        'thumbnail_path',
        'category_id',
        'is_featured',
        'published_at',
        'created_by',
        'updated_by',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'link_url',
        'download_count',
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
            'download_count' => 'integer',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SupportResourceFile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ResourceTag::class, 'support_resource_tag');
    }
}
