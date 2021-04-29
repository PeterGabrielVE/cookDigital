<?php

namespace App\Http\Controllers;

use App\Estimation;
use App\User;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    public function index()
    {
        $client = \Auth::user();
        if(\Auth::user()->can('manage client'))
        {
            $clients = User::where('created_by', '=', $client->creatorId())->where('type', '=', 'client')->get();

            return view('client.index')->with('clients', $clients);
        }
        else
        {
            return redirect()->back();
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create client'))
        {
            return view('client.create');
        }
        else
        {
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create client'))
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

                return redirect()->route('clients.index')->with('error', $messages->first());
            }

            $objUser      = \Auth::user();
            $total_client = $objUser->countClient();
            $password     = $request->password;

            $request['password']   = Hash::make($request->password);
            $request['type']       = 'client';
            $request['lang']       = Utility::getValByName('default_language');
            $request['created_by'] = \Auth::user()->creatorId();
            $user                  = User::create($request->all());
            $role_r                = Role::findByName('client');

            $uArr = [
                'email' => $request->email,
                'password' => $password,
            ];

            // Send Email
            $resp = Utility::sendEmailTemplate('New User', $user->id, $uArr);

            $user->assignRole($role_r);

            return redirect()->route('clients.index')->with('success', __('Client successfully added.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit client'))
        {
            $client = User::findOrFail($id);

            return view('client.edit', compact('client'));
        }
        else
        {
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit client'))
        {
            $client    = User::findOrFail($id);
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users,email,' . $id,
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('clients.index')->with('error', $messages->first());
            }


            $input = $request->all();
            $client->fill($input)->save();

            return redirect()->route('clients.index')->with('success', __('Client successfully updated.'));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->can('delete client'))
        {
            $user       = User::find($id);
            $estimation = Estimation::where('client_id', '=', $id)->first();

            if(empty($estimation))
            {
                if($user)
                {
                    $user->delete();
                    $user->destroyUserNotesInfo($user->id);
                    $user->removeClientProjectInfo($user->id);
                    $user->removeClientLeadInfo($user->id);
                    $user->destroyUserTaskAllInfo($user->id);

                    return redirect()->route('clients.index')->with('success', __('Client Deleted Successfully.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Something is wrong.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('This client has assigned some estimation.'));
            }
        }
        else
        {
            return redirect()->back();
        }
    }
}
