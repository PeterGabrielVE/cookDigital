<?php

namespace App\Http\Controllers;

use App\Products;
use App\Productunits;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage product'))
        {
            $products = Products::where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('products.index', compact('products'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create product'))
        {
            $productunits = Productunits::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            return view('products.create', compact('productunits'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create product'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'price' => 'required',
                                   'unit' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('products.index')->with('error', $messages->first());
            }

            $products              = new Products;
            $products->name        = $request->name;
            $products->price = $request->price;
            $products->unit = $request->unit;
            $products->description = $request->description;
            $products->created_by  = \Auth::user()->creatorId();
            $products->save();

            return redirect()->route('products.index')->with('success', __('Product successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }


    public function edit($id)
    {

        if(\Auth::user()->can('edit product'))
        {
            $productunits = Productunits::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $product = Products::findOrfail($id);
            if($product->created_by == \Auth::user()->creatorId())
            {
                return view('products.edit', compact('product','productunits'));
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


        if(\Auth::user()->can('edit product'))
        {
            $product = Products::findOrfail($id);
            if($product->created_by == \Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'price' => 'required',
                                       'unit' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('products')->with('error', $messages->first());
                }

                $product->name        = $request->name;
                $product->price = $request->price;
                $product->unit = $request->unit;
                $product->description = $request->description;
                $product->save();
                return redirect()->route('products.index')->with('success', __('product successfully updated.'));
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


        if(\Auth::user()->can('delete product'))
        {
            $product = Products::findOrfail($id);
            if($product->created_by == \Auth::user()->creatorId())
            {
                $product->delete();

                return redirect()->route('products.index')->with('success', __('Product successfully deleted.'));
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
}
