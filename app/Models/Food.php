<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Food extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'foods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'category',
        'calories_per_100g',
        'protein_per_100g',
        'carbs_per_100g',
        'fat_per_100g',
        'image',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'calories_per_100g' => 'decimal:2',
            'protein_per_100g' => 'decimal:2',
            'carbs_per_100g' => 'decimal:2',
            'fat_per_100g' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all food logs for this food.
     */
    public function foodLogs(): HasMany
    {
        return $this->hasMany(FoodLog::class);
    }

    /**
     * Get the food image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/food-placeholder.png');
    }

    /**
     * Scope to only active foods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get categories list.
     */
    public static function categories(): array
    {
        return [
            'Nasi & Roti',
            'Lauk Pauk',
            'Sayuran',
            'Buah-Buahan',
            'Minuman',
            'Cemilan',
            'Makanan Cepat Saji',
            'Sup & Soto',
            'Mie & Pasta',
            'Dessert',
            'Umum',
        ];
    }
}
