<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\ReparationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RavitaillementController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\AdministrateurController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'prevent.blocked',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes avec vérification de validation de véhicule pour les clients
    Route::middleware(['vehicule.validation'])->group(function () {
        
        // Véhicules
        Route::resource('vehicules', VehiculeController::class);
        Route::post('vehicules/{vehicule}/validate', [VehiculeController::class, 'validate'])
            ->name('vehicules.validate')
            ->middleware('role:validator,admin');

        // Assurances
        Route::resource('assurances', AssuranceController::class);
        
        // Réparations
        Route::resource('reparations', ReparationController::class);
        
        // Maintenances (Vidanges)
        Route::resource('maintenances', MaintenanceController::class);
        
        // Ravitaillements
        Route::resource('ravitaillements', RavitaillementController::class);
        
        // Trajets
        Route::resource('trajets', TrajetController::class);
        
        // Reçus
        Route::resource('recus', RecuController::class);
        
        // Propriétaires
        Route::resource('proprietaires', ProprietaireController::class);
    });

    // Routes Administration
    Route::middleware(['role:administrateur'])->prefix('administrateur')->name('admin.')->group(function () {
        Route::get('/users', [AdministrateurController::class, 'users'])->name('users.index');
        Route::put('/users/{user}/role', [AdministrateurController::class, 'updateUserRole'])->name('users.role');
        Route::delete('/users/{user}', [AdministrateurController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{user}/block', [AdministrateurController::class, 'blockUser'])->name('users.block');
        Route::post('/users/{user}/unblock', [AdministrateurController::class, 'unblockUser'])->name('users.unblock');
    });

    // Routes Validateur
    Route::middleware(['role:validator,admin'])->prefix('validator')->name('validator.')->group(function () {
        Route::get('/pending', [VehiculeController::class, 'pending'])->name('pending');
    });
});
