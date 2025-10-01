<?php

namespace App\Http\Controllers;

use App\Models\Administrateur;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class AdministrateurController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        $users = $query->withCount(['vehicles', 'repairs', 'maintenances'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'roles' => UserRole::cases(),
        ]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('message', 'Rôle utilisateur mis à jour avec succès.');
    }

    public function deleteUser(User $user)
    {
        if (isset($user) && $user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $user->delete();

        return redirect()->back()->with('message', 'Utilisateur supprimé avec succès.');
    }

    public function blockUser(User $user)
    {
        $user->update([
            'blocked_until' => now()->addDays(7),
        ]);

        return redirect()->back()->with('message', 'Utilisateur bloqué pour 7 jours.');
    }

    public function unblockUser(User $user)
    {
        $user->update([
            'blocked_until' => null,
            'login_attempts' => 0,
        ]);

        return redirect()->back()->with('message', 'Utilisateur débloqué avec succès.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Administrateur $administrateur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Administrateur $administrateur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administrateur $administrateur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administrateur $administrateur)
    {
        //
    }
}
