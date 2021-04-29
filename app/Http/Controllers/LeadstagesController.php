<?php

namespace App\Http\Controllers;

use App\Leads;
use App\Leadstages;
use Auth;
use Illuminate\Http\Request;

class LeadstagesController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage lead stage'))
        {
            $leadstages = Leadstages::where('created_by', '=', \Auth::user()->creatorId())->orderBy('order')->get();

            return view('leadstages.index', compact('leadstages'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create lead stage'))
        {
            return view('leadstages.create');
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create lead stage'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('leadstages.index')->with('error', $messages->first());
            }
            $all_stage         = Leadstages::where('created_by', \Auth::user()->creatorId())->orderBy('id', 'DESC')->first();
            $stage             = new Leadstages();
            $stage->name       = $request->name;
            $stage->created_by = \Auth::user()->creatorId();
            $stage->color      = '#' . $request->color;
            $stage->order      = (!empty($all_stage) ? ($all_stage->order + 1) : 0);
            $stage->save();

            return redirect()->route('leadstages.index')->with('success', __('Lead stage successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit lead stage'))
        {
            $leadstages = Leadstages::findOrfail($id);
            if($leadstages->created_by == \Auth::user()->creatorId())
            {
                return view('leadstages.edit', compact('leadstages'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit lead stage'))
        {
            $leadstages = Leadstages::findOrfail($id);
            if($leadstages->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('leadstages.index')->with('error', $messages->first());
                }

                $leadstages->name  = $request->name;
                $leadstages->color = '#' . $request->color;
                $leadstages->save();

                return redirect()->route('leadstages.index')->with('success', __('Lead stage successfully updated.'));
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


        if(\Auth::user()->can('delete lead stage'))
        {

            $leadstages = Leadstages::findOrfail($id);
            if($leadstages->created_by == \Auth::user()->creatorId())
            {
                $checkLead = Leads::where('stage', '=', $leadstages->id)->get()->toArray();
                if(empty($checkLead))
                {
                    $leadstages->delete();

                    return redirect()->route('leadstages.index')->with('success', __('Lead stage successfully deleted.'));
                }
                else
                {
                    return redirect()->route('leadstages.index')->with('error', __('Lead already assign this stage , so please remove or move lead to other lead stage.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function order(Request $request)
    {
        $post = $request->all();
        foreach($post['order'] as $key => $item)
        {
            $stage        = Leadstages::where('id', '=', $item)->first();
            $stage->order = $key;
            $stage->save();
        }
    }
}
