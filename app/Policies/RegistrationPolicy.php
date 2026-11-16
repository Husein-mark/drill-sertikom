<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Tentukan apakah user bisa melihat detail pendaftaran.
     */
    public function view(User $user, Registration $registration): bool
    {
        if ($user->isPanitia()) {
            return true;
        }

        return $user->id === $registration->user_id;
    }

    /**
     * Tentukan apakah user bisa melihat tiket digital (harus lunas & pemiliknya atau panitia).
     */
    public function viewTicket(User $user, Registration $registration): bool
    {
        if (! $registration->isPaid()) {
            return false;
        }

        if ($user->isPanitia()) {
            return true;
        }

        return $user->id === $registration->user_id;
    }

    /**
     * Tentukan apakah user bisa membatalkan / mengelola pendaftaran.
     */
    public function manage(User $user, Registration $registration): bool
    {
        return $user->isPanitia();
    }
}
