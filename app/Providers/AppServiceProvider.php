<?php

namespace app\Providers;

use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\Uuid;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        \app\User::creating(function ($user) {
            $user->uuid = Uuid::uuid1();
            $user->middle_name = Uuid::uuid1();
        });

        \app\District::creating(function ($district) {
            $district->uuid = Uuid::uuid1();
        });

        \app\Organization::creating(function ($organization) {
            $organization->uuid = Uuid::uuid1();
        });

        \app\Group::creating(function ($group) {
            $group->uuid = Uuid::uuid1();
        });

        \app\Game::creating(function ($game) {
            $game->uuid = Uuid::uuid1();
        });

        \app\Flip::creating(function ($flip) {
            $flip->uuid = Uuid::uuid1();
        });

        // Attach event handler, on deleting of the user
        \app\User::deleting(function ($user) {
            echo('deleting from AppServiceProvider ');
            $user->districts()->detach();
            $user->organizations()->detach();
            $user->groups()->detach();
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }
}
