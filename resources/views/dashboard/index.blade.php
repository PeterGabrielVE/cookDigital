@extends('layouts.admin')
@section('page-title')
    {{__('Dashboard')}}
@endsection
@section('action-button')

    @if(Auth::user()->type =='Administración')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a {{ (Request::segment(1) == 'projects')?'active open':''}}" href="{{ route('projects.index') }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-tasks"></i></a>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{ route('get.projects.by.user',Auth::user()->id) }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only btn-success width-auto" style="background-color: green"><i class="fas fa-tasks"></i></a>
            </div>
        </div>
    @else
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a {{ (Request::segment(1) == 'projects')?'active open':''}}" href="{{ route('projects.index') }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-tasks"></i></a>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{ route('get.all.projects') }}" title="{{__('View All Projects')}}" class="btn btn-xs btn-white btn-icon-only btn-success width-auto" style="background-color: green"><i class="fas fa-tasks"></i></a>
            </div>
        </div>
    @endif
@endsection
@push('script-page')
    <script>
        var SalesChart = (function () {
            var $chart = $('#chart-sales');

            function init($this) {
                var salesChart = new Chart($this, {
                    type: 'line',
                    options: {
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    color: Charts.colors.gray[200],
                                    zeroLineColor: Charts.colors.gray[200]
                                },
                            }]
                        }
                    },
                    data: {
                        labels: {!! json_encode($taskData['label']) !!},
                        datasets: {!! json_encode($taskData['dataset']) !!}
                    }
                });
                $this.data('chart', salesChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();
        var DoughnutChart = (function () {
            var $chart = $('#chart-doughnut');

            function init($this) {
                var randomScalingFactor = function () {
                    return Math.round(Math.random() * 100);
                };
                var doughnutChart = new Chart($this, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($project_status) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($projectData)) !!},
                            backgroundColor: ["#40c5d2", "#f36a5b", "#67b7dc"],
                            // label: 'Dataset 1'
                        }],
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });

                $this.data('chart', doughnutChart);

            };
            if ($chart.length) {
                init($chart);
            }
        })();
    </script>
