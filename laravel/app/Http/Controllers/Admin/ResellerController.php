<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->query('view', 'standard');
        $resellers = User::where('role', 'reseller')
            ->withSum([
                'resellerTransactions as total_sales_sum' => function ($query) {
                    $query->where('status', 'paid');
                }
            ], 'total_price')
            ->latest()
            ->paginate(10);

        return view('admin.resellers.index', compact('resellers', 'view'));
    }

    public function create()
    {
        return view('admin.resellers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        $reseller = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'reseller',
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'address' => $validated['address'] ?? null,
            'email_verified_at' => now(),
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $reseller->profile_photo_path = $path;
            $reseller->save();
        }

        return redirect()->route('admin.resellers.index')->with('success', 'Reseller created successfully.');
    }

    public function edit(User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Invalid user role.');
        }
        return view('admin.resellers.edit', compact('reseller'));
    }

    public function update(Request $request, User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Cannot edit this user.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($reseller->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $reseller->profile_photo_path = $path;
        }

        $reseller->name = $validated['name'];
        $reseller->email = $validated['email'];
        $reseller->phone = $validated['phone'];
        $reseller->bio = $validated['bio'];
        $reseller->address = $validated['address'];

        if (!empty($validated['password'])) {
            $reseller->password = Hash::make($validated['password']);
        }

        $reseller->save();

        return redirect()->route('admin.resellers.index')->with('success', 'Reseller updated successfully.');
    }

    public function destroy(User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Cannot delete this user.');
        }

        $reseller->delete();
        return redirect()->route('admin.resellers.index')->with('success', 'Reseller deleted successfully.');
    }

    public function deposits(User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Invalid reseller.');
        }

        $deposits = $reseller->deposits()->with('creator')->latest()->paginate(20);
        return view('admin.resellers.deposits', compact('reseller', 'deposits'));
    }

    public function storeDeposit(Request $request, User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Invalid reseller.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($reseller, $validated) {
            $reseller->deposits()->create([
                'amount' => $validated['amount'],
                'note' => $validated['note'],
                'created_by' => Auth::id(),
            ]);

            $reseller->increment('balance', $validated['amount']);
        });

        return back()->with('success', 'Deposit added successfully.');
    }

    public function editDeposit(User $reseller, \App\Models\ResellerDeposit $deposit)
    {
        if ($reseller->role !== 'reseller' || $deposit->user_id !== $reseller->id) {
            abort(404);
        }
        return view('admin.resellers.deposits-edit', compact('reseller', 'deposit'));
    }

    public function updateDeposit(Request $request, User $reseller, \App\Models\ResellerDeposit $deposit)
    {
        if ($reseller->role !== 'reseller' || $deposit->user_id !== $reseller->id) {
            return back()->with('error', 'Invalid request.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($reseller, $deposit, $validated) {
            $oldAmount = $deposit->amount;
            $newAmount = $validated['amount'];
            $difference = $newAmount - $oldAmount;

            // Update user balance
            $reseller->increment('balance', $difference);

            // Update deposit
            $deposit->update([
                'amount' => $newAmount,
                'note' => $validated['note'],
            ]);
        });

        return redirect()->route('admin.resellers.deposits', $reseller)->with('success', 'Deposit updated successfully.');
    }

    public function destroyDeposit(User $reseller, \App\Models\ResellerDeposit $deposit)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Invalid reseller.');
        }

        if ($deposit->user_id !== $reseller->id) {
            return back()->with('error', 'Deposit does not belong to this reseller.');
        }

        DB::transaction(function () use ($reseller, $deposit) {
            // Deduct the balance
            $reseller->decrement('balance', $deposit->amount);
            // Soft delete the deposit
            $deposit->delete();
        });

        return back()->with('success', 'Deposit deleted and balance reverted successfully.');
    }

    public function toggleActive(User $reseller)
    {
        if ($reseller->role !== 'reseller') {
            return back()->with('error', 'Cannot modify this user.');
        }

        $reseller->is_active = !$reseller->is_active;
        $reseller->save();

        $status = $reseller->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Reseller account has been {$status}.");
    }
}
