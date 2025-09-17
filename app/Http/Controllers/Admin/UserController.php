<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $roles = ['admin', 'staff', 'agent', 'customer'];
        $statuses = ['active', 'inactive'];

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = ['admin', 'staff', 'agent', 'customer'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,agent,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        // If user is customer, create customer profile
        if ($request->role === 'customer') {
            Customer::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['customer', 'agentBookings', 'agentCustomers']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = ['admin', 'staff', 'agent', 'customer'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,staff,agent,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Handle customer profile
        if ($request->role === 'customer') {
            if (!$user->customer) {
                Customer::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'is_active' => $request->boolean('is_active', true),
                ]);
            } else {
                $user->customer->update([
                    'phone' => $request->phone,
                    'is_active' => $request->boolean('is_active', true),
                ]);
            }
        } else {
            // If role changed from customer, deactivate customer profile
            if ($user->customer) {
                $user->customer->update(['is_active' => false]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has active bookings
        if ($user->role === 'customer' && $user->customer) {
            $activeBookings = $user->customer->bookings()
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Cannot delete user with active bookings.');
            }
        }

        if ($user->role === 'agent') {
            $activeBookings = $user->agentBookings()
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Cannot delete agent with active bookings.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully.");
    }
}
