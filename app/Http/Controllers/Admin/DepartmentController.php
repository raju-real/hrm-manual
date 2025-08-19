<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::latest()->paginate(20);
        return view('admin.configuration.department_list', compact('departments'));
    }

    public function create()
    {
        $route = route('admin.departments.store');
        return view('admin.configuration.department_add_edit', compact('route'));
    }

    public function store(Request $request)
    {
         $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'name')->whereNull('deleted_at')
            ],
             'description' => 'nullable|sometimes|max:1000'
        ]);
        $department = new Department();
        $department->name = $request->name;
        $department->slug = Str::slug($request->name);
        $department->description = $request->description;
        $department->created_by = Auth::id();
        $department->save();
        if($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $department
            ]);
        } else {
         return redirect()->route('admin.departments.index')->with(successMessage());
        }
    }


    public function edit($slug)
    {
        $department = Department::whereSlug($slug)->first();
        $route = route('admin.departments.update', $department->id);
        return view('admin.configuration.department_add_edit', compact('department', 'route'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'name')->whereNull('deleted_at')->ignore($id),
            ],
            'description' => 'nullable|sometimes|max:1000'
        ]);

        $department = Department::findOrFail($id);
        $department->name = $request->name;
        $department->slug = Str::slug($request->name);
        $department->description = $request->description;
        $department->updated_by = Auth::id();
        $department->save();
        return redirect()->route('admin.departments.index')->with(infoMessage());
    }


    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->update(['deleted_by' => Auth::id()]);
        $department->delete();
        return redirect()->route('admin.departments.index')->with(deleteMessage());
    }
}
