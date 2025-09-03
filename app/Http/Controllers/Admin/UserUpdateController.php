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
use Illuminate\Support\Facades\Hash;

class UserUpdateController extends Controller
{
    /**
     * @return string
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        $user->update(['password' => Hash::make($request->input('new_password'))]);

        return 'Password updated successfully';
    }

    /**
     * @return string
     */
    public function updatePersonalProfile(UpdatePersonalProfileRequest $request, User $user)
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

        return redirect()->back();
    }

    /**
     * @return string
     */
    public function updateCompanyProfile(UpdateCompanyProfileRequest $request, User $user)
    {
        $user->update($request->only([
            'email',
            'company_number',
            'company_ext',
            'company_mobile_number',
        ]));

        return 'Company profile updated successfully';
    }

    /**
     * @return string
     */
    public function updateProtectedInfo(UpdateProtectedInfoRequest $request, User $user)
    {
        $user->update($request->only([
            'username',
            'joined_at',
            'left_at',
            'employee_id',
        ]));

        $user->syncRoles($request->input('role_id'));

        return 'Protected info updated successfully';
    }

    /**
     * @return string
     */
    public function updateUsername(UpdateUsernameRequest $request, User $user)
    {
        $user->update(['username' => $request->input('username')]);

        return 'Username updated successfully';
    }

    /**
     * @return string
     */
    public function updateEmployeeId(UpdateEmployeeIdRequest $request, User $user)
    {
        $user->update(['employee_id' => $request->input('employee_id')]);

        return 'Employee ID updated successfully';
    }
}
