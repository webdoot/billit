<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\Contracts\CustomerRepositoryInterface::class, \App\Repositories\Eloquent\CustomerRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ServiceCategoryRepositoryInterface::class, \App\Repositories\Eloquent\ServiceCategoryRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ServiceProductRepositoryInterface::class, \App\Repositories\Eloquent\ServiceProductRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ServerRepositoryInterface::class, \App\Repositories\Eloquent\ServerRepository::class);
        $this->app->bind(\App\Repositories\Contracts\CustomerServiceRepositoryInterface::class, \App\Repositories\Eloquent\CustomerServiceRepository::class);
        $this->app->bind(\App\Repositories\Contracts\DomainRepositoryInterface::class, \App\Repositories\Eloquent\DomainRepository::class);
        $this->app->bind(\App\Repositories\Contracts\HostingRepositoryInterface::class, \App\Repositories\Eloquent\HostingRepository::class);
        $this->app->bind(\App\Repositories\Contracts\RenewalRepositoryInterface::class, \App\Repositories\Eloquent\RenewalRepository::class);
        $this->app->bind(\App\Repositories\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\Eloquent\InvoiceRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PaymentRepositoryInterface::class, \App\Repositories\Eloquent\PaymentRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ReceiptRepositoryInterface::class, \App\Repositories\Eloquent\ReceiptRepository::class);
        $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\Eloquent\UserRepository::class);
        $this->app->bind(\App\Repositories\Contracts\RoleRepositoryInterface::class, \App\Repositories\Eloquent\RoleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(fn ($user, $ability) => $user->hasRole('Super Admin') ? true : null);
    }
}
