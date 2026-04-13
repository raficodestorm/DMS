<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::where('branch_id', auth()->user()->branch_id)
            ->whereNotIn('rank', ['manager', 'admin']);

        if ($request->filled('search')) {
            $search = $request->search;


            if (str_starts_with($search, 'BRE100')) {
                $id = str_replace('BRE100', '', $search);

                $query->where('id', $id);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('id', $search);
                });
            }
        }

        $employees = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'table' => view('pages.manager.employee.table', compact('employees'))->render(),
                'mobile' => view('pages.manager.employee.mtable', compact('employees'))->render(),
            ]);
        }

        return view('pages.manager.employee.index', compact('employees'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.manager.employee.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rank' => 'required|string|max:100',
            'father' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        // photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        // add logged user id
        $validated['branch_id'] = auth()->user()->branch_id;

        Employee::create($validated);

        return redirect()->route('manager.employees.index')
            ->with('success', 'Employee added successfully!');
    }

    public function see($id)
    {
        // Fetch employee with branch relationship
        $employee = Employee::with('branch')->findOrFail($id);

        return view('pages.employee.profile', compact('employee'));
    }

    public function show(Employee $employee)
    {
        return view('pages.manager.employee.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.manager.employee.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rank' => 'required|string|max:100',
            'father' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($validated);

        return redirect()->route('manager.employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {

        $employee->delete();

        return redirect()->route('manager.employees.index')
            ->with('success', 'Employee deleted successfully');
    }
}
