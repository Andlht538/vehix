<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;
use App\Http\Requests\VehiculeRequest;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VehiculeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();

        $query = Vehicule::with(['user', 'validator']);

        if ($user->isClient()) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        $vehicules = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Vehicules/Index', [
            'vehicules' => $vehicules,
            'filters' => $request->only(['search', 'status']),
            'canValidate' => $user->canValidateVehicules(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Vehicules/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehiculeRequest $request)
    {
        //
        $vehicule = Vehicule::create([
            'user_id' => $request->user()->id,
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'license_plate' => $request->license_plate,
            'vin' => $request->vin,
            'status' => VehiculeStatus::PENDING,
        ]);

        return redirect()->route('vehicules.show', $vehicule)
            ->with('message', 'Véhicule créé avec succès. En attente de validation.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Vehicule $vehicule)
    {
        //
        $vehicule->load([
            'user',
            'validator',
            'assurances'=> fn($q) => $q->orderBy('end_date', 'desc'),
            'maintenances' => fn($q) => $q->orderBy('maintenance_date', 'desc'),
            'reparations'=> fn($q) => $q->orderBy('reparation_date', 'desc'),
            'ravitaillements'=> fn($q) => $q->orderBy('ravitaillement_date', 'desc'),
            'trajets'=> fn($q) => $q->orderBy('trajet_date', 'desc'),
        ]);

        return Inertia::render('Vehicules/Show', [
            'vehicule' => $vehicule,
            'canValidate' => $request->user()->canValidateVehicules(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicule $vehicule)
    {
        //
        $this->authorize('update', $vehicule);

        return Inertia::render('Vehicles/Edit', [
            'vehicle' => $vehicule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehiculeRequest $request, Vehicule $vehicule)
    {
        //
         $this->authorize('update', $vehicule);

        $vehicule->update($request->validated());

        return redirect()->route('vehicules.show', $vehicule)
            ->with('message', 'Véhicule mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicule $vehicule)
    {
        //
         $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('message', 'Véhicule supprimé avec succès.');
    }
}
