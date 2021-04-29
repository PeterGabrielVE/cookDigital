<?php

namespace App\Http\Controllers;

use App\Expense;
use App\ExpensesCategory;
use App\Productunits;
use Illuminate\Http\Request;

class ExpensesCategoryController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage expense category')) {
            $expenses = ExpensesCategory::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('expensescategory.index', compact('expenses'));
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function create()
    {
        if(\Auth::user()->can('create expense category')) {
            return view('expensescategory.create');
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function store(Request $request)
    {

        if(\Auth::user()->can('create expense category')) {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('expensescategory.index')->with('error', $messages->first());
            }
            $expense             = new ExpensesCategory();
            $expense->name       = $request->name;
            $expense->created_by = \Auth::user()->authId();
            $expense->save();

            return redirect()->route('expensescategory.index')->with('success', __('Expense Category successfully created.'));
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }


    public function edit($id)
    {

        if(\Auth::user()->can('edit expense category')) {
            $expense = ExpensesCategory::findOrfail($id);
            if($expense->created_by == \Auth::user()->creatorId())
            {
                return view('expensescategory.edit', compact('expense'));
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
        
        if(\Auth::user()->can('edit expense category')) {
            $expense = ExpensesCategory::findOrfail($id);
            if($expense->created_by == \Auth::user()->creatorId()) {

                $validator = \Validator::make($request->all(), [
                    'name' => 'required|max:20',
                ]);

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('users')->with('error', $messages->first());
                }

                $expense->name = $request->name;
                $expense->save();
                return redirect()->route('expensescategory.index')->with('success',__('Expense category successfully updated.'));
            }else{
                return redirect()->back()->with('error',__('Permission denied.'));
            }
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }

    public function destroy($id)
    {
        
        if(\Auth::user()->can('delete expense category')) {
            $expense = ExpensesCategory::findOrfail($id);
            if($expense->created_by == \Auth::user()->creatorId()) {

                $expense->delete();
                Expense::where('category_id', '=', $expense->id)->update(array('category_id' => 0));
                return redirect()->route('expensescategory.index')->with('success',__('Expense category successfully deleted.'));
            }else{
                return redirect()->back()->with('error',__('Permission denied.'));
            }
        }else{
            return redirect()->back()->with('error','Permission denied.');
        }
    }
}
