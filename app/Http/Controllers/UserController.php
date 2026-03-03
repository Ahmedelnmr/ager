<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'تم إنشاء المستخدم.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'role'      => 'required|exists:roles,name',
            'password'  => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'password'  => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم.');
    }

    public function auditLog()
    {
        $logs = AuditLog::with(['user', 'subject'])->latest('created_at')->paginate(30);
        return view('users.audit', compact('logs'));
    }
}
