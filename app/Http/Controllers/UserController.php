<?php

namespace App\Http\Controllers;

use App\Notification;
use App\User;
use App\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if(Auth::user()->can('manage user'))
        {
            if(Auth::user()->type == 'super admin')
            {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '=', 'company')->get();
            }
            else
            {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '!=', 'client')->get();
            }

            return view('user.index')->with('users', $users);
        }
        else
        {
            return redirect()->back();
        }
    }

    public function create()
    {
        $user  = Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        if(Auth::user()->can('create user'))
        {
            return view('user.create', compact('roles'));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->can('create user'))
        {
            $resp = '';

            if(Auth::user()->type == 'super admin')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:120',
                                       'email' => 'required|email|unique:users',
                                       'password' => 'required|min:6',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users.index')->with('error', $messages->first());
                }

                $user               = new User();
                $user['name']       = $request->name;
                $user['email']      = $request->email;
                $user['password']   = Hash::make($request->password);
                $user['type']       = 'company';
                $user['lang']       = Utility::getValByName('default_language');
                $user['created_by'] = Auth::user()->creatorId();
                $user->save();

                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                $user->makeEmployeeRole();
                $user->userDefaultData();
            }
            else
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:120',
                                       'email' => 'required|email|unique:users',
                                       'password' => 'required|min:6',
                                       'role' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users.index')->with('error', $messages->first());
                }

                $objUser    = Auth::user();
                $total_user = $objUser->countUsers();
                $password   = $request->password;

                $role_r                = Role::findById($request->role);
                $request['password']   = Hash::make($request->password);
                $request['type']       = $role_r->name;
                $request['lang']       = Utility::getValByName('default_language');
                $request['created_by'] = Auth::user()->creatorId();
                $user                  = User::create($request->all());

                $uArr = [
                    'email' => $request->email,
                    'password' => $password,
                ];

                // Send Email
                $resp = Utility::sendEmailTemplate('New User', $user->id, $uArr);

                $user->assignRole($role_r);
            }

            return redirect()->route('users.index')->with(
                'success', __('User successfully added.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '')
            );
        }
        else
        {
            return redirect()->back();
        }

    }

    public function edit($id)
    {
        $user  = Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        if(Auth::user()->can('edit user'))
        {
            $user = User::findOrFail($id);

            return view('user.edit', compact('user', 'roles'));
        }
        else
        {
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {

        if(Auth::user()->can('edit user'))
        {
            if(Auth::user()->type == 'super admin')
            {
                $user = User::findOrFail($id);

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:120',
                                       'email' => 'required|email|unique:users,email,' . $id,
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users.index')->with('error', $messages->first());
                }

                $input = $request->all();
                $user->fill($input)->save();

                return redirect()->route('users.index')->with(
                    'success', __('User successfully updated.')
                );
            }
            else
            {
                $user      = User::findOrFail($id);
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:120',
                                       'email' => 'required|email|unique:users,email,' . $id,
                                       'role' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users.index')->with('error', $messages->first());
                }

                $role          = Role::findById($request->role);
                $input         = $request->all();
                $input['type'] = $role->name;
                $user->fill($input)->save();

                $roles[] = $request->role;
                $user->roles()->sync($roles);

                return redirect()->route('users.index')->with(
                    'success', __('User successfully updated.')
                );
            }
        }
        else
        {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->can('delete user'))
        {
            $user = User::find($id);
            if($user)
            {
                if(Auth::user()->type == 'super admin')
                {
                    if($user->delete_status == 0)
                    {
                        $user->delete_status = 1;
                    }
                    else
                    {
                        $user->delete_status = 0;
                    }
                    $user->save();
                }
                else
                {
                    $user->delete();
                    $user->destroyUserProjectInfo($user->id);
                    $user->removeUserLeadInfo($user->id);
                    $user->destroyUserNotesInfo($user->id);
                    $user->removeUserExpenseInfo($user->id);
                    $user->removeUserTaskInfo($user->id);
                    $user->destroyUserTaskAllInfo($user->id);
                }

                return redirect()->route('users.index')->with('success', __('User Deleted Successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back();
        }
    }

    public function profile()
    {
        $userDetail = Auth::user();

        return view('user.profile')->with('userDetail', $userDetail);
    }

    public function editprofile(Request $request)
    {
        $userDetail = Auth::user();
        $user       = User::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                    ]
        );

        if($request->hasFile('profile') || !empty($request->profile))
        {
            $image_path = storage_path('avatar/') . $request->profile;
            if(\File::exists($image_path))
            {
                \File::delete($image_path);
            }

            $avatarName = 'avatar_' . time() . '.' . $request->profile->getClientOriginalExtension();
            $request->profile->storeAs('avatar', $avatarName);
            $user['avatar'] = $avatarName;
        }


        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();

        return redirect()->back()->with('success', __('Profile successfully updated.'));
    }

    public function updatePassword(Request $request)
    {
        if(Auth::user()->can('change password account'))
        {
            if(Auth::Check())
            {
                $request->validate(
                    [
                        'current_password' => 'required',
                        'new_password' => 'required|min:6',
                        'confirm_password' => 'required|same:new_password',
                    ]
                );
                $objUser          = Auth::user();
                $request_data     = $request->All();
                $current_password = $objUser->password;
                if(Hash::check($request_data['current_password'], $current_password))
                {
                    $user_id            = Auth::User()->id;
                    $obj_user           = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['new_password']);;
                    $obj_user->save();

                    return redirect()->back()->with('success', __('Password updated successfully.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Please enter correct current password.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function notificationSeen($user_id)
    {
        Notification::where('user_id', '=', $user_id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }
}
