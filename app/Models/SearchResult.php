<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchResult extends Model
{
    protected $fillable = [
        'search_batch_id',
        'query',
        'position',
        'title',
        'link',
        'snippet',
        'displayed_link',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(SearchBatch::class, 'search_batch_id');
    }
}
