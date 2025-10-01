<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\VehiculeStatus;
use App\Http\Requests\VehiculeRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class VehiculeController extends Controller
{
    use AuthorizesRequests;

    public function selection(Request $request)
    {
        $user = $request->user();

        $vehicules = $user->vehicules()
            ->with(['validator', 'pendingEditRequests'])
            ->orderByRaw("FIELD(status, 'valide', 'en_attente', 'a_corriger', 'refuse', 'doublon')")
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Vehicules/Selection', [
            'vehicules' => $vehicules,
        ]);
    }

    public function select(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        if ($vehicule->status !== VehiculeStatus::VALIDATED) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Seuls les véhicules validés peuvent être sélectionnés.');
        }

        session(['selected_vehicule_id' => $vehicule->id]);

        return redirect()->route('dashboard')
            ->with('message', "Véhicule {$vehicule->full_name} sélectionné.");
    }

    public function pendingDetail(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        if ($vehicule->status === VehiculeStatus::VALIDATED) {
            return redirect()->route('vehicules.selection');
        }

        return Inertia::render('Vehicules/PendingValidation', [
            'vehicule' => $vehicule->load('validator'),
        ]);
    }

    public function index(Request $request)
    {
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
            'filters' => $request->only(['search', 'status']),
            'canValidate' => $user->canValidateVehicules(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Vehicules/Create');
    }

    public function store(VehiculeRequest $request)
    {
        $existingVehicule = Vehicule::where('vin', $request->vin)
            ->where('user_id', '!=', $request->user()->id)
            ->first();

        $status = $existingVehicule ? VehiculeStatus::DUPLICATE : VehiculeStatus::PENDING;

        $validationNotes = $existingVehicule
            ? "Un véhicule avec ce VIN existe déjà dans le système (Propriétaire: {$existingVehicule->user->name})."
            : null;

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
            ->with('message', 'Véhicule créé avec succès. ' .
                ($status === VehiculeStatus::DUPLICATE
                    ? 'Attention: Doublon détecté.'
                    : 'En attente de validation.'));
    }

    public function show(Request $request, Vehicule $vehicule)
    {
        $vehicule->load([
            'user',
            'validator',
            'assurances' => fn($q) => $q->orderBy('end_date', 'desc'),
            'maintenances' => fn($q) => $q->orderBy('maintenance_date', 'desc'),
            'reparations' => fn($q) => $q->orderBy('reparation_date', 'desc'),
            'ravitaillements' => fn($q) => $q->orderBy('ravitaillement_date', 'desc'),
            'trajets' => fn($q) => $q->orderBy('trajet_date', 'desc'),
            'editRequests' => fn($q) => $q->with('user')->orderBy('created_at', 'desc')->limit(5),
        ]);

        return Inertia::render('Vehicules/Show', [
            'vehicule' => $vehicule,
            'canValidate' => $request->user()->canValidateVehicules(),
            'canRequestEdit' => $vehicule->canRequestEdit() && $request->user()->id === $vehicule->user_id,
        ]);
    }

    public function edit(Vehicule $vehicule)
    {
        $user = Auth::user();
        
        // Les admins peuvent modifier les véhicules validés
        if ($user->isAdmin() && $vehicule->status === VehiculeStatus::VALIDATED) {
            return Inertia::render('Vehicules/AdminEdit', [
                'vehicule' => $vehicule->load('editRequests'),
                'isAdminEdit' => true,
            ]);
        }
        
        // Les clients ne peuvent modifier que certains statuts
        $this->authorize('update', $vehicule);

        if (!$vehicule->status->canEdit()) {
            return redirect()->route('vehicules.selection')
                ->with('error', 'Ce véhicule ne peut pas être modifié dans son état actuel.');
        }

        return Inertia::render('Vehicules/Edit', [
            'vehicule' => $vehicule,
            'isAdminEdit' => false,
        ]);
    }

    public function update(VehiculeRequest $request, Vehicule $vehicule)
    {
        $user = $request->user();
        
        // Admin peut modifier directement les véhicules validés
        if ($user->isAdmin() && $vehicule->status === VehiculeStatus::VALIDATED) {
            $vehicule->update($request->validated());
            
            return redirect()->route('vehicules.show', $vehicule)
                ->with('message', 'Véhicule mis à jour par l\'administrateur.');
        }
        
        // Pour les autres utilisateurs, logique normale
        $this->authorize('update', $vehicule);

        $existingVehicule = Vehicule::where('vin', $request->vin)
            ->where('id', '!=', $vehicule->id)
            ->where('user_id', '!=', $request->user()->id)
            ->first();

        $updateData = $request->validated();

        if (in_array($vehicule->status, [VehiculeStatus::REJECTED, VehiculeStatus::TO_CORRECT, VehiculeStatus::DUPLICATE])) {
            $updateData['status'] = $existingVehicule ? VehiculeStatus::DUPLICATE : VehiculeStatus::PENDING;
            $updateData['validation_notes'] = $existingVehicule
                ? "Un véhicule avec ce VIN existe déjà (Propriétaire: {$existingVehicule->user->name})."
                : null;
            $updateData['validated_by'] = null;
            $updateData['validated_at'] = null;
        }

        $vehicule->update($updateData);

        return redirect()->route('vehicules.selection')
            ->with('message', 'Véhicule mis à jour avec succès. ' .
                (isset($updateData['status']) && $updateData['status'] === VehiculeStatus::DUPLICATE
                    ? 'Attention: Doublon détecté.'
                    : 'Soumis à nouveau pour validation.'));
    }

    public function destroy(Vehicule $vehicule)
    {
        $this->authorize('delete', $vehicule);

        $vehicule->delete();

        return redirect()->route('vehicules.selection')
            ->with('message', 'Véhicule supprimé avec succès.');
    }

    public function validateVehicule(Request $request, Vehicule $vehicule)
    {
        $this->authorize('validate', $vehicule);

        if (!$request->user()->canValidateVehicules()) {
            abort(403, 'Non autorisé à valider des véhicules.');
        }

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
            'reject' => 'Véhicule rejeté.',
            'correct' => 'Corrections demandées au propriétaire.',
        };

        return redirect()->back()->with('message', $message);
    }

    public function pending(Request $request)
    {
        if (!$request->user()->canValidateVehicules()) {
            abort(403, 'Non autorisé.');
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