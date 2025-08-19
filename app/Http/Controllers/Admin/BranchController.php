<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(20);
        return view('admin.configuration.branch_list', compact('branches'));
    }

    public function create()
    {
        $route = route('admin.branches.store');
        return view('admin.configuration.branch_add_edit', compact('route'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches', 'name')->whereNull('deleted_at')
            ],
            'address' => 'required|sometimes|max:255'
        ]);

        $branch = new Branch();
        $branch->name = $request->name;
        $branch->slug = Str::slug($request->name);
        $branch->address = $request->address;
        $branch->created_by = Auth::id();
        $branch->save();
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $branch
            ]);
        } else {
            return redirect()->route('admin.branches.index')->with(successMessage());
        }
    }


    public function edit($slug)
    {
        $branch = Branch::whereSlug($slug)->first();
        $route = route('admin.branches.update', $branch->id);
        return view('admin.configuration.branch_add_edit', compact('branch', 'route'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches', 'name')->whereNull('deleted_at')->ignore($id),
            ],
            'address' => 'required|sometimes|max:255'
        ]);

        $branch = Branch::findOrFail($id);
        $branch->name = $request->name;
        $branch->slug = Str::slug($request->name);
        $branch->address = $request->address;
        $branch->updated_by = Auth::id();
        $branch->save();
        return redirect()->route('admin.branches.index')->with(infoMessage());
    }


    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->update(['deleted_by' => Auth::id()]);
        $branch->delete();
        return redirect()->route('admin.branches.index')->with(deleteMessage());
    }
}
