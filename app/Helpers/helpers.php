<?php

use App\Enums\ticketStatusEnum;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;

if (!function_exists('getDistinctModuleNamesFromPermissionArray')) {
    function getDistinctModuleNamesFromPermissionArray($permissions)
    {
        $permissionArray = [];

        foreach ($permissions as $permission) {
            array_push($permissionArray, (explode('-', $permission->name)[0]));
        }

        return array_unique($permissionArray);
    }
}


if (!function_exists('getDistinctModuleNamesFromPermissionArrays')) {
    function getDistinctModuleNamesFromPermissionArrays($permissions)
    {
        $permissionArray = [];

        foreach ($permissions as $permission) {
            array_push($permissionArray, (explode('-', $permission)[0]));
        }

        return array_unique($permissionArray);
    }
}

if (!function_exists('developerAccess')) {
    function developerAccess($attribute, $column, $projectId): bool
    {
        if (auth()->user()) {

            $authUser = User::find(auth()->user()->id);

            $projectAssignee = Project::where('id', $projectId)->first()->assignee;

            $projectAssignee = json_decode($projectAssignee);

            if (count($projectAssignee) > 0) {

                if (in_array($authUser->id, $projectAssignee)) {

                    // For developer show assigned projects.
                    if ($column == 'project_id')
                        return true;

                    // For developer show only asigned tickets and folder regarding the project.
                    $tickets = Ticket::where($column, $attribute->id)->whereNotNull('assignee')->get();

                    if ($tickets)
                        foreach ($tickets as $ticket) {
                            if ($ticket) {
                                if ($ticket->assignee != null) {
                                    $userIds = json_decode($ticket->assignee);

                                    if (in_array($authUser->id, $userIds)) {
                                        return true;
                                    }
                                }
                            }
                        }
                }
            }
        }

        return false;
    }
}

if (!function_exists('projectManagerAccess')) {
    function projectManagerAccess($project): bool
    {
        if (auth()->user()) {

            $authUser = auth()->user()->id;

            if ($project->assignee != null) {
                $userIds = json_decode($project->assignee);

                if (in_array($authUser, $userIds))
                    return true;
            }
        }

        return false;
    }
}

if (!function_exists('testerAccess')) {
    function testerAccess($project): bool
    {
        if (auth()->user()) {

            $authUser = auth()->user()->id;

            if (in_array($authUser, json_decode($project->assignee)))
                return true;
        }

        return false;
    }
}

if (!function_exists('getUnChangableStatus')) {
    function getUnChangableStatus($user, $ticket)
    {
        $unChangableStatus = [];

        if ($user->hasRole('Developer')) {
            $unChangableStatus = config('constants.developerUnChangableStatus');

            if (in_array($ticket->status, [1, 2])) {

                array_push($unChangableStatus, 5);
            } else if (in_array($ticket->status, [0, 3])) {

                $unChangableStatus = [];
            }
        } else if ($user->hasRole('QA')) {
            $unChangableStatus = config('constants.testerUnChangableStatus');

            if ($ticket->status == 3) {
                array_push($unChangableStatus, 1);
            } elseif ($ticket->status == 4) {
                array_push($unChangableStatus, 1);
            } elseif ($ticket->status == 0) {
                $unChangableStatus = [];
            }
        }

        return $unChangableStatus;
    }
}

if (!function_exists('updatedValues')) {
    function updatedValues($activity, $attr)
    {
        if ($attr == 'old') {
            return array_diff_assoc($activity->properties['old'], $activity->properties['attributes']);
        } else {
            return array_diff_assoc($activity->properties['attributes'], $activity->properties['old']);
        }
    }
}

if (!function_exists('isValueNull')) {
    function isValueNull($key, $val)
    {
        $val = in_array($val, ['']) ? '-' : $val;

        if ($key == 'status') {
            return config("constants.ticketStatus.$val");
        } else if ($key == 'is_active') {
            return config("constants.isActiveStatus.$val");
        } else if ($key == 'priority') {
            return config("constants.priorityTypes.$val");
        } else if ($key == 'type') {
            return config("constants.taskTypes.$val");
        }
        return $val;
    }
}
