<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $authId = auth()->id();

        $employees = User::when($request->search, function ($q, $search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("id = ? DESC", [$authId]) // akun login paling atas
            ->orderBy('name')                     // sisanya urut A-Z
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.employees._table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees'));
    }


    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $employee->id,
        ]);

        $employee->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan diperbarui');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return back()->with('success', 'Karyawan dihapus');
    }

    public function resetPassword($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $user->password = Hash::make('password123');
        $user->save();

        return back()->with('success', 'Password berhasil direset ke: password123');
    }
}
