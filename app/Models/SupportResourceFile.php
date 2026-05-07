<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportResourceFile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'support_resource_id',
        'file_path',
        'file_name',
        'stored_name',
        'mime_type',
        'file_size',
        'download_count',
        'uploaded_by',
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
        ];
    }

    public function supportResource(): BelongsTo
    {
        return $this->belongsTo(SupportResource::class);
    }
}
