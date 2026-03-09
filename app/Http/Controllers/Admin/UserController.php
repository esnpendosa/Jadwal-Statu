<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->role($request->role))
            ->latest()
            ->paginate(15)->withQueryString();

        // Hanya tampilkan 2 role yang tersedia
        $roles = Role::whereIn('name', ['Admin', 'PIC'])->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'PIC'])->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users',
            'password'           => 'required|string|min:8|confirmed',
            'phone'              => 'nullable|string|max:20',
            'preferred_language' => 'required|in:id,en',
            'role'               => 'required|in:Admin,PIC',
        ]);

        $user = User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),
            'phone'              => $data['phone'] ?? null,
            'preferred_language' => $data['preferred_language'],
        ]);

        $user->assignRole($data['role']);
        AuditLog::log('create', "Created user: {$user->email}", $user);

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_created'));
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['Admin', 'PIC'])->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $user->id,
            'phone'              => 'nullable|string|max:20',
            'preferred_language' => 'required|in:id,en',
            'role'               => 'required|in:Admin,PIC',
        ]);

        $old = $user->toArray();
        $user->update([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'phone'              => $data['phone'] ?? null,
            'preferred_language' => $data['preferred_language'],
        ]);
        $user->syncRoles([$data['role']]);
        AuditLog::log('update', "Updated user: {$user->email}", $user, $old, $data);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.cannot_delete_self'));
        }
        AuditLog::log('delete', "Deleted user: {$user->email}", $user);
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user_deleted'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active
            ? __('admin.user_activated')
            : __('admin.user_deactivated'));
    }
}
