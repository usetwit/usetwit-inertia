<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UpdateCompanyProfileRequest;
use App\Http\Requests\Admin\Users\UpdateEmployeeIdRequest;
use App\Http\Requests\Admin\Users\UpdatePasswordRequest;
use App\Http\Requests\Admin\Users\UpdatePersonalProfileRequest;
use App\Http\Requests\Admin\Users\UpdateProtectedInfoRequest;
use App\Http\Requests\Admin\Users\UpdateRoleRequest;
use App\Http\Requests\Admin\Users\UpdateUsernameRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserUpdateController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => Hash::make($request->input('new_password'))]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function updatePersonalProfile(UpdatePersonalProfileRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only([
            'first_name',
            'middle_names',
            'last_name',
            'dob',
            'personal_number',
            'personal_mobile_number',
            'personal_email',
        ]));

        return redirect()->back()->with('success', 'Personal Profile Updated Successfully');
    }

    public function updateCompanyProfile(UpdateCompanyProfileRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only([
            'email',
            'company_number',
            'company_ext',
            'company_mobile_number',
        ]));

        return redirect()->back()->with('success', 'Company Profile Updated Successfully');
    }

    public function updateProtectedInfo(UpdateProtectedInfoRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only([
            'joined_at',
            'left_at',
        ]));

        return redirect()->back()->with('success', 'Protected Info Updated Successfully');
    }

    public function updateRole(UpdateRoleRequest $request, User $user): RedirectResponse
    {
        $user->syncRoles($request->integer('role_id'));

        return redirect()->back()->with('success', 'Role Updated Successfully');
    }

    public function updateUsername(UpdateUsernameRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()->back()->with('success', 'Username Updated Successfully');
    }

    public function updateEmployeeId(UpdateEmployeeIdRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()->back()->with('success', 'Employee ID Updated Successfully');
    }
}
