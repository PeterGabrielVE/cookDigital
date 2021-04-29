<?php

namespace App\Http\Controllers;

use App\Projects;
use App\User;
use App\Utility;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::check())
        {
            $total_lead = Auth::user()->total_lead();

            $last_leadstage = Auth::user()->last_leadstage();
            $complete_leads = Auth::user()->total_complete_lead((isset($last_leadstage->order) ? $last_leadstage->order : 0));
            $complete_lead  = (!empty($complete_leads) ? $complete_leads : 0);

            $lead_percentage         = ($total_lead != 0 ? intval(($complete_lead / $total_lead) * 100) : 0);
            $lead['total_lead']      = $total_lead;
            $lead['lead_percentage'] = $lead_percentage;

            if(Auth::user()->type == 'company')
            {
                $project['projects'] = Projects::where('created_by', '=', Auth::user()->creatorId())->where('due_date', '>', date('Y-m-d'))->limit(5)->orderBy('due_date')->get();
            }
            elseif(Auth::user()->type == 'client')
            {
                $project['projects']       = Projects::where('client', '=', Auth::user()->authId())->where('due_date', '>', date('Y-m-d'))->limit(5)->orderBy('due_date')->get();
                $project['project_budget'] = Projects::where('client', Auth::user()->id)->sum('price');

            }
            else
            {
                $project['projects'] = Projects::select('projects.*', 'userprojects.id as up_id')->join('userprojects', 'userprojects.project_id', '=', 'projects.id')->where('userprojects.user_id', '=', Auth::user()->authId())->where('due_date', '>', date('Y-m-d'))->limit(5)->orderBy('due_date')->get();
            }

            $project_last_stages      = Auth::user()->last_projectstage();
            $project_last_stage       = (!empty($project_last_stages) ? $project_last_stages->id : 0);
            $project['total_project'] = Auth::user()->user_project();
            $total_project_task       = Auth::user()->created_total_project_task();
            $complete_task            = Auth::user()->project_complete_task($project_last_stage);


            $project['project_percentage'] = ($total_project_task != 0) ? intval(($complete_task / $total_project_task) * 100) : 0;

            $invoice         = [];
            $top_due_invoice = [];
            if(Auth::user()->type == 'client' || Auth::user()->type == 'company')
            {

                $total_invoices           = $top_due_invoice = Auth::user()->created_total_invoice();
                $invoice['total_invoice'] = count($total_invoices);
                $complete_invoice         = 0;
                $total_due_amount         = 0;
                $top_due_invoice          = array();
                $pay_amount               = 0;
                foreach($total_invoices as $total_invoice)
                {
                    $amount   = $total_due = $total_invoice->getDue();
                    $payments = $total_invoice->payments;


                    foreach($payments as $payment)
                    {
                        $pay_amount += $payment->amount;
                    }

                    $total_due_amount += $total_due;
                    if($amount == 0.00)
                    {
                        $complete_invoice++;
                    }
                    if($amount > 0)
                    {
                        $total_invoice['due_amount'] = $amount;
                        $top_due_invoice[]           = $total_invoice;
                    }
                }
                if(count($total_invoices) > 0)
                {
                    $invoice['invoice_percentage'] = intval(($complete_invoice / count($total_invoices)) * 100);
                }
                else
                {
                    $invoice['invoice_percentage'] = 0;
                }

                $top_due_invoice = array_slice($top_due_invoice, 0, 5);
            }

            if(Auth::user()->type == 'client')
            {
                if(!empty($project['project_budget']))
                {
                    $project['client_project_budget_due_per'] = intval(($pay_amount / $project['project_budget']) * 100);
                }
                else
                {
                    $project['client_project_budget_due_per'] = 0;
                }

            }

            $top_tasks       = Auth::user()->created_top_due_task();
            $users['staff']  = User::where('created_by', '=', Auth::user()->creatorId())->count();
            $users['user']   = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '!=', 'client')->count();
            $users['client'] = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'client')->count();
            $project_status  = array_values(Projects::$project_status);
            $projectData     = \App\Projects::getProjectStatus();
            $taskData        = \App\Projectstages::getChartData();

            return view('dashboard.index', compact('lead', 'project', 'invoice', 'top_tasks', 'top_due_invoice', 'users', 'project_status', 'projectData', 'taskData'));
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                if(Utility::getValByName('enable_landing') == 'yes')
                {
                    return view('layouts.landing');
                }
                else
                {
                    return redirect()->route('login');
                }
            }
        }
    }
}

