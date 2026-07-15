<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'calorie_target',
        'protein_target',
        'gender',
        'age',
        'height',
        'weight',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'calorie_target' => 'integer',
            'protein_target' => 'integer',
        ];
    }

    /**
     * Get the user's food logs.
     */
    public function foodLogs(): HasMany
    {
        return $this->hasMany(FoodLog::class);
    }

    /**
     * Get the user's weight logs.
     */
    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class);
    }

    /**
     * Get today's food logs.
     */
    public function todayFoodLogs(): HasMany
    {
        return $this->hasMany(FoodLog::class)
            ->whereDate('date', today());
    }

    /**
     * Get the avatar URL attribute.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate avatar from name initials using UI Avatars service
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=16a34a&color=fff&size=128';
    }
}
