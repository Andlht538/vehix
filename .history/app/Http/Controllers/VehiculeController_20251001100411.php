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
     * Display vehicle selection page (cards view)
     */
     public function selection(Request $request)
    {
        $user = $request->user();

        $vehicules = $user->vehicules()
            ->with(['validator'])
            ->orderByRaw("FIELD(status, 'valide', 'en_attente', 'a_corriger', 'refuse', 'doublon')")
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Vehicules/Selection', [
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * Select a vehicle (store in session and redirect to dashboard)
     */
    public function select(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        // Only allow selection of validated vehicles
        if ($vehicule->status !== VehiculeStatus::VALIDATED) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Seuls les véhicules validÃ©s peuvent être sélectionnées.');
        }

        // Store selected vehicle ID in session
        session(['selected_vehicule_id' => $vehicule->id]);

        return redirect()->route('dashboard')
            ->with('message', "Véhicule {$vehicule->full_name} sélectionnée.");
    }

    /**
     * Display pending validation detail page
     */
    public function pendingDetail(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

         // Autoriser seulement les statuts non-validÃ©s
    if ($vehicule->status === VehiculeStatus::VALIDATED) {
        return redirect()->route('vehicules.selection');
    }

    return Inertia::render('Vehicules/PendingValidation', [
        'vehicule' => $vehicule->load('validator'),
    ]);
    }

    /**
     * Display a listing of the resource.(admin/validator view)
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
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('vehicule_type') && $request->get('vehicule_type') !== 'all') {
            $query->where('vehicule_type', $request->get('vehicule_type'));
        }

        $vehicules = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Vehicules/Index', [
            'vehicules' => $vehicules,
            'filters' => $request->only(['search', 'status']), // $vehicules est une collection pagination
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
        // Check for duplicate VIN
        $existingVehicule = Vehicule::where('vin', $request->vin)
            ->where('user_id', '!=', $request->user()->id)
            ->first();

        $status = $existingVehicule ? VehiculeStatus::DUPLICATE : VehiculeStatus::PENDING;

        $validationNotes = $existingVehicule
            ? "Un véhicule avec ce VIN existe déja  dans le système (PropriÃ©taire: {$existingVehicule->user->name})."
            : null;
        //
        $vehicule = Vehicule::create([
            'user_id' => $request->user()->id,
            'make' => $request->make,
            'model' => $request->model,
            'vehicule_type' => $request->vehicule_type,
            'year' => $request->year,
            'license_plate' => $request->license_plate,
            'vin' => $request->vin,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'mileage' => $request->mileage ?? 0,
            'status' => $status,
            'validation_notes' => $validationNotes,
        ]);

        return redirect()->route('vehicules.selection')
            ->with('message', 'Véhicule crée avec succès. ' .
                ($status === VehiculeStatus::DUPLICATE
                    ? 'Attention: Doublon détecté.'
                    : 'En attente de validation.'));
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

        // Only allow editing if status allows it
        if (!$vehicule->status->canEdit()) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Ce véhicule ne peut pas être modifié dans son état actuel.');
        }

        return Inertia::render('Vehicules/Edit', [
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehiculeRequest $request, Vehicule $vehicule)
    {
        //
         $this->authorize('update', $vehicule);

       // $vehicule->update($request->validated());

        // Check for duplicate VIN (excluding current vehicle)
        $existingVehicule = Vehicule::where('vin', $request->vin)
            ->where('id', '!=', $vehicule->id)
            ->where('user_id', '!=', $request->user()->id)
            ->first();

        $updateData = $request->validated();

        // Reset to pending if was rejected/to_correct/duplicate
        if (in_array($vehicule->status, [VehiculeStatus::REJECTED, VehiculeStatus::TO_CORRECT, VehiculeStatus::DUPLICATE])) {
            $updateData['status'] = $existingVehicule ? VehiculeStatus::DUPLICATE : VehiculeStatus::PENDING;
            $updateData['validation_notes'] = $existingVehicule
                ? "Un véhicule avec ce VIN existe déja  (PropriÃ©taire: {$existingVehicule->user->name})."
                : null;
            $updateData['validated_by'] = null;
            $updateData['validated_at'] = null;
        }

        $vehicule->update($updateData);

        return redirect()->route('vehicules.selection')
            ->with('message', 'Véhicule mis à  jour avec succès. ' .
                (isset($updateData['status']) && $updateData['status'] === VehiculeStatus::DUPLICATE
                    ? 'Attention: Doublon détecté.'
                    : 'Soumis à  nouveau pour validation.'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicule $vehicule)
    {
        //
         $this->authorize('delete', $vehicule);

        $vehicule->delete();

       return redirect()->route('vehicules.selection')
            ->with('message', 'Véhicule supprimée avec succès.');
    }

     /**
     * Validate or reject a vehicle (validator/admin only)
     */

    public function validateVehicule(Request $request, Vehicule $vehicule)
    {
        $this->authorize('validate', $vehicule);

        // Vérifier les permissions
        if (!$request->user()->canValidateVehicules()) {
            abort(403, 'Non autorisé à  valider des véhicules.');
        }

        // Validation des données
        $request->validate([
            'action' => 'required|in:validate,reject,correct',
            'notes' => 'nullable|string|max:1000',
        ]);

       $status = match($request->action) {
            'validate' => VehiculeStatus::VALIDATED,
            'reject' => VehiculeStatus::REJECTED,
            'correct' => VehiculeStatus::TO_CORRECT,
        };

        $vehicule->update([
            'status' => $status,
            'validation_notes' => $request->notes,
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        $message = match($request->action) {
            'validate' => 'Véhicule validé avec succès.',
            'reject' => 'Véhicule rejetÃ©.',
            'correct' => 'Corrections demandÃ©es au propriÃ©taire.',
        };

        return redirect()->back()->with('message', $message);
    }

    /**
     * Display pending vehicules (validator/admin only)
     */
    public function pending(Request $request)
    {
        if (!$request->user()->canValidateVehicules()) {
            abort(403, 'Non autorisÃ©.');
        }

        $query = Vehicule::where('status', VehiculeStatus::PENDING)
            ->with(['user']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $vehicules = $query->orderBy('created_at', 'asc')->paginate(15);

        return Inertia::render('Validator/Pending', [
            'vehicules' => $vehicules,
            'filters' => $request->only(['search']),
        ]);
    }

}