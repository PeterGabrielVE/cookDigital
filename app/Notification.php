<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'is_read',
    ];

    public function toHtml()
    {
        $data       = json_decode($this->data);
        $link       = '#';
        $icon       = 'fa fa-bell';
        $icon_color = 'bg-primary';
        $text       = '';

        if(isset($data->updated_by) && !empty($data->updated_by))
        {
            $usr = User::find($data->updated_by);
        }

        $name = !empty($usr) ? $usr->name : __('Someone');

        // For Notification
        if($this->type == 'assign_project')
        {
            $link       = route('projects.show', [$data->project_id,]);
            $text       = $name . " " . __('Added you') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-plus";
            $icon_color = 'bg-primary';
        }

        if($this->type == 'create_milestone')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Added new milestone') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-plus";
            $icon_color = 'bg-primary';
        }

        if($this->type == 'upload_file')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Upload new File') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-file";
            $icon_color = 'bg-info';
        }

        if($this->type == 'create_task')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Create new Task') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-tasks";
            $icon_color = 'bg-primary';
        }

        if($this->type == 'create_bug')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Create new Bug') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-tasks";
            $icon_color = 'bg-warning';
        }

        if($this->type == 'add_product')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Add new Products') . " " . __('in project') . " <b class='font-weight-bold'>" . $data->name . "</b> ";
            $icon       = "fa fa-dolly";
            $icon_color = 'bg-danger';
        }

        if($this->type == 'move_task')
        {
            $link       = route('projects.show', [$data->project_id]);
            $text       = $name . " " . __('Moved the task') . " <b class='font-weight-bold'>" . $data->name . "</b> " . __('from') . " " . __(ucwords($data->old_status)) . " " . __('to') . " " . __(ucwords($data->new_status)) . " " . __('in project') . $data->project_name;
            $icon       = "fa fa-arrows-alt";
            $icon_color = 'bg-success';
        }
        // end deals

        // for estimations
        if($this->type == 'assign_estimation')
        {
            $link       = route('estimations.show', [$data->estimation_id,]);
            $text       = $name . " " . __('Added you') . " " . __('in estimation') . " <b class='font-weight-bold'>" . $data->estimation_name . "</b> ";
            $icon       = "fa fa-plus";
            $icon_color = 'bg-primary';
        }
        // end estimations

        $date = $this->created_at->diffForHumans();

        $html = '<a href="' . $link . '" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="avatar ' . $icon_color . ' text-white rounded-circle"><i class="' . $icon . '"></i></span>
                                    </div>
                                    <div class="flex-fill ml-3">
                                        <div class="h6 text-sm mb-0">' . $text . '</div>
                                        <small class="text-muted text-xs">' . $date . '</small>
                                    </div>
                                </div>
                            </a>';

        return $html;
    }
}
