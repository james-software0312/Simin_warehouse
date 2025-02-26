<?php
// app/Services/ActivityLogService.php

namespace App\Services;

use App\Models\ActivityLogModel;

class ActivityLogService
{
    /**
 * Get all activity logs with associated user information.
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getAll()
{
    return ActivityLogModel::join('users', 'activity_log.causer_id', '=', 'users.id')
        ->select('activity_log.*', 'users.name as user')
        ->where('log_name', '!=', 'sell_hide_history')
        ->where('event', '!=', 'deleted')
        ->orderBy('activity_log.created_at', 'desc')
        ->get();
}

/**
 * Create a new activity log entry.
 *
 * @param array $data The data for creating a new activity log entry.
 * @return \App\Models\ActivityLogModel
 */
public function create($data)
{
    return ActivityLogModel::create($data);
}

/**
 * Update an existing activity log entry.
 *
 * @param int $id The ID of the activity log entry to update.
 * @param array $data The data for updating the activity log entry.
 * @return \App\Models\ActivityLogModel
 */
public function update($id, $data)
{
    $SetData = ActivityLogModel::findOrFail($id);
    $SetData->each->update($data);
    return $SetData;
}

/**
 * Delete an existing activity log entry.
 *
 * @param int $id The ID of the activity log entry to delete.
 * @return void
 */
public function delete($id)
{
    $SetData = ActivityLogModel::findOrFail($id);
    $SetData->each->delete();
}

/**
 * Get an activity log entry by its ID.
 *
 * @param int $id The ID of the activity log entry to retrieve.
 * @return \App\Models\ActivityLogModel
 */
public function getById($id)
{
    return ActivityLogModel::findOrFail($id);
}


}
