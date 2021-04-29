<?php

namespace App\Http\Controllers;

use App\ExpenseCategory;
use App\Leads;
use App\Leadsource;
use App\Leadstages;
use App\User;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage lead'))
        {
            $stages = Leadstages::where('created_by', '=', \Auth::user()->creatorId())->orderBy('order')->get();

            return view('leads.index', compact('stages'));
        }
        else
        {
            if(\Auth::user()->type == 'client')
            {
                $leads = Leads::select('leads.*', 'leadstages.name as stage_name')->join('leadstages', 'leadstages.id', '=', 'leads.stage')->where('leads.client', \Auth::user()->id)->where('leadstages.created_by', \Auth::user()->creatorId())->get();

                return view('leads.client_index', compact('leads'));
            }

            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create lead'))
        {
            $stages  = Leadstages::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $owners  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->get()->pluck('name', 'id');
            $clients = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'client')->get()->pluck('name', 'id');
            $sources = Leadsource::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('leads.create', compact('stages', 'owners', 'clients', 'sources'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create lead'))
        {
            if(\Auth::user()->type == 'company')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'price' => 'required',
                                       'stage' => 'required',
                                       'owner' => 'required',
                                       'client' => 'required',
                                       'source' => 'required',
                                   ]
                );
            }
            else
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'price' => 'required',
                                       'stage' => 'required',
                                       'client' => 'required',
                                       'source' => 'required',
                                   ]
                );
            }


            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('leads.index')->with('error', $messages->first());
            }
            $leads        = new Leads();
            $leads->name  = $request->name;
            $leads->price = $request->price;
            $leads->stage = $request->stage;
            if(\Auth::user()->type == 'company')
            {
                $leads->owner = $request->owner;
            }
            else
            {
                $leads->owner = \Auth::user()->id;
            }


            $leads->client     = $request->client;
            $leads->source     = $request->source;
            $leads->notes      = $request->notes;
            $leads->created_by = \Auth::user()->creatorId();
            $leads->save();

            return redirect()->route('leads.index')->with('success', __('Lead successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Leads::findOrfail($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $stages = Leadstages::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                //                $owners  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'employee')->get()->pluck('name', 'id');
                $owners  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->get()->pluck('name', 'id');
                $clients = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'client')->get()->pluck('name', 'id');
                $sources = Leadsource::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('leads.edit', compact('stages', 'owners', 'clients', 'sources', 'lead'));
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


        if(\Auth::user()->can('edit lead'))
        {
            $leads = Leads::findOrfail($id);
            if($leads->created_by == \Auth::user()->creatorId())
            {
                if(\Auth::user()->type == 'company')
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'name' => 'required|max:20',
                                           'price' => 'required',
                                           'stage' => 'required',
                                           'owner' => 'required',
                                           'client' => 'required',
                                           'source' => 'required',
                                       ]
                    );
                }
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('leads.index')->with('error', $messages->first());
                }
                $leads->name       = $request->name;
                $leads->price      = $request->price;
                $leads->stage      = $request->stage;
                $leads->owner      = $request->owner;
                $leads->client     = $request->client;
                $leads->source     = $request->source;
                $leads->notes      = $request->notes;
                $leads->created_by = \Auth::user()->creatorId();
                $leads->save();

                return redirect()->route('leads.index')->with('success', __('Lead successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {


        if(\Auth::user()->can('delete lead'))
        {
            $leads = Leads::findOrfail($id);
            if($leads->created_by == \Auth::user()->creatorId())
            {
                $leads->delete();
                $leads->removeProjectLead($id);

                return redirect()->route('leads.index')->with('success', __('Lead successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function order(Request $request)
    {
        $post  = $request->all();
        $lead  = Leads::find($post['lead_id']);
        $stage = Leadstages::find($post['stage_id']);

        if(!empty($stage))
        {
            $lead->stage = $post['stage_id'];
            $lead->save();
        }

        foreach($post['order'] as $key => $item)
        {
            $lead_order             = Leads::find($item);
            $lead_order->item_order = $key;
            $lead_order->stage      = $post['stage_id'];
            $lead_order->save();
        }
    }
}
