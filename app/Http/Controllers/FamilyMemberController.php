<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class FamilyMemberController extends Controller
{
    private const ALLOWED_ROLES = ['Kepala Keluarga', 'Ibu Rumah Tangga', 'Anak'];

    public function index(Request $request): View
    {
        $family = $request->user()->family()->with('users.role')->first();
        $roles = Role::whereIn('role_name', self::ALLOWED_ROLES)->withCount([
            'users as family_users_count' => fn ($query) => $query->where('family_id', $request->user()->family_id),
        ])->orderBy('role_name')->get();
        $allMembers = $family?->users()->with('role')->orderBy('name')->get() ?? collect();
        $members = $family?->users()
            ->with('role')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', (bool) $request->boolean('status')))
            ->orderBy('name')
            ->get() ?? collect();

        return view('pages.family.members', [
            'family' => $family,
            'roles' => $roles,
            'members' => $members,
            'allMembers' => $allMembers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $familyId = $request->user()->family_id;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->whereIn('role_name', self::ALLOWED_ROLES)),
            ],
            'password' => ['required', Password::defaults()],
        ]);

        User::create($data + [
            'family_id' => $familyId,
            'is_active' => true,
        ]);

        return back()->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->family_id === $request->user()->family_id, 404);

        $data = $request->validate([
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->whereIn('role_name', self::ALLOWED_ROLES)),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update($data);

        return back()->with('success', 'Anggota keluarga berhasil diperbarui.');
    }
}
