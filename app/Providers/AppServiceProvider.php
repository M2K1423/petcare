<?php

namespace App\Providers;

use App\Models\Pet;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Medicine;
use App\Models\Payment;
use App\Policies\PetPolicy;
use App\Policies\Admin\UserPolicy;
use App\Policies\Admin\DoctorPolicy;
use App\Policies\Admin\ServicePolicy;
use App\Policies\Admin\MedicinePolicy;
use App\Policies\Admin\PaymentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Existing policies
        Gate::policy(Pet::class, PetPolicy::class);

        // Admin policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Doctor::class, DoctorPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Medicine::class, MedicinePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
