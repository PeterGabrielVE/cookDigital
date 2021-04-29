<?php

namespace App\Http\Controllers;

use App\Bug;
use App\Invoice;
use App\Task;
use Illuminate\Http\Request;

class CalenderController extends Controller
{
    public function index()
    {
        $all_tasks    = \Auth::user()->project_all_task();
        $all_bugs     = \Auth::user()->project_all_bug();
        $due_invoices = \Auth::user()->project_due_invoice();

        $taskArray = array();
        foreach($all_tasks as $task)
        {
            $taskData['title']           = $task['name'] . ' / ' . $task['title'];
            $taskData['start']           = $task['task_start_date'];
            $taskData['end']             = $task['task_due_date'];
            $taskData['backgroundColor'] = '#b6e6ea';
            $taskData['url']             = route('task.show', $task['task_id']);
            $taskData['type']            = 'task';
            $taskData['eventId']         = $task['task_id'];
            $taskData['eventUrl']        = route('calender.event.date');
            $taskArray[]                 = $taskData;
        }


        $bugArray = array();
        foreach($all_bugs as $bug)
        {
            $bugData['title']           = $bug['name'] . ' / ' . $bug['title'];
            $bugData['start']           = $bug['bug_start_date'];
            $bugData['end']             = $bug['bug_due_date'];
            $bugData['backgroundColor'] = '#f1b8bc';
            $bugData['url']             = route(
                'task.bug.show', array(
                                   $bug['id'],
                                   $bug['bug_id'],
                               )
            );
            $bugData['type']            = 'bug';
            $bugData['eventId']         = $bug['bug_id'];
            $bugData['eventUrl']        = route('calender.event.date');
            $bugArray[]                 = $bugData;
        }

        $dueInvoices = array();
        if(!isset($due_invoices) && !empty($due_invoices))
        {
            foreach($due_invoices as $key => $invoice)
            {
                $dueAmount = $invoice->getDue();
                if($dueAmount > 0)
                {
                    $invoiceData['title']           = \Auth::user()->invoiceNumberFormat($invoice->invoice_id) . ' / ' . \Auth::user()->priceFormat($dueAmount);
                    $invoiceData['start']           = $invoice['due_date'];
                    $invoiceData['backgroundColor'] = '#e8d99a';
                    $invoiceData['url']             = route('invoices.show', $invoice->id);
                    $invoiceData['type']            = 'invoice';
                    $invoiceData['eventId']         = $invoice['invoice_id'];
                    $invoiceData['eventUrl']        = route('calender.event.date');
                    $dueInvoices[]                  = $invoiceData;
                }
            }
        }

        $calenderArray = array_merge($taskArray, $bugArray, $dueInvoices);
        $calenderData  = str_replace(']"', ']', str_replace('"[', "[", json_encode($calenderArray)));

        return view('calender.index', compact('calenderData'));
    }

    public function dropEventDate(Request $request)
    {
        if($request->type == 'task')
        {
            $task             = Task::find($request->eventId);
            $task->start_date = $request->start;
            $task->due_date   = $request->end;
            $task->save();
        }
        else if($request->type == 'bug')
        {
            $bug             = Bug::find($request->eventId);
            $bug->start_date = $request->start;
            $bug->due_date   = $request->end;
            $bug->save();
        }
        else
        {
            $invoice = Invoice::where('invoice_id', $request->eventId)->first();

            $invoice->due_date = $request->end;
            $invoice->save();
        }
    }
}
