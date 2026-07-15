<?php

namespace App\Policies;

use App\Models\FoodLog;
use App\Models\User;

class FoodLogPolicy
{
    public function update(User $user, FoodLog $foodLog): bool
    {
        return $user->id === $foodLog->user_id;
    }

    public function delete(User $user, FoodLog $foodLog): bool
    {
        return $user->id === $foodLog->user_id;
    }
}