@endpush
@section('content')
    @php
        $lead_percentage = $lead['lead_percentage'];
        $project_percentage = $project['project_percentage'];
        $client_project_budget_due_per = @$project['client_project_budget_due_per'];
        $invoice_percentage = @$invoice['invoice_percentage'];

        $label='';
        if(($lead_percentage<=15)){
            $label='bg-danger';
        }else if ($lead_percentage > 15 && $lead_percentage <= 33) {
            $label='bg-warning';
        } else if ($lead_percentage > 33 && $lead_percentage <= 70) {
            $label='bg-primary';
        } else {
            $label='bg-success';
        }

         $label1='';
        if($project_percentage<=15){
            $label1='bg-danger';
        }else if ($project_percentage > 15 && $project_percentage <= 33) {
            $label1='bg-warning';
        } else if ($project_percentage > 33 && $project_percentage <= 70) {
            $label1='bg-primary';
        } else {
            $label1='bg-success';
        }

        $label2='';
        if($invoice_percentage<=15){
            $label2='bg-danger';
        }else if ($invoice_percentage > 15 && $invoice_percentage <= 33) {
            $label2='bg-warning';
        } else if ($invoice_percentage > 33 && $invoice_percentage <= 70) {
            $label2='bg-primary';
        } else {
            $label2='bg-success';
        }

         $label3='';
        if($client_project_budget_due_per<=15){
            $label3='bg-danger';
        }else if ($client_project_budget_due_per > 15 && $client_project_budget_due_per <= 33) {
            $label3='bg-warning';
        } else if ($client_project_budget_due_per > 33 && $client_project_budget_due_per <= 70) {
            $label3='bg-primary';
        } else {
            $label3='bg-success';
        }
    @endphp

    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card card-box height-95">
                <div class="icon-box {{$label}}">{{($lead['total_lead'] > 100) ? '99+' : $lead['total_lead']}}</div>
                <div class="number-icon">
                    <div class="card-right-title">
                        <h4 class="float-left">{{__('Total Lead')}}</h4>
                        <h5 class="float-right">{{$lead_percentage}}%</h5>
                    </div>
                    <div class="border-progress">
                        <div class="border-inner-progress {{$label}}" style="width:{{$lead_percentage}}%"></div>
                    </div>
                </div>
                <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card card-box height-95">
                <div class="icon-box {{$label1}}">{{$project['total_project']}}</div>
                <div class="number-icon">
                    <div class="card-right-title">
                        <h4 class="float-left">{{__('Total Project')}}</h4>
                        <h5 class="float-right">{{$project_percentage}}%</h5>
                    </div>
                    <div class="border-progress">
                        <div class="border-inner-progress {{$label1}}" style="width:{{$project_percentage}}%"></div>
                    </div>
                </div>
                <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
            </div>
        </div>
        @if(Auth::user()->type =='company' || Auth::user()->type =='client')
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                <div class="card card-box height-95">
                    <div class="icon-box {{$label2}}">{{($invoice['total_invoice'] > 100 ? '99+' : $invoice['total_invoice'])}}</div>
                    <div class="number-icon">
                        <div class="card-right-title">
                            <h4 class="float-left">{{__('Total Invoice')}}</h4>
                            <h5 class="float-right">{{$invoice_percentage}}%</h5>
                        </div>
                        <div class="border-progress">
                            <div class="border-inner-progress {{$label2}}" style="width:{{$invoice_percentage}}%"></div>
                        </div>
                    </div>
                    <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
                </div>
            </div>
        @endif
        @if(Auth::user()->type =='company')
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                <div class="card card-box height-95">
                    <div class="icon-box">{{($users['staff'] > 100) ? '99+' : $users['staff']}}</div>
                    <div class="number-icon">
                        <div class="card-right-title pt-2">
                            <h4 class="float-left">{{__('Total Staff')}}</h4>
                        </div>
                    </div>
                    <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
                </div>
            </div>
        @endif
        @if(Auth::user()->type =='client')
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                <div class="card card-box height-95">
                    <div class="number-icon w-100">
                        <div class="card-right-title">
                            <h4 class="float-left"><span>{{ Auth::user()->priceFormat($project['project_budget']) }}</span> <br> {{__('Total Project Budget')}}</h4>
                            <h5 class="float-right">{{$client_project_budget_due_per}}%</h5>
                        </div>
                        <div class="border-progress">
                            <div class="border-inner-progress {{$label3}}" style="width:{{$client_project_budget_due_per}}%"></div>
                        </div>
                    </div>
                    <img src="{{ asset('assets/img/dot-icon.png') }}" alt="" class="dotted-icon">
                </div>
            </div>
        @endif

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div>
                <h4 class="h4 font-weight-400 float-left">{{__('Tasks Overview')}}</h4>
                <h6 class="last-day-text">{{__('Last 7 Days')}}</h6>
            </div>
            <div class="card bg-none">
                <canvas id="chart-sales" height="300" class="p-3"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        @if(\Auth::user()->type != 'super admin')
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div>
                    <h4 class="h4 font-weight-400 float-left">{{__('Project Status')}}</h4>
                </div>
                <div class="card bg-none py-4">
                    <div class="chart">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="chart-doughnut" class="chart-canvas chartjs-render-monitor" width="734" height="350" style="display: block; width: 734px; height: 350px;"></canvas>
                    </div>
                    <div class="project-details" style="margin-top: 15px;">
                        <div class="row">
                            <div class="col text-center">
                                <div class="tx-gray-500 small">{{__('On Going')}}</div>
                                <div class="font-weight-bold">{{ number_format($projectData['on_going'],2) }} %</div>
                            </div>
                            <div class="col text-center">
                                <div class="tx-gray-500 small">{{__('On Hold')}}</div>
                                <div class="font-weight-bold">{{ number_format($projectData['on_hold'],2) }} %</div>
                            </div>
                            <div class="col text-center">
                                <div class="tx-gray-500 small">{{__('Completed')}}</div>
                                <div class="font-weight-bold">{{ number_format($projectData['completed'],2) }} %</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->type =='company' || Auth::user()->type =='client')
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div>
                    <h4 class="h4 font-weight-400 float-left">{{__('Top Due Payment')}}</h4>
                </div>
                <div class="card bg-none min-410 mx-410">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th>{{__('Invoice ID')}}</th>
                                <th>{{__('Due Amount')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            @forelse($top_due_invoice as $invoice)
                                <tr>
                                    <td class="Id">
                                        <a href="{{route('invoices.show',$invoice->id)}}">{{ Utility::invoiceNumberFormat($invoice->id) }}</a></td>
                                    <td>{{Auth::user()->priceFormat($invoice->getDue()) }}</td>
                                    <td>{{ Auth::user()->dateFormat($invoice->due_date) }}</td>
                                    <td>
                                        <a href="{{route('invoices.show',$invoice->id)}}" class="edit-icon"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="4">{{__('No Data Found.!')}}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="{{ (Auth::user()->type =='company' || Auth::user()->type =='client') ? 'col-xl-6 col-lg-6 col-md-6' : 'col-xl-8 col-lg-8 col-md-8' }} col-sm-12">
            <div>
                <h4 class="h4 font-weight-400 float-left">{{__('Top Due Project')}}</h4>
            </div>
            <div class="card bg-none min-410 mx-410">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                        <tr>
                            <th>{{__('Task Name')}}</th>
                            <th>{{__('Remain Task')}}</th>
                            <th>{{__('Due Date')}}</th>
                            <th>{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @forelse($project['projects'] as $project)
                            @php
                                $datetime1 = new DateTime($project->due_date);
                                $datetime2 = new DateTime(date('Y-m-d'));
                                $interval = $datetime1->diff($datetime2);
                                $days = $interval->format('%a');

                                 $project_last_stage = ($project->project_last_stage($project->id))?$project->project_last_stage($project->id)->id:'';
                                $total_task = $project->project_total_task($project->id);
                                $completed_task=$project->project_complete_task($project->id,$project_last_stage);
                                $remain_task=$total_task-$completed_task;
                            @endphp
                            <tr>
                                <td class="id-web">
                                    {{$project->name}}
                                </td>
                                <td>{{$remain_task }}</td>
                                <td>{{ Auth::user()->dateFormat($project->due_date) }}</td>
                                <td>
                                    <a href="{{ route('projects.show',$project->id) }}" class="edit-icon"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="4">{{__('No Data Found.!')}}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div>
                <h4 class="h4 font-weight-400 float-left">{{__('Top Due Task')}}</h4>
            </div>
            <div class="card bg-none min-410 mx-410">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                        <tr>
                            <th>{{__('Task Name')}}</th>
                            <th>{{__('Assign To')}}</th>
                            <th>{{__('Status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($top_tasks as $top_task)
                            <tr>
                                <td class="id-web">
                                    {{$top_task->title}}
                                </td>
                                <td>
                                    @if(\Auth::user()->type != 'client' && \Auth::user()->type != 'company')
                                        {{$top_task->project_name}}
                                    @else
                                        {{$top_task->task_user->name}}
                                    @endif
                                </td>
                                <td><span class="badge badge-pill blue-bg">{{ $top_task->stage_name }}</span></td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="4">{{__('No Data Found.!')}}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
