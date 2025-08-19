<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('admin.profile.edit_profile');
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required',
                'string',
                'max:50',
            ],
            'email' => [
                'required',
                'email',
                'max:30',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore(authUser()->id)
            ],
            'mobile' => [
                'required',
                'string',
                'max:11',
                Rule::unique('users', 'mobile')->whereNull('deleted_at')->ignore(authUser()->id)
            ],
            'image' => 'nullable|sometimes|mimes:jpg,jpeg,png|max:1024',
        ]);

        $admin = User::find(authUser()->id);
        if ($request->file('image')) {
            if ($admin->image !== null and file_exists($admin->image)) {
                unlink($admin->image);
            }
            $admin->image = uploadImage($request->file('image'), 'admin');
        }
        $admin->save();
        return redirect()->route('admin.profile')->with(infoMessage());
    }

}
