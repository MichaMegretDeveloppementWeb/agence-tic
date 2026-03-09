<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Interface → Implementation bindings.
     * Ajouter ici chaque contrat de repository au fur et a mesure.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // Exemple :
        // \App\Contracts\Repositories\User\UserReadRepositoryInterface::class
        //     => \App\Repositories\User\UserReadRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
