<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Only super-admin and branch-managers can administer users
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager'])) {
            abort(403, 'غير مصرح لك بإدارة المستخدمين.');
        }

        $query = User::with(['roles', 'branch']);

        // Branch Managers are scoped to their branch users
        if (auth()->user()->hasRole('branch_manager')) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager'])) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = Role::all();

        // Branch managers cannot allocate across other branches
        if (auth()->user()->hasRole('branch_manager')) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
            $roles = Role::whereIn('name', ['sales_agent', 'finance_officer', 'reception'])->get();
        }

        return view('users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
        ]);

        // Branch manager constraint
        if (auth()->user()->hasRole('branch_manager')) {
            $validated['branch_id'] = auth()->user()->branch_id;
            if (in_array($validated['role'], ['super_admin'])) {
                abort(403, 'لا يمكنك منح صلاحيات أعلى.');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'branch_id' => $validated['branch_id'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'تم إنشاء المستخدم وتعيين الصلاحيات والفرع بنجاح.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager'])) {
            abort(403);
        }

        // Branch Manager constraint
        if (auth()->user()->hasRole('branch_manager') && $user->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = Role::all();

        if (auth()->user()->hasRole('branch_manager')) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
            $roles = Role::whereIn('name', ['sales_agent', 'finance_officer', 'reception'])->get();
        }

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'branch_manager'])) {
            abort(403);
        }

        if (auth()->user()->hasRole('branch_manager') && $user->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
        ]);

        if (auth()->user()->hasRole('branch_manager')) {
            $validated['branch_id'] = auth()->user()->branch_id;
            if (in_array($validated['role'], ['super_admin'])) {
                abort(403);
            }
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'branch_id' => $validated['branch_id'],
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Update roles
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', 'تم تحديث بيانات المستخدم وصلاحياته بنجاح.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'صلاحية الحذف مقتصرة على الإدارة العليا.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف حساب الموظف بنجاح.');
    }
}
