<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'weight',
        'notes',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'date' => 'date',
        ];
    }

    /**
     * Get the user that owns the weight log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to order by date descending.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('date', 'desc');
    }

    /**
     * Scope to date range.
     */
    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
