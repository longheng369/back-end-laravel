<?php

// app/Policies/FilamentUserPolicy.php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilamentUserPolicy
{
    use HandlesAuthorization;

    public function accessFilament(User $user)
    {
        return $user->hasRole('admin');
    }
}

