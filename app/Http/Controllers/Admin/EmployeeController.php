<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EmployeeController extends Controller
{
    use UploadHelper;

    public function index(Request $request)
    {
        $query = Employee::orderBy('branch_id', 'asc');

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
                'table' => view('pages.admin.employee.table', compact('employees'))->render(),
                'mobile' => view('pages.admin.employee.mtable', compact('employees'))->render(),
            ]);
        }

        return view('pages.admin.employee.index', compact('employees'));
    }


    public function create()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.admin.employee.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rank' => 'required|string|max:100',
            'branch_id' => 'required|integer|max:100',
            'father' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        // photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadFile($request->file('photo'), 'employees');
        }

        Employee::create($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee added successfully!');
    }



    public function show(Employee $employee)
    {
        return view('pages.admin.employee.show', compact('employee'));
    }

    public function showqr(Employee $employee)
    {
        return view('pages.admin.employee.qrcode', compact('employee'));
    }

    public function downloadQR(Employee $employee)
    {

        $qr = QrCode::format('svg')
            ->size(300)
            ->generate(url('/our/employee/' . $employee->id));

        return response($qr)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="employee-' . $employee->id . '-qr.svg"');
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.admin.employee.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rank' => 'required|string|max:100',
            'branch_id' => 'nullable|integer|max:100',
            'father' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $this->deleteFile($employee->photo);
            $validated['photo'] = $this->uploadFile($request->file('photo'), 'employees');
        }

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $this->deleteFile($employee->photo);
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully');
    }
}
