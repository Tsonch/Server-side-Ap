<?php

namespace App\Http\Controllers;

use App\Models\ChangeLogs;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function createLogs($table_name, $action_name, $row_id, $value_before, $value_after, $user_id)
    {
        ChangeLogs::create([
            'table_name' => $table_name,
            'action_name' => $action_name,
            'row_id' => $row_id,
            'value_before' => $value_before,
            'value_after' => $value_after,
            'created_by' => $user_id,
        ]);
    }

    public function getLogs(Request $request) {
        $id = $request->id;
        $model = $request->model;
        $Logs = ChangeLogs::where('table_name', $model)->where('row_id', $id);

        if ($Logs) {
            return $Logs;
        }
        else {
            return response()->json(['error' => 'Not Found'], 404);
        }
    }
    
}
