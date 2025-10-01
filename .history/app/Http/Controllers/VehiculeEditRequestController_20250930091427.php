<?php

namespace App\Http\Controllers;

use App\Models\VehiculeEditRequest;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VehiculeEditRequestController extends Controller
{
    use AuthorizesRequests;

    /**
     * Afficher le formulaire de demande de modification (Client)
     */
    public function create(Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        if (!$vehicule->canRequestEdit()) {
            return redirect()->route('vehicules.show', $vehicule)
                ->with('error', 'Vous ne pouvez pas demander de modification pour ce véhicule.');
        }

        return Inertia::render('Vehicules/RequestEdit', [
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * Soumettre une demande de modification (Client)
     */
    public function store(Request $request, Vehicule $vehicule)
    {
        $this->authorize('view', $vehicule);

        if (!$vehicule->canRequestEdit()) {
            return redirect()->route('vehicules.show', $vehicule)
                ->with('error', 'Vous ne pouvez pas demander de modification pour ce véhicule.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'requested_changes' => 'nullable|array',
        ]);

        VehiculeEditRequest::create([
            'vehicule_id' => $vehicule->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'requested_changes' => $validated['requested_changes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('vehicules.show', $vehicule)
            ->with('message', 'Votre demande de modification a été envoyée avec succès.');
    }

    /**
     * Liste des demandes de modification (Admin)
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Non autorisé.');
        }

        $query = VehiculeEditRequest::with(['vehicule.user', 'user', 'reviewer'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('vehicule', function($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $editRequests = $query->paginate(15);

        return Inertia::render('Admin/EditRequests/Index', [
            'editRequests' => $editRequests,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Afficher une demande de modification (Admin)
     */
    public function show(Request $request, VehiculeEditRequest $editRequest)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Non autorisé.');
        }

        $editRequest->load(['vehicule.user', 'user', 'reviewer']);

        return Inertia::render('Admin/EditRequests/Show', [
            'editRequest' => $editRequest,
        ]);
    }

    /**
     * Approuver et permettre la modification (Admin)
     */
    public function approve(Request $request, VehiculeEditRequest $editRequest)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Non autorisé.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $editRequest->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return redirect()->route('admin.edit-requests.show', $editRequest)
            ->with('message', 'Demande approuvée. Vous pouvez maintenant modifier le véhicule.');
    }

    /**
     * Rejeter la demande (Admin)
     */
    public function reject(Request $request, VehiculeEditRequest $editRequest)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Non autorisé.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $editRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.edit-requests.index')
            ->with('message', 'Demande rejetée.');
    }
}