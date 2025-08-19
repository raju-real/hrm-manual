<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::latest()->paginate(20);
        return view('admin.configuration.designation_list', compact('designations'));
    }

    public function create()
    {
        $route = route('admin.designations.store');
        return view('admin.configuration.designation_add_edit', compact('route'));
    }

    public function store(Request $request)
    {
         $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('designations', 'name')->whereNull('deleted_at')
            ],
             'description' => 'nullable|sometimes|max:1000'
        ]);
        $designation = new Designation();
        $designation->name = $request->name;
        $designation->slug = Str::slug($request->name);
        $designation->description = $request->description;
        $designation->created_by = Auth::id();
        $designation->save();
        if($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $designation
            ]);
        } else {
         return redirect()->route('admin.designations.index')->with(successMessage());
        }
    }


    public function edit($slug)
    {
        $designation = Designation::whereSlug($slug)->first();
        $route = route('admin.designations.update', $designation->id);
        return view('admin.configuration.designation_add_edit', compact('designation', 'route'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('designations', 'name')->whereNull('deleted_at')->ignore($id),
            ],
            'description' => 'nullable|sometimes|max:1000'
        ]);

        $designation = Designation::findOrFail($id);
        $designation->name = $request->name;
        $designation->slug = Str::slug($request->name);
        $designation->description = $request->description;
        $designation->updated_by = Auth::id();
        $designation->save();
        return redirect()->route('admin.designations.index')->with(infoMessage());
    }


    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->update(['deleted_by' => Auth::id()]);
        $designation->delete();
        return redirect()->route('admin.designations.index')->with(deleteMessage());
    }
}
