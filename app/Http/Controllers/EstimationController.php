<?php

namespace App\Http\Controllers;

use App\Estimation;
use App\EstimationProduct;
use App\Tax;
use App\User;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstimationController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->can('manage estimations'))
        {
            if(Auth::user()->type == 'client')
            {

                $estimations = Estimation::where('client_id', '=', Auth::user()->id)->where('created_by', '=', Auth::user()->creatorId())->get();
                $curr_month  = Estimation::where('client_id', '=', Auth::user()->id)->where('created_by', '=', Auth::user()->creatorId())->whereMonth('issue_date', '=', date('m'))->get();
                $curr_week   = Estimation::where('client_id', '=', Auth::user()->id)->where('created_by', '=', Auth::user()->creatorId())->whereBetween(
                    'issue_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Estimation::where('client_id', '=', Auth::user()->id)->where('created_by', '=', Auth::user()->creatorId())->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }
            else
            {
                $estimations = Estimation::where('created_by', '=', Auth::user()->creatorId())->get();
                $curr_month  = Estimation::where('created_by', '=', Auth::user()->creatorId())->whereMonth('issue_date', '=', date('m'))->get();
                $curr_week   = Estimation::where('created_by', '=', Auth::user()->creatorId())->whereBetween(
                    'issue_date', [
                                    \Carbon\Carbon::now()->startOfWeek(),
                                    \Carbon\Carbon::now()->endOfWeek(),
                                ]
                )->get();
                $last_30days = Estimation::where('created_by', '=', Auth::user()->creatorId())->whereDate('issue_date', '>', \Carbon\Carbon::now()->subDays(30))->get();
            }

            // Estimation Summary
            $cnt_estimation                = [];
            $cnt_estimation['total']       = Estimation::getEstimationSummary($estimations);
            $cnt_estimation['this_month']  = Estimation::getEstimationSummary($curr_month);
            $cnt_estimation['this_week']   = Estimation::getEstimationSummary($curr_week);
            $cnt_estimation['last_30days'] = Estimation::getEstimationSummary($last_30days);

            return view('estimations.index', compact('estimations', 'cnt_estimation'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->can('create estimation'))
        {
            $taxes  = Tax::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $client = User::where('type', '=', 'Client')->where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('estimations.create', compact('client', 'taxes'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usr = Auth::user();

        if($usr->can('create estimation'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'client_id' => 'required',
                                   'issue_date' => 'required',
                                   'tax_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('estimations.index')->with('error', $messages->first());
            }

            $estimation                = new Estimation();
            $estimation->estimation_id = $this->estimationNumber();
            $estimation->client_id     = $request->client_id;
            $estimation->status        = 0;
            $estimation->issue_date    = $request->issue_date;
            $estimation->discount      = 0;
            $estimation->tax_id        = $request->tax_id;
            $estimation->terms         = $request->terms;
            $estimation->created_by    = Auth::user()->creatorId();
            $estimation->save();

            $estimationArr = [
                'estimation_id' => $estimation->id,
                'estimation_name' => Utility::estimateNumberFormat($estimation->estimation_id),
                'updated_by' => $usr->id,
            ];

            $client = User::find($estimation->client_id);

            $eArr = [
                'estimation_name' => Utility::estimateNumberFormat($estimation->estimation_id),
                'estimation_client' => ucfirst($client->name),
                'estimation_status' => Estimation::$statues[$estimation->status],
            ];

            Utility::sendNotification('assign_estimation', $request->client_id, $estimationArr);
            $resp = Utility::sendEmailTemplate('Assign Estimation', $request->client_id, $eArr);

            return redirect()->route('estimations.show', $estimation->id)->with('success', __('Estimation successfully created!') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function estimationNumber()
    {
        $latest = Estimation::where('created_by', '=', Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->estimation_id + 1;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Estimation $estimation)
    {
        $usr = Auth::user();
        if($usr->can('view estimation'))
        {
            if($estimation->created_by == $usr->creatorId())
            {
                if($usr->type != 'super admin' && $estimation->client_id != $usr->id)
                {
                    return redirect()->back()->with('error', __('Permission Denied.'));
                }

                $settings = Utility::settings();
                $client   = $estimation->client;

                return view('estimations.show', compact('estimation', 'settings', 'client'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Estimation $estimation)
    {
        if(Auth::user()->can('edit estimation'))
        {
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $taxes  = Tax::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $client = User::where('type', '=', 'Client')->where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('estimations.edit', compact('estimation', 'client', 'taxes'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estimation $estimation)
    {
        if(Auth::user()->can('edit estimation'))
        {
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'client_id' => 'required',
                                       'status' => 'required',
                                       'issue_date' => 'required',
                                       'tax_id' => 'required',
                                       'discount' => 'required|min:0',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('estimations.index')->with('error', $messages->first());
                }

                $estimation->client_id  = $request->client_id;
                $estimation->status     = $request->status;
                $estimation->issue_date = $request->issue_date;
                $estimation->tax_id     = $request->tax_id;
                $estimation->terms      = $request->terms;
                $estimation->discount   = $request->discount;
                $estimation->save();

                return redirect()->back()->with('success', __('Estimation successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Estimation $estimation
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estimation $estimation)
    {
        if(Auth::user()->can('delete estimation'))
        {
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $estimation->delete();

                return redirect()->route('estimations.index')->with('success', __('Estimation successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productAdd($id)
    {
        if(Auth::user()->can('estimation add product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == Auth::user()->creatorId())
            {
                return view('estimations.products', compact('estimation'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productStore($id, Request $request)
    {
        if(Auth::user()->can('estimation add product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'price' => 'required|numeric|min:1',
                                       'quantity' => 'required|numeric|min:1',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('estimations.show', $estimation->id)->with('error', $messages->first());
                }

                EstimationProduct::create(
                    [
                        'estimation_id' => $estimation->id,
                        'name' => $request->name,
                        'price' => $request->price,
                        'quantity' => $request->quantity,
                        'description' => $request->description,
                    ]
                );

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Item successfully added!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productEdit($id, $product_id)
    {
        if(Auth::user()->can('estimation edit product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $product = EstimationProduct::find($product_id);

                return view('estimations.products', compact('estimation', 'product'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productUpdate($id, $product_id, Request $request)
    {
        if(Auth::user()->can('estimation edit product'))
        {
            $estimation = Estimation::find($id);
            if($estimation->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'price' => 'required|numeric|min:1',
                                       'quantity' => 'required|numeric|min:1',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('estimations.show', $estimation->id)->with('error', $messages->first());
                }

                $estimationProduct              = EstimationProduct::find($product_id);
                $estimationProduct->name        = $request->name;
                $estimationProduct->price       = $request->price;
                $estimationProduct->quantity    = $request->quantity;
                $estimationProduct->description = $request->description;
                $estimationProduct->save();

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Item successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function productDelete($id, $product_id)
    {
        if(Auth::user()->can('estimation edit product'))
        {
            $estimation = Estimation::find($id);

            if($estimation->created_by == Auth::user()->creatorId())
            {
                $estimationProduct = EstimationProduct::find($product_id);
                $estimationProduct->delete();

                return redirect()->route('estimations.show', $estimation->id)->with('success', __('Item successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function printEstimation($id)
    {
        if(Auth::user()->can('manage estimations'))
        {
            $estimation = Estimation::findOrFail($id);
            $settings   = Utility::settings();
            $client     = User::where('id', '=', $estimation->client_id)->where('type', '=', 'Client')->first();

            //Set your logo
            $logo            = asset(\Storage::url('logo/'));
            $company_logo    = Utility::getValByName('company_logo');
            $estimation_logo = Utility::getValByName('estimation_logo');
            if(isset($estimation_logo) && !empty($estimation_logo))
            {
                $img = asset(\Storage::url('estimation_logo/') . $estimation_logo);
            }
            else
            {
                $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
            }

            if($estimation)
            {
                $color      = '#' . $settings['estimation_color'];
                $font_color = Utility::getFontColor($color);

                return view('estimations.templates.' . $settings['estimation_template'], compact('estimation', 'color', 'settings', 'client', 'img', 'font_color'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function previewEstimation($template, $color)
    {
        $settings   = Utility::settings();
        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $estimation    = new Estimation();
        $client        = new \stdClass();
        $tax           = new \stdClass();
        $client->name  = 'Client';
        $client->email = 'client@example.com';
        $tax->name     = 'GST';
        $tax->rate     = 10;

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item       = new \stdClass();
            $item->name = 'Product ' . $i;;
            $item->price    = 100;
            $item->quantity = $i;
            $items[]        = $item;
        }

        $estimation->estimation_id = 1;
        $estimation->issue_date    = date('Y-m-d H:i:s');
        $estimation->discount      = 50;
        $estimation->getProducts   = $items;
        $estimation->tax           = $tax;

        //Set your logo
        $logo            = asset(\Storage::url('logo/'));
        $company_logo    = Utility::getValByName('company_logo');
        $estimation_logo = Utility::getValByName('estimation_logo');
        if(isset($estimation_logo) && !empty($estimation_logo))
        {
            $img = asset(\Storage::url('estimation_logo/') . $estimation_logo);
        }
        else
        {
            $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        }

        return view('estimations.templates.' . $template, compact('estimation', 'preview', 'color', 'settings', 'client', 'img', 'font_color'));
    }
}
