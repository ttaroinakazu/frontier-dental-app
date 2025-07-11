<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        \Log::info('Deleting user: '.$user->id);
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();

        $user->delete();

        if ($user->exists) {
            \Log::warning('User still exists after delete');
        } else {
            \Log::info('User deleted successfully');
        }
    }
}
