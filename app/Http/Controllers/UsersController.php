<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Concerns\ProfileValidationRules;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Flux\Flux;

class UsersController extends Controller
{
    use ProfileValidationRules;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CreatesNewUsers $creator)
    {
        $this->authorize('create', User::class);

        $creator->create($request->all());

        return redirect()
            ->route('users.index')
            ->with('flux.toast', ['variant' => 'success', 'text' => 'User created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        // Menggunakan trait untuk validasi otomatis mengabaikan ID user ini agar tidak error "already taken"
        $validated = $request->validate($this->profileRules((int) $user->id));

        $user->update($validated);

        return redirect()
            ->route('users.show', ['user' => $user->id])
            ->with('flux.toast', ['variant' => 'success', 'text' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('flux.toast', ['variant' => 'success', 'text' => 'User deleted successfully.']);
    }
}
