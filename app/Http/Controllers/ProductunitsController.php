<?php

namespace App\Http\Controllers;

use App\Productunits;
use Illuminate\Http\Request;

class ProductunitsController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product unit')) {
            $productunits = Productunits::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('productunits.index', compact('productunits'));
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create product unit')) {
            return view('productunits.create');
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create product unit')) {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('productunits.index')->with('error', $messages->first());
            }
            $productunits             = new Productunits();
            $productunits->name       = $request->name;
            $productunits->created_by = \Auth::user()->creatorId();
            $productunits->save();

            return redirect()->route('productunits.index')->with('success', __('Product unit successfully created.'));
        }else{
            return redirect()->back()->with('error',__('Permission denied.'));
        }
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit product unit')) {
            $productunits = Productunits::findOrfail($id);
            if($productunits->created_by == \Auth::user()->creatorId())
            {
                return view('productunits.edit', compact('productunits'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function update(Request $request, $id)
    {


        if(\Auth::user()->can('edit product unit')) {
            $leadsource = Productunits::findOrfail($id);
            if($leadsource->created_by == \Auth::user()->creatorId()) {

                $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:20',
                ]);

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('users')->with('error', $messages->first());
                }

                $leadsource->name = $request->name;
                $leadsource->save();
                return redirect()->route('productunits.index')->with('success',__('Product Unit successfully updated.'));
            }else{
                return redirect()->back()->with('error',__('Permission denied.'));
            }
        }else{
            return redirect()->back()->with('error',__('Permission denied.'));
        }
    }

    public function destroy($id)
    {


        if(\Auth::user()->can('delete product unit')) {
            $productunits = Productunits::findOrfail($id);
            if($productunits->created_by == \Auth::user()->creatorId()) {
                $productunits->delete();
                return redirect()->route('productunits.index')->with('success',__('Product Unit successfully deleted.'));
            }else{
                return redirect()->back()->with('error',__('Permission denied.'));
            }
        }else{
            return redirect()->back()->with('error',__('Permission denied.'));
        }
    }

}
