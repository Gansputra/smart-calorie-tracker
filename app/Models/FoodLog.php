<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'food_id',
        'food_name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'portion',
        'meal_type',
        'notes',
        'date',
        'ai_detected',
        'ai_confidence',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'calories' => 'decimal:2',
            'protein' => 'decimal:2',
            'carbs' => 'decimal:2',
            'fat' => 'decimal:2',
            'portion' => 'decimal:2',
            'date' => 'date',
            'ai_detected' => 'boolean',
            'ai_confidence' => 'decimal:4',
        ];
    }

    /**
     * Get the meal types available.
     */
    public static function mealTypes(): array
    {
        return ['Sarapan', 'Makan Siang', 'Makan Malam', 'Camilan'];
    }

    /**
     * Get the user that owns the food log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the food record (if from master).
     */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    /**
     * Scope to a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope to a specific meal type.
     */
    public function scopeForMealType($query, string $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    /**
     * Get the confidence percentage formatted.
     */
    public function getConfidencePercentAttribute(): ?string
    {
        if ($this->ai_confidence === null) {
            return null;
        }

        return round($this->ai_confidence * 100, 1) . '%';
    }
}
