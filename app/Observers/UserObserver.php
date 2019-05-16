<?php

namespace Weekendr\Observers;

use Faker\Generator as Faker;
use Weekendr\Models\User;

class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param  \Weekendr\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->slug = md5($user->email);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \Weekendr\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \Weekendr\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \Weekendr\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \Weekendr\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
