<?php

namespace App\Http\Controllers;

use App\Models\ChangeLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    
    public function restoreRow(Request $request) {
        $log_id = $request->id;
        $user = $request->user();

        try {
            $log = ChangeLogs::where('id', $log_id)->first();
            $table = $log->table_name;
            $current_value = $log->value_after;
            $value_before = $log->value_before;

            if($value_before == null) {
                DB::table($table)->where('id', $log->id)->delete();
                $this->createLogs($table, 'delete', $log->row_id, $current_value, null, $user->id);
            }
            else if($current_value == null) {
                $dataArray = json_decode($value_before, true);
                $dataArray['created_at'] = Carbon::parse($dataArray['created_at'])->format('Y-m-d H:i:s');
                $dataArray['updated_at'] = Carbon::parse($dataArray['updated_at'])->format('Y-m-d H:i:s');
                if (DB::table($table)->where('id', $dataArray['id'])->exists()) {
                    DB::table($table)->where('id', $dataArray['id'])->delete();
                }
                DB::table($table)->insert($dataArray);
                $this->createLogs($table, 'create', $log->row_id, 'null', $current_value, $user->id);
            }
            else {
                $dataArray = json_decode($value_before, true);
                $dataArray['created_at'] = Carbon::parse($dataArray['created_at'])->format('Y-m-d H:i:s');
                $dataArray['updated_at'] = Carbon::parse($dataArray['updated_at'])->format('Y-m-d H:i:s');
                DB::table($table)->where('id', $log->row_id)->update($dataArray);
                $this->createLogs($table, __FUNCTION__, $log->row_id, $current_value, $value_before, $user->id);
            }

            DB::commit();

        } catch (\Exception $err) {
            DB::rollBack();

            throw $err;
        }
    }

}
