<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\Bug;
use App\BugComment;
use App\BugFile;
use App\BugStatus;
use App\CheckList;
use App\ClientPermission;
use App\Comment;
use App\Invoice;
use App\Labels;
use App\Leads;
use App\Milestone;
use App\ProjectFile;
use App\Projects;
use App\Projectstages;
use App\Task;
use App\TaskFile;
use App\Timesheet;
use App\User;
use App\Userprojects;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage project'))
        {
            $user = \Auth::user();
            if($user->type == 'client')
            {
                $projects = Projects::where('client', '=', $user->id)->get();
            }
            else
            {
                $projects = $user->projects;

            }

            $project_status = Projects::$project_status;

            return view('projects.index', compact('projects', 'project_status'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create project'))
        {
            $users   = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->get()->pluck('name', 'id');
            $clients = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'client')->get()->pluck('name', 'id');
            $labels  = Labels::where('created_by', '=', \Auth::user()->creatorId())->get();
            $leads   = Leads::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $clients->prepend('Select Client', '');
            $users->prepend('Select User', '');
            $leads->prepend('Select Lead', '');

            return view('projects.create', compact('clients', 'labels', 'users', 'leads'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create project'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'price' => 'required',
                                   'start_date' => 'required',
                                   'due_date' => 'required',
                                   'label' => 'required',
                                   'user' => 'required',
                                   'lead' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('projects.index')->with('error', $messages->first());
            }

            $objUser = \Auth::user()->creatorId();
            $objUser = User::find($objUser);

            $total_client         = $objUser->countProject();
            $project              = new Projects();
            $project->name        = $request->name;
            $project->price       = $request->price;
            $project->start_date  = $request->start_date;
            $project->due_date    = $request->due_date;
            $project->client      = $request->client;
            $project->label       = $request->label;
            $project->description = $request->description;
            $project->lead        = $request->lead;
            $project->created_by  = \Auth::user()->creatorId();
            $project->save();

            $userproject             = new Userprojects();
            $userproject->user_id    = \Auth::user()->creatorId();
            $userproject->project_id = $project->id;
            $userproject->save();

            $project    = Projects::find($project->id);
            $projectArr = [
                'project_id' => $project->id,
                'name' => $project->name,
                'updated_by' => \Auth::user()->id,
            ];

            $pArr = [
                'project_name' => $project->name,
                'project_label' => $project->label()->name,
                'project_status' => Projects::$project_status[$project->status],
            ];

            foreach($request->user as $key => $user)
            {
                $userproject             = new Userprojects();
                $userproject->user_id    = $user;
                $userproject->project_id = $project->id;
                $userproject->save();

                Utility::sendNotification('assign_project', $user, $projectArr);
                Utility::sendEmailTemplate('Assign Project', $user, $pArr);
            }

            //for client
            Utility::sendNotification('assign_project', $request->client, $projectArr);
            $resp = Utility::sendEmailTemplate('Assign Project', $request->client, $pArr);

            $permissions = Projects::$permission;
            ClientPermission::create(
                [
                    'client_id' => $project->client,
                    'project_id' => $project->id,
                    'permissions' => implode(',', $permissions),
                ]
            );

            return redirect()->route('projects.index')->with('success', __('Project successfully created.') . (($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {

        if(\Auth::user()->can('edit project'))
        {
            $clients = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'client')->get()->pluck('name', 'id');
            $labels  = Labels::where('created_by', '=', \Auth::user()->creatorId())->get();
            $leads   = Leads::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $project = Projects::findOrfail($id);
            if($project->created_by == \Auth::user()->creatorId())
            {
                return view('projects.edit', compact('project', 'clients', 'labels', 'leads'));
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

    public function show($project_id)
    {
        $usr = Auth::user();

        if($usr->can('show project'))
        {
            $project = Projects::where('id', $project_id)->first();
            if(!empty($project) && $project->created_by == $usr->creatorId())
            {
                if($usr->type != 'company')
                {
                    $arrProjectUsers = $project->project_user()->pluck('user_id')->toArray();
                    array_push($arrProjectUsers, $project->client);

                    if(!in_array($usr->id, $arrProjectUsers))
                    {
                        return redirect()->back()->with('error', __('Permission denied.'));
                    }
                }

                $project_status = Projects::$project_status;
                $permissions    = $project->client_project_permission();
                $perArr         = (!empty($permissions) ? explode(',', $permissions->permissions) : []);

                return view('projects.show', compact('project', 'project_status', 'perArr'));
            }
            else
            {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit project'))
        {
            $project = Projects::findOrfail($id);
            if($project->created_by == \Auth::user()->creatorId())
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required|max:20',
                                       'price' => 'required',
                                       'date' => 'required',
                                       'label' => 'required',
                                       'lead' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $project->name        = $request->name;
                $project->price       = $request->price;
                $project->due_date    = $request->date;
                $project->client      = $request->client;
                $project->label       = $request->label;
                $project->lead        = $request->lead;
                $project->description = $request->description;
                $project->save();

                ClientPermission::where('client_id', '=', $project->client)->where('project_id', '=', $project->id)->delete();
                $permissions = Projects::$permission;
                ClientPermission::create(
                    [
                        'client_id' => $project->client,
                        'project_id' => $project->id,
                        'permissions' => implode(',', $permissions),
                    ]
                );

                return redirect()->route('projects.index')->with('success', __('Project successfully updated.'));
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

    public function updateStatus(Request $request, $id)
    {
        if(\Auth::user()->can('edit project'))
        {
            $project = Projects::findOrfail($id);
            if($project->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'status' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', 'Project Status is required.');
                }

                $project->status = $request->status;
                $project->save();

                return redirect()->back()->with('success', __('Status Updated!'));
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

    public function destroy($id)
    {
        if(\Auth::user()->can('delete project'))
        {
            $project = Projects::findOrfail($id);
            if($project->created_by == \Auth::user()->creatorId())
            {
                //                $project->delete();
                Milestone::where('project_id', $id)->delete();
                Userprojects::where('project_id', $id)->delete();
                ActivityLog::where('project_id', $id)->delete();

                $projectFile = ProjectFile::select('file_path')->where('project_id', $id)->get()->map(
                    function ($file){
                        $dir        = storage_path('project_files/');
                        $file->file = $dir . $file->file;

                        return $file;
                    }
                );
                if(!empty($projectFile))
                {
                    foreach($projectFile->pluck('file_path') as $file)
                    {
                        File::delete($file);
                    }
                }
                ProjectFile::where('project_id', $id)->delete();

                Invoice::where('project_id', $id)->update(array('project_id' => 0));
                $tasks     = Task::select('id')->where('project_id', $id)->get()->pluck('id');
                $comment   = Comment::whereIn('task_id', $tasks)->delete();
                $checklist = CheckList::whereIn('task_id', $tasks)->delete();

                $taskFile = TaskFile::select('file')->whereIn('task_id', $tasks)->get()->map(
                    function ($file){
                        $dir        = storage_path('tasks/');
                        $file->file = $dir . $file->file;

                        return $file;
                    }
                );
                if(!empty($taskFile))
                {
                    foreach($taskFile->pluck('file') as $file)
                    {
                        File::delete($file);
                    }
                }
                TaskFile::whereIn('task_id', $tasks)->delete();

                Task::where('project_id', $id)->delete();

                return redirect()->route('projects.index')->with('success', __('Project successfully deleted.'));
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

    public function userInvite($project_id)
    {
        $assign_user = Userprojects::select('user_id')->where('project_id', $project_id)->get()->pluck('user_id');
        $employee    = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->whereNotIn('id', $assign_user)->get()->pluck('name', 'id');

        return view('projects.invite', compact('employee', 'project_id'));
    }

    public function Invite(Request $request, $project_id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'user' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('projects.show', $project_id)->with('error', $messages->first());
        }

        $project = Projects::find($project_id);

        $projectArr = [
            'project_id' => $project_id,
            'name' => $project->name,
            'updated_by' => \Auth::user()->id,
        ];

        foreach($request->user as $key => $user)
        {
            $userproject             = new Userprojects();
            $userproject->user_id    = $user;
            $userproject->project_id = $project_id;
            $userproject->save();

            Utility::sendNotification('assign_project', $user, $projectArr);
        }


        return redirect()->route('projects.show', $project_id)->with('success', __('User successfully Invited.'));
    }

    public function removeUser($id, $user_id)
    {
        if(Auth::user()->can('invite user project'))
        {
            $project = Projects::find($id);
            if($project->created_by == Auth::user()->creatorId())
            {
                Userprojects::where('project_id', '=', $id)->where('user_id', '=', $user_id)->delete();

                return redirect()->back()->with('success', __('User successfully removed!'));
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

    public function milestone($project_id)
    {
        $project = Projects::find($project_id);
        $status  = Projects::$status;

        return view('projects.milestone', compact('project', 'status'));
    }

    public function milestoneStore(Request $request, $project_id)
    {
        $usr         = \Auth::user();
        $project     = Projects::find($project_id);
        $userProject = Userprojects::where('project_id', '=', $project_id)->pluck('user_id')->toArray();

        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
                'cost' => 'required',
            ]
        );

        $milestone              = new Milestone();
        $milestone->project_id  = $project->id;
        $milestone->title       = $request->title;
        $milestone->status      = $request->status;
        $milestone->cost        = $request->cost;
        $milestone->description = $request->description;
        $milestone->save();

        ActivityLog::create(
            [
                'user_id' => \Auth::user()->creatorId(),
                'project_id' => $project->id,
                'log_type' => 'Create Milestone',
                'remark' => json_encode(['title' => $milestone->title]),
            ]
        );

        $projectArr = [
            'project_id' => $project->id,
            'name' => $project->name,
            'updated_by' => $usr->id,
        ];

        foreach(array_merge($userProject, [$project->client]) as $u)
        {
            Utility::sendNotification('create_milestone', $u, $projectArr);
        }

        return redirect()->back()->with('success', __('Milestone successfully created.'));
    }

    public function milestoneEdit($id)
    {
        $milestone = Milestone::find($id);
        $status    = Projects::$status;

        return view('projects.milestoneEdit', compact('milestone', 'status'));
    }

    public function milestoneUpdate($id, Request $request)
    {


        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
                'cost' => 'required',
            ]
        );

        $milestone              = Milestone::find($id);
        $milestone->title       = $request->title;
        $milestone->status      = $request->status;
        $milestone->cost        = $request->cost;
        $milestone->description = $request->description;
        $milestone->save();

        return redirect()->back()->with('success', __('Milestone updated successfully.'));
    }

    public function milestoneDestroy($id)
    {


        $milestone = Milestone::find($id);
        $milestone->delete();

        return redirect()->back()->with('success', __('Milestone successfully deleted.'));
    }

    public function milestoneShow($id)
    {
        $milestone = Milestone::find($id);

        return view('projects.milestoneShow', compact('milestone'));
    }

    public function fileUpload($id, Request $request)
    {
        $project     = Projects::find($id);
        $userProject = Userprojects::where('project_id', '=', $project->id)->pluck('user_id')->toArray();
        $request->validate(['file' => 'required|mimes:png,jpeg,jpg,pdf,doc,txt|max:20480']);

        $file_name = $request->file->getClientOriginalName();
        $file_path = $project->id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
        $request->file->storeAs('project_files', $file_path);

        $file                 = ProjectFile::create(
            [
                'project_id' => $project->id,
                'file_name' => $file_name,
                'file_path' => $file_path,
            ]
        );
        $return               = [];
        $return['is_success'] = true;
        $return['download']   = route(
            'projects.file.download', [
                                        $project->id,
                                        $file->id,
                                    ]
        );
        $return['delete']     = route(
            'projects.file.delete', [
                                      $project->id,
                                      $file->id,
                                  ]
        );

        ActivityLog::create(
            [
                'user_id' => \Auth::user()->creatorId(),
                'project_id' => $project->id,
                'log_type' => 'Upload File',
                'remark' => json_encode(['file_name' => $file_name]),
            ]
        );

        $projectArr = [
            'project_id' => $project->id,
            'name' => $project->name,
            'updated_by' => \Auth::user()->id,
        ];

        foreach(array_merge($userProject, [$project->client]) as $u)
        {
            Utility::sendNotification('upload_file', $u, $projectArr);
        }

        return response()->json($return);
    }

    public function fileDownload($id, $file_id)
    {

        $project = Projects::find($id);
        $file    = ProjectFile::find($file_id);
        if($file)
        {
            $file_path = storage_path('project_files/' . $file->file_path);
            $filename  = $file->file_name;

            return \Response::download(
                $file_path, $filename, [
                              'Content-Length: ' . filesize($file_path),
                          ]
            );
        }
        else
        {
            return redirect()->back()->with('error', __('File is not exist.'));
        }
    }

    public function fileDelete($id, $file_id)
    {


        $project = Projects::find($id);

        $file = ProjectFile::find($file_id);
        if($file)
        {
            $path = storage_path('project_files/' . $file->file_path);
            if(file_exists($path))
            {
                \File::delete($path);
            }
            $file->delete();

            return response()->json(['is_success' => true], 200);
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('File is not exist.'),
                ], 200
            );
        }
    }

    public function taskBoard($project_id)
    {
        $user    = Auth::user();
        $project = Projects::where('id', $project_id)->first();

        if(!empty($project) && $project->created_by == Auth::user()->creatorId())
        {
            if($user->type != 'company')
            {
                $arrProjectUsers = $project->project_user()->pluck('user_id')->toArray();
                array_push($arrProjectUsers, $project->client);

                if(!in_array($user->id, $arrProjectUsers))
                {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            }

            $stages      = Projectstages::where('created_by', '=', Auth::user()->creatorId())->orderBy('order', 'ASC')->get();
            $permissions = $project->client_project_permission();
            $perArr      = (!empty($permissions) ? explode(',', $permissions->permissions) : []);

            return view('projects.taskboard', compact('project', 'stages', 'perArr'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function taskCreate($project_id)
    {
        $project    = Projects::where('created_by', '=', \Auth::user()->creatorId())->where('projects.id', '=', $project_id)->first();
        $projects   = Projects::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $milestones = Milestone::where('project_id', '=', $project->id)->get()->pluck('title', 'id');
        $priority   = Projects::$priority;
        $usersArr   = Userprojects::where('project_id', '=', $project_id)->get();
        $users      = array();
        foreach($usersArr as $user)
        {
            $users[$user->project_assign_user->id] = ($user->project_assign_user->name . ' - ' . $user->project_assign_user->email);
        }

        return view('projects.taskCreate', compact('project', 'projects', 'priority', 'users', 'milestones'));
    }

    public function taskStore(Request $request, $projec_id)
    {
        if(\Auth::user()->type == 'company')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'priority' => 'required',
                                   'assign_to' => 'required',
                                   'due_date' => 'required',
                                   'start_date' => 'required',
                               ]
            );
        }
        else
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'priority' => 'required',
                                   'due_date' => 'required',
                                   'start_date' => 'required',
                               ]
            );
        }
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('leads.index')->with('error', $messages->first());
        }

        $usr         = \Auth::user();
        $userProject = Userprojects::where('project_id', '=', $projec_id)->pluck('user_id')->toArray();
        $project     = Projects::where('created_by', '=', \Auth::user()->creatorId())->where('projects.id', '=', $projec_id)->first();

        if($project)
        {
            $post = $request->all();
            if($usr->type != 'company')
            {
                $post['assign_to'] = $usr->id;
            }
            $post['project_id'] = $projec_id;
            $post['stage']      = Projectstages::where('created_by', '=', $usr->creatorId())->first()->id;
            $task               = Task::create($post);

            $task = Task::find($task->id);

            ActivityLog::create(
                [
                    'user_id' => $usr->creatorId(),
                    'project_id' => $projec_id,
                    'log_type' => 'Create Task',
                    'remark' => json_encode(['title' => $task->title]),
                ]
            );

            $projectArr = [
                'project_id' => $project->id,
                'name' => $project->name,
                'updated_by' => $usr->id,
            ];

            $pArr = [
                'project_name' => $project->name,
                'project_label' => $project->label()->name,
                'project_status' => Projects::$project_status[$project->status],
                'task_name' => $task->title,
                'task_priority' => ucfirst($task->priority),
                'task_status' => ucfirst($task->status),
            ];

            foreach(array_merge($userProject, [$project->client]) as $u)
            {
                Utility::sendNotification('create_task', $u, $projectArr);
                Utility::sendEmailTemplate('Create Task', $u, $pArr);
            }

            return redirect()->route('project.taskboard', [$projec_id])->with('success', __('Task successfully created.'));
        }
        else
        {
            return redirect()->route('project.taskboard', [$projec_id])->with('error', __('You can \'t Add Task.'));
        }
    }

    public function taskEdit($task_id)
    {
        $task       = Task::find($task_id);
        $project    = Projects::where('created_by', '=', \Auth::user()->creatorId())->where('projects.id', '=', $task->project_id)->first();
        $projects   = Projects::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $usersArr   = Userprojects::where('project_id', '=', $task->project_id)->get();
        $priority   = Projects::$priority;
        $milestones = Milestone::where('project_id', '=', $project->id)->get()->pluck('title', 'id');
        $users      = array();
        foreach($usersArr as $user)
        {
            $users[$user->project_assign_user->id] = ($user->project_assign_user->name . ' - ' . $user->project_assign_user->email);
        }

        return view('projects.taskEdit', compact('project', 'projects', 'users', 'task', 'priority', 'milestones'));
    }


    public function taskUpdate(Request $request, $task_id)
    {


        if(\Auth::user()->type == 'company')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'priority' => 'required',
                                   'assign_to' => 'required',
                                   'due_date' => 'required',
                                   'start_date' => 'required',
                                   'milestone_id' => 'required',
                               ]
            );
        }

        $task    = Task::find($task_id);
        $project = Projects::where('created_by', '=', \Auth::user()->creatorId())->where('projects.id', '=', $task->project_id)->first();
        if($project)
        {
            $post               = $request->all();
            $post['project_id'] = $task->project_id;
            $task->update($post);

            return redirect()->route(
                'project.taskboard', [$task->project_id]
            )->with('success', __('Task Updated Successfully!'));
        }
        else
        {
            return redirect()->route(
                'project.taskboard', [$task->project_id]
            )->with('error', __('You can \'t Edit Task!'));
        }
    }

    public function taskDestroy($task_id)
    {


        $task    = Task::find($task_id);
        $project = Projects::find($task->project_id);
        if($project->created_by == \Auth::user()->creatorId())
        {
            $task->delete();

            return redirect()->route(
                'project.taskboard', [$task->project_id]
            )->with('success', __('Task successfully deleted'));
        }
        else
        {
            return redirect()->route(
                'project.taskboard', [$task->project_id]
            )->with('error', __('You can\'t Delete Task.'));
        }
    }

    public function taskOrderUpdate(Request $request, $slug, $projectID)
    {
        if(isset($request->sort))
        {
            foreach($request->sort as $index => $taskID)
            {
                echo $index . "-" . $taskID;
                $task        = Task::find($taskID);
                $task->order = $index;
                $task->save();
            }
        }
        if($request->new_status != $request->old_status)
        {
            $task         = Task::find($request->id);
            $task->status = $request->new_status;
            $task->save();

            if(isset($request->client_id) && !empty($request->client_id))
            {
                $client = Client::find($request->client_id);
                $name   = $client->name . " <b>(" . __('Client') . ")</b>";
                $id     = 0;
            }
            else
            {
                $name = \Auth::user()->name;
                $id   = \Auth::user()->creatorId();
            }

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'project_id' => $projectID,
                    'log_type' => 'Move',
                    'remark' => json_encode(
                        [
                            'title' => $task->title,
                            'old_status' => ucwords($request->old_status),
                            'new_status' => ucwords($request->new_status),
                        ]
                    ),
                ]
            );

            return $task->toJson();
        }
    }

    public function order(Request $request)
    {
        $post        = $request->all();
        $task        = Task::find($post['task_id']);
        $stage       = Projectstages::find($post['stage_id']);
        $userProject = Userprojects::where('project_id', '=', $task->project_id)->pluck('user_id')->toArray();
        $project     = Projects::where('id', '=', $task->project_id)->first();

        if(!empty($stage))
        {
            $task->stage = $post['stage_id'];
            $task->save();
        }

        foreach($post['order'] as $key => $item)
        {
            $task_order        = Task::find($item);
            $task_order->order = $key;
            $task_order->stage = $post['stage_id'];
            $task_order->save();
        }

        $projectArr = [
            'project_id' => $task->project_id,
            'project_name' => $project->name,
            'task_id' => $task->id,
            'name' => $task->title,
            'updated_by' => \Auth::user()->id,
            'old_status' => ucwords($request->old_status),
            'new_status' => ucwords($request->new_status),
        ];

        $pArr = [
            'project_name' => $project->name,
            'project_label' => $project->label()->name,
            'project_status' => Projects::$project_status[$project->status],
            'task_name' => $task->title,
            'task_priority' => ucfirst($task->priority),
            'task_status' => ucfirst($task->status),
            'task_old_stage' => ucwords($request->old_status),
            'task_new_stage' => ucwords($request->new_status),
        ];

        foreach(array_merge($userProject, [$project->client]) as $u)
        {
            Utility::sendNotification('move_task', $u, $projectArr);
            Utility::sendEmailTemplate('Move Task', $u, $pArr);
        }
    }

    public function taskShow($task_id, $client_id = '')
    {
        $task    = Task::find($task_id);
        $project = Projects::find($task->project_id);

        $permissions = $project->client_project_permission();
        $perArr      = (!empty($permissions) ? explode(',', $permissions->permissions) : []);

        return view('projects.taskShow', compact('task', 'perArr'));
    }

    public function commentStore(Request $request, $project_id, $task_id)
    {

        $post               = [];
        $post['task_id']    = $task_id;
        $post['comment']    = $request->comment;
        $post['created_by'] = \Auth::user()->authId();
        $post['user_type']  = \Auth::user()->type;
        $comment            = Comment::create($post);

        $comment->deleteUrl = route('comment.destroy', [$comment->id]);

        return $comment->toJson();
    }

    public function commentDestroy($comment_id)
    {


        $comment = Comment::find($comment_id);
        $comment->delete();

        return "true";
    }

    public function commentStoreFile(Request $request, $task_id)
    {
        $request->validate(
            ['file' => 'required|mimes:jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,zip,rar|max:20480']
        );
        $fileName = $task_id . time() . "_" . $request->file->getClientOriginalName();

        $request->file->storeAs('tasks', $fileName);
        $post['task_id']    = $task_id;
        $post['file']       = $fileName;
        $post['name']       = $request->file->getClientOriginalName();
        $post['extension']  = "." . $request->file->getClientOriginalExtension();
        $post['file_size']  = round(($request->file->getSize() / 1024) / 1024, 2) . ' MB';
        $post['created_by'] = \Auth::user()->authId();
        $post['user_type']  = \Auth::user()->type;

        $TaskFile            = TaskFile::create($post);
        $TaskFile->deleteUrl = route('comment.file.destroy', [$TaskFile->id]);

        return $TaskFile->toJson();
    }

    public function commentDestroyFile(Request $request, $file_id)
    {


        $commentFile = TaskFile::find($file_id);
        $path        = storage_path('tasks/' . $commentFile->file);
        if(file_exists($path))
        {
            \File::delete($path);
        }
        $commentFile->delete();

        return "true";
    }

    public function checkListStore(Request $request, $task_id)
    {

        $request->validate(
            ['name' => 'required']
        );
        $post['task_id']      = $task_id;
        $post['name']         = $request->name;
        $post['created_by']   = \Auth::user()->authId();
        $CheckList            = CheckList::create($post);
        $CheckList->deleteUrl = route(
            'task.checklist.destroy', [
                                        $CheckList->task_id,
                                        $CheckList->id,
                                    ]
        );
        $CheckList->updateUrl = route(
            'task.checklist.update', [
                                       $CheckList->task_id,
                                       $CheckList->id,
                                   ]
        );

        return $CheckList->toJson();
    }

    public function checklistDestroy(Request $request, $task_id, $checklist_id)
    {


        $checklist = CheckList::find($checklist_id);
        $checklist->delete();

        return "true";
    }

    public function checklistUpdate($task_id, $checklist_id)
    {


        $checkList = CheckList::find($checklist_id);
        if($checkList->status == 0)
        {
            $checkList->status = 1;
        }
        else
        {
            $checkList->status = 0;
        }
        $checkList->save();

        return $checkList->toJson();
    }

    public function clientPermission($project_id, $client_id)
    {
        $client   = User::find($client_id);
        $project  = Projects::find($project_id);
        $selected = $client->clientPermission($project->id);
        if($selected)
        {
            $selected = explode(',', $selected->permissions);
        }
        else
        {
            $selected = [];
        }
        $permissions = Projects::$permission;

        return view('clients.create', compact('permissions', 'project_id', 'client_id', 'selected'));
    }

    public function storeClientPermission(request $request, $project_id, $client_id)
    {
        $this->validate(
            $request, [
                        'permissions' => 'required',
                    ]
        );

        $project = Projects::find($project_id);
        if($project->created_by == \Auth::user()->creatorId())
        {
            $client      = User::find($client_id);
            $permissions = $client->clientPermission($project->id);
            if($permissions)
            {
                $permissions->permissions = implode(',', $request->permissions);
                $permissions->save();
            }
            else
            {
                ClientPermission::create(
                    [
                        'client_id' => $client->id,
                        'project_id' => $project->id,
                        'permissions' => implode(',', $request->permissions),
                    ]
                );
            }

            return redirect()->back()->with('success', __('Permissions successfully updated.'))->with('status', 'clients');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'))->with('status', 'clients');
        }

    }

    public function getSearchJson(Request $request)
    {
        $html   = '';
        $usr    = \Illuminate\Support\Facades\Auth::user();
        $type   = $usr->type;
        $search = $request->keyword;

        if(!empty($search))
        {
            if($type == 'client')
            {
                $objProject = Projects::select(
                    [
                        'projects.id',
                        'projects.name',
                    ]
                )->where('projects.client', '=', Auth::user()->id)->where('projects.created_by', '=', $usr->creatorId())->where('projects.name', 'LIKE', $search . "%")->get();

                $html .= '<li>
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Projects') . '
                            </span>
                        </li>';


                if($objProject->count() > 0)
                {
                    foreach($objProject as $project)
                    {
                        $html .= '<li>
                            <a class="list-link pl-4" href="' . route('projects.show', $project->id) . '">
                                <span>' . $project->name . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li>
                                <a class="list-link pl-4" href="#">
                                    <span>' . __('No Projects Found.') . '</span>
                                </a>
                            </li>';
                }

                $objTask = Task::select(
                    [
                        'tasks.project_id',
                        'tasks.title',
                    ]
                )->join('projects', 'tasks.project_id', '=', 'projects.id')->where('projects.client', '=', $usr->id)->where('projects.created_by', '=', $usr->creatorId())->where('tasks.title', 'LIKE', $search . "%")->get();

                $html .= '<li>
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Tasks') . '
                            </span>
                        </li>';

                if($objTask->count() > 0)
                {
                    foreach($objTask as $task)
                    {
                        $html .= '<li>
                            <a class="list-link pl-4" href="' . route('project.taskboard', [$task->project_id]) . '">
                                <span>' . $task->title . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li>
                                <a class="list-link pl-4" href="#">
                                    <span>' . __('No Tasks Found.') . '</span>
                                </a>
                            </li>';
                }
            }
            else
            {
                $objProject = Projects::select(
                    [
                        'projects.id',
                        'projects.name',
                    ]
                )->join('userprojects', 'userprojects.project_id', '=', 'projects.id')->where('userprojects.user_id', '=', $usr->id)->where('projects.created_by', '=', $usr->creatorId())->where('projects.name', 'LIKE', $search . "%")->get();

                $html .= '<li>
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Projects') . '
                            </span>
                        </li>';

                if($objProject->count() > 0)
                {
                    foreach($objProject as $project)
                    {
                        $html .= '<li>
                                    <a class="list-link pl-4" href="' . route('projects.show', [$project->id]) . '">
                                        <span>' . $project->name . '</span>
                                    </a>
                                </li>';
                    }
                }
                else
                {
                    $html .= '<li>
                                <a class="list-link pl-4" href="#">
                                    <span>' . __('No Projects Found.') . '</span>
                                </a>
                            </li>';
                }

                $objTask = Task::select(
                    [
                        'tasks.project_id',
                        'tasks.title',
                    ]
                )->join('projects', 'tasks.project_id', '=', 'projects.id')->join('userprojects', 'userprojects.project_id', '=', 'projects.id')->where('userprojects.user_id', '=', $usr->id)->where('projects.created_by', '=', $usr->creatorId())->where('tasks.title', 'LIKE', $search . "%")->get();

                $html .= '<li>
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Tasks') . '
                            </span>
                        </li>';

                if($objTask->count() > 0)
                {
                    foreach($objTask as $task)
                    {
                        $html .= '<li>
                            <a class="list-link pl-4" href="' . route('project.taskboard', [$task->project_id]) . '">
                                <span>' . $task->title . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li>
                                <a class="list-link pl-4" href="#">
                                    <span>' . __('No Tasks Found.') . '</span>
                                </a>
                            </li>';
                }
            }
        }
        else
        {
            $html .= '<li>
                        <a class="list-link pl-4" href="#">
                        <i class="fas fa-search"></i>
                            <span>' . __('Type and search By Project & Tasks.') . '</span>
                        </a>
                      </li>';
        }

        print_r($html);
    }

    public function timeSheetCreate()
    {
        if(\Auth::user()->can('create timesheet'))
        {
            $projects = \Auth::user()->projects->pluck('name', 'id')->prepend('Please Select Project', '');

            return view('projects.timesheetCreate', compact('projects'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function timeSheetStore(Request $request)
    {
        if(\Auth::user()->can('create timesheet'))
        {
            $timeSheet             = new Timesheet();
            $timeSheet->project_id = $request->project_id;
            $timeSheet->task_id    = $request->task_id;
            $timeSheet->date       = $request->date;
            $timeSheet->hours      = $request->hours;
            $timeSheet->remark     = $request->remark;
            $timeSheet->user_id    = \Auth::user()->id;
            $timeSheet->save();

            return redirect()->route('task.timesheetRecord')->with('success', __('Task timesheet successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function timeSheet()
    {
        if(\Auth::user()->can('manage timesheet'))
        {
            $user        = \Auth::user();
            $project_ids = $user->projects->pluck('id')->toArray();

            if($user->type == 'company')
            {
                $timeSheets = Timesheet::whereIn('project_id', $project_ids)->get();
            }
            elseif($user->type == 'client')
            {
                $client_project = Projects::where('client', '=', $user->id)->pluck('id')->toArray();
                $timeSheets     = Timesheet::whereIn('project_id', $client_project)->get();
            }
            else
            {
                $timeSheets = Timesheet::whereIn('project_id', $project_ids)->where('user_id', '=', $user->id)->get();
            }

            return view('projects.timeSheet', compact('timeSheets'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function timeSheetEdit($timeSheet_id)
    {
        if(\Auth::user()->can('edit timesheet'))
        {
            $projects  = \Auth::user()->projects->pluck('name', 'id')->prepend('Please Select Project', '');
            $timeSheet = Timesheet::find($timeSheet_id);

            return view('projects.timesheetEdit', compact('timeSheet', 'projects'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }

    public function timeSheetUpdate(Request $request, $timeSheet_id)
    {
        if(\Auth::user()->can('edit timesheet'))
        {
            $timeSheet             = Timesheet::find($timeSheet_id);
            $timeSheet->project_id = $request->project_id;
            $timeSheet->task_id    = $request->task_id;
            $timeSheet->date       = $request->date;
            $timeSheet->hours      = $request->hours;
            $timeSheet->remark     = $request->remark;
            $timeSheet->save();

            return redirect()->route('task.timesheetRecord')->with('success', __('Task timesheet successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function timeSheetDestroy($timeSheet_id)
    {


        if(\Auth::user()->can('delete timesheet'))
        {
            $timeSheet = Timesheet::find($timeSheet_id);
            $timeSheet->delete();

            return redirect()->route('task.timesheetRecord')->with('success', __('Task timesheet successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function projectTask(Request $request)
    {
        $task = new Task();
        if($request->project_id)
        {
            $task = $task->where('project_id', '=', $request->project_id);
        }
        $task = $task->get()->pluck('title', 'id');

        return response()->json($task);
    }

    public function bug($project_id)
    {
        $user = Auth::user();
        if($user->can('manage bug report'))
        {
            $project = Projects::find($project_id);
            if(!empty($project) && $project->created_by == Auth::user()->creatorId())
            {
                if($user->type != 'company')
                {
                    $arrProjectUsers = $project->project_user()->pluck('user_id')->toArray();
                    array_push($arrProjectUsers, $project->client);

                    if(!in_array($user->id, $arrProjectUsers))
                    {
                        return redirect()->back()->with('error', __('Permission denied.'));
                    }
                }

                if($user->type == 'company' || $user->type == 'client')
                {
                    $bugs = Bug::where('project_id', '=', $project_id)->get();
                }
                else
                {
                    $bugs = Bug::where('assign_to', '=', $user->id)->where('project_id', '=', $project_id)->get();
                }

                return view('projects.bug', compact('project', 'bugs'));
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

    public function bugCreate($project_id)
    {
        if(\Auth::user()->can('create bug report'))
        {

            $priority     = Bug::$priority;
            $status       = BugStatus::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');
            $project_user = Userprojects::where('project_id', $project_id)->get();
            $users        = array();
            foreach($project_user as $user)
            {
                $user               = $user->project_users->first();
                $users[$user['id']] = $user['name'];
            }

            return view('projects.bugCreate', compact('status', 'project_id', 'priority', 'users'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }

    function bugNumber()
    {
        $latest = Bug::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->bug_id + 1;
    }

    public function bugStore(Request $request, $project_id)
    {
        if(\Auth::user()->can('create bug report'))
        {
            $validator = \Validator::make(
                $request->all(), [

                                   'title' => 'required',
                                   'priority' => 'required',
                                   'status' => 'required',
                                   'assign_to' => 'required',
                                   'start_date' => 'required',
                                   'due_date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('task.bug', $project_id)->with('error', $messages->first());
            }

            $usr         = \Auth::user();
            $userProject = Userprojects::where('project_id', '=', $project_id)->pluck('user_id')->toArray();
            $project     = Projects::where('id', '=', $project_id)->first();

            $bug              = new Bug();
            $bug->bug_id      = $this->bugNumber();
            $bug->project_id  = $project_id;
            $bug->title       = $request->title;
            $bug->priority    = $request->priority;
            $bug->start_date  = $request->start_date;
            $bug->due_date    = $request->due_date;
            $bug->description = $request->description;
            $bug->status      = $request->status;
            $bug->assign_to   = $request->assign_to;
            $bug->created_by  = \Auth::user()->id;
            $bug->save();

            ActivityLog::create(
                [
                    'user_id' => $usr->id,
                    'project_id' => $project_id,
                    'log_type' => 'Create Bug',
                    'remark' => json_encode(['title' => $bug->title]),
                ]
            );

            $projectArr = [
                'project_id' => $project_id,
                'name' => $project->name,
                'updated_by' => $usr->id,
            ];

            foreach(array_merge($userProject, [$project->client]) as $u)
            {
                Utility::sendNotification('create_bug', $u, $projectArr);
            }

            return redirect()->route('task.bug', $project_id)->with('success', __('Bug successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugEdit($project_id, $bug_id)
    {
        if(\Auth::user()->can('edit bug report'))
        {
            $bug          = Bug::find($bug_id);
            $priority     = Bug::$priority;
            $status       = BugStatus::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');
            $project_user = Userprojects::where('project_id', $project_id)->get();
            $users        = array();
            foreach($project_user as $user)
            {
                $user               = $user->project_users->first();
                $users[$user['id']] = $user['name'];
            }

            return view('projects.bugEdit', compact('status', 'project_id', 'priority', 'users', 'bug'));
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

    public function bugUpdate(Request $request, $project_id, $bug_id)
    {


        if(\Auth::user()->can('edit bug report'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'priority' => 'required',
                                   'status' => 'required',
                                   'assign_to' => 'required',
                                   'start_date' => 'required',
                                   'due_date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('task.bug', $project_id)->with('error', $messages->first());
            }
            $bug              = Bug::find($bug_id);
            $bug->title       = $request->title;
            $bug->priority    = $request->priority;
            $bug->start_date  = $request->start_date;
            $bug->due_date    = $request->due_date;
            $bug->description = $request->description;
            $bug->status      = $request->status;
            $bug->assign_to   = $request->assign_to;
            $bug->save();

            return redirect()->route('task.bug', $project_id)->with('success', __('Bug successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugDestroy($project_id, $bug_id)
    {


        if(\Auth::user()->can('delete bug report'))
        {
            $bug = Bug::find($bug_id);
            $bug->delete();

            return redirect()->route('task.bug', $project_id)->with('success', __('Bug successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugKanban($project_id)
    {
        $user = Auth::user();
        if($user->can('move bug report'))
        {
            $project = Projects::find($project_id);
            if(!empty($project) && $project->created_by == $user->creatorId())
            {
                if($user->type != 'company')
                {
                    $arrProjectUsers = $project->project_user()->pluck('user_id')->toArray();
                    array_push($arrProjectUsers, $project->client);

                    if(!in_array($user->id, $arrProjectUsers))
                    {
                        return redirect()->back()->with('error', __('Permission denied.'));
                    }
                }

                if($user->type == 'company' || $user->type == 'client')
                {
                    $bugStatus = BugStatus::where('created_by', '=', Auth::user()->creatorId())->orderBy('order', 'ASC')->get();
                }
                else
                {
                    $bugStatus = BugStatus::where('created_by', '=', Auth::user()->creatorId())->orderBy('order', 'ASC')->get();
                }

                return view('projects.bugKanban', compact('project', 'bugStatus'));
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

    public function bugKanbanOrder(Request $request)
    {
        if(\Auth::user()->can('move bug report'))
        {
            $post   = $request->all();
            $bug    = Bug::find($post['bug_id']);
            $status = BugStatus::find($post['status_id']);

            if(!empty($status))
            {
                $bug->status = $post['status_id'];
                $bug->save();
            }

            foreach($post['order'] as $key => $item)
            {
                $bug_order         = Bug::find($item);
                $bug_order->order  = $key;
                $bug_order->status = $post['status_id'];
                $bug_order->save();
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }


    }

    public function bugShow($project_id, $bug_id)
    {
        $bug = Bug::find($bug_id);

        return view('projects.bugShow', compact('bug'));
    }

    public function bugCommentStore(Request $request, $project_id, $bug_id)
    {

        $post               = [];
        $post['bug_id']     = $bug_id;
        $post['comment']    = $request->comment;
        $post['created_by'] = \Auth::user()->authId();
        $post['user_type']  = \Auth::user()->type;
        $comment            = BugComment::create($post);
        $comment->deleteUrl = route('bug.comment.destroy', [$comment->id]);

        return $comment->toJson();
    }

    public function bugCommentDestroy($comment_id)
    {


        $comment = BugComment::find($comment_id);
        $comment->delete();

        return "true";
    }

    public function bugCommentStoreFile(Request $request, $bug_id)
    {
        $request->validate(
            ['file' => 'required|mimes:jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,zip,rar|max:20480']
        );
        $fileName = $bug_id . time() . "_" . $request->file->getClientOriginalName();

        $request->file->storeAs('bugs', $fileName);
        $post['bug_id']     = $bug_id;
        $post['file']       = $fileName;
        $post['name']       = $request->file->getClientOriginalName();
        $post['extension']  = "." . $request->file->getClientOriginalExtension();
        $post['file_size']  = round(($request->file->getSize() / 1024) / 1024, 2) . ' MB';
        $post['created_by'] = \Auth::user()->authId();
        $post['user_type']  = \Auth::user()->type;

        $BugFile            = BugFile::create($post);
        $BugFile->deleteUrl = route('bug.comment.file.destroy', [$BugFile->id]);

        return $BugFile->toJson();
    }

    public function bugCommentDestroyFile(Request $request, $file_id)
    {
        $commentFile = BugFile::find($file_id);
        $path        = storage_path('bugs/' . $commentFile->file);
        if(file_exists($path))
        {
            \File::delete($path);
        }
        $commentFile->delete();

        return "true";
    }

    public function getAllProjects(Request $request)
    {
        $projects = Projects::all();
        return view('projects.indexProjects', compact('projects'));
    }

    public function projectsByUser(Request $request,$id)
    {
        $projects = Projects::leftjoin('userprojects as user','projects.id','=','user.project_id')
                    ->select('projects.*')
                    ->where('user.user_id',$id)
                    ->get();
        return view('projects.indexProjects', compact('projects'));
    }
}
