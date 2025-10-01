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

// Accueil
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'prevent.blocked',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion des véhicules (sélection, détail en attente)
    Route::get('/vehicules/selection', [VehiculeController::class, 'selection'])
        ->name('vehicules.selection');
    
    Route::post('/vehicules/{vehicule}/select', [VehiculeController::class, 'select'])
        ->name('vehicules.select');
    
    Route::get('/vehicules/{vehicule}/pending', [VehiculeController::class, 'pendingDetail'])
        ->name('vehicules.pending-detail');

    // Vehicle CRUD routes
    Route::resource('vehicules', VehiculeController::class);

    // Routes avec validation de véhicule (réservées aux utilisateurs avec véhicule validé)
    Route::middleware(['vehicule.validation'])->group(function () {
        // Autres ressources
        Route::resource('assurances', AssuranceController::class);
        Route::resource('reparations', ReparationController::class);
        Route::resource('maintenances', MaintenanceController::class); // Vidanges
        Route::resource('ravitaillements', RavitaillementController::class);
        Route::resource('trajets', TrajetController::class);
        Route::resource('recus', RecuController::class);
        Route::resource('proprietaires', ProprietaireController::class);
    });

    // Routes de validation (réservées aux validateurs/admins)
    Route::middleware(['role:validator,administrateur'])
        ->prefix('validator')
        ->name('validator.')
        ->group(function () {
            Route::get('/pending', [VehiculeController::class, 'pending'])->name('pending');
            Route::post('/vehicules/{vehicule}/validate', [VehiculeController::class, 'validateVehicule'])
                ->name('vehicules.validate');
        });

    // Routes d'administration
    Route::middleware(['role:administrateur'])
        ->prefix('administrateur')
        ->name('administrateur.')
        ->group(function () {
            Route::get('/users', [AdministrateurController::class, 'users'])->name('users.index');
            Route::put('/users/{user}/role', [AdministrateurController::class, 'updateUserRole'])->name('users.role');
            Route::delete('/users/{user}', [AdministrateurController::class, 'deleteUser'])->name('users.delete');
            Route::post('/users/{user}/block', [AdministrateurController::class, 'blockUser'])->name('users.block');
            Route::post('/users/{user}/unblock', [AdministrateurController::class, 'unblockUser'])->name('users.unblock');
        });
});