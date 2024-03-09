<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getPhpInfo() {
        return response()->json([
            'currentPhpVersion' => phpversion()
        ]);
    }

    public function getClientInfo() {
        return response()->json([
            'ip' => request()->ip(),
            'userAgent' => request()->userAgent()
        ]);
    }

    public function getDatabaseInfo() {
        return response()->json([
            'database' => DB::connection()->getDatabaseName()
        ]);
    }
}
