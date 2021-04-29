<?php

namespace App\Http\Controllers;

use App\Leads;
use App\Leadsource;
use App\Task;
use Illuminate\Http\Request;

class LeadsourceController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage lead source'))
        {
            $leadsources = Leadsource::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leadsources.index', compact('leadsources'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create lead source'))
        {
            return view('leadsources.create');
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create lead source'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('leadsources.index')->with('error', $messages->first());
            }
            $source             = new Leadsource();
            $source->name       = $request->name;
            $source->created_by = \Auth::user()->creatorId();
            $source->save();

            return redirect()->route('leadsources.index')->with('success', __('Lead Source successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit lead source'))
        {
            $leadsources = Leadsource::findOrfail($id);
            if($leadsources->created_by == \Auth::user()->creatorId())
            {
                return view('leadsources.edit', compact('leadsources'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function update(Request $request, $id)
    {


        if(\Auth::user()->can('edit lead source'))
        {
            $leadsource = Leadsource::findOrfail($id);
            if($leadsource->created_by == \Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $leadsource->name = $request->name;
                $leadsource->save();

                return redirect()->route('leadsources.index')->with('success', __('Lead Source successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function destroy($id)
    {


        if(\Auth::user()->can('delete lead source'))
        {
            $leadsource = Leadsource::findOrfail($id);
            if($leadsource->created_by == \Auth::user()->creatorId())
            {
                $checkStage = Task::where('stage', '=', $leadsource->id)->get()->toArray();;
                if(empty($checkStage))
                {
                    $leadsource->delete();
                    return redirect()->route('leadsources.index')->with('success', __('Lead Source successfully deleted.'));
                }
                else
                {
                    return redirect()->route('leadsources.index')->with('error', __('Lead already assign this source , so please remove or move lead to other source.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
