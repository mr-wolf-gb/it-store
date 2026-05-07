<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ResourceTag extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(SupportResource::class, 'support_resource_tag');
    }
}
