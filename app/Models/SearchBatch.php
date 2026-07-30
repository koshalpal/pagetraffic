<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchBatch extends Model
{
    protected $fillable = [
        'queries',
        'result_count',
    ];

    protected function casts(): array
    {
        return [
            'queries' => 'array',
            'result_count' => 'integer',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(SearchResult::class);
    }
}
