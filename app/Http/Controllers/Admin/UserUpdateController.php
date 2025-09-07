<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UpdateCompanyProfileRequest;
use App\Http\Requests\Admin\Users\UpdateEmployeeIdRequest;
use App\Http\Requests\Admin\Users\UpdatePasswordRequest;
use App\Http\Requests\Admin\Users\UpdatePersonalProfileRequest;
use App\Http\Requests\Admin\Users\UpdateProtectedInfoRequest;
use App\Http\Requests\Admin\Users\UpdateUsernameRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserUpdateController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => Hash::make($request->input('new_password'))]);

        return redirect()->back();
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

        return redirect()->back()->with(['success' => 'Personal Profile Updated Successfully']);
    }

    public function updateCompanyProfile(UpdateCompanyProfileRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only([
            'email',
            'company_number',
            'company_ext',
            'company_mobile_number',
        ]));

        return redirect()->back()->with(['success' => 'Company Profile Updated Successfully']);
    }

    public function updateProtectedInfo(UpdateProtectedInfoRequest $request, User $user): RedirectResponse
    {
        $user->update($request->only([
            'username',
            'joined_at',
            'left_at',
            'employee_id',
        ]));

        $user->syncRoles($request->input('role_id'));

        return redirect()->back();
    }

    public function updateUsername(UpdateUsernameRequest $request, User $user): RedirectResponse
    {
        $user->update(['username' => $request->input('username')]);

        return redirect()->back();
    }

    public function updateEmployeeId(UpdateEmployeeIdRequest $request, User $user): RedirectResponse
    {
        $user->update(['employee_id' => $request->input('employee_id')]);

        return redirect()->back();
    }
}
