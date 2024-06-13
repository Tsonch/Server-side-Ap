<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth;
use App\Http\Requests\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use App\DTO\UserDTO;
use App\Models\UsersAndRoles;
use App\Models\VerifyCode;
use Carbon\Carbon;
use Error;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MainController extends Controller
{
    public function auth(Auth $request)
    {
        $userData = $request->createDTO();

        $user = User::where('username', $userData->username)->first();

        if (!$user || !Hash::check($userData->password, $user->password)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $verify_code = VerifyCode::where('user_id', $user->id)->first();
        if ($verify_code && $verify_code->count >= env("MAX_CODE_COUNT", 3)) {
            $now = Carbon::now();
            $oldestCode = VerifyCode::where('user_id', $user->id)->oldest()->first();
            $verify_code->count += 1;
            if ($now->diffInSeconds($oldestCode->updated_at) <= 30) {
                return response()->json(['message' => 'You need to wait ' . 30 - $now->diffInSeconds($oldestCode->updated_at) . ' seconds'], 401);
            }
        }

        $code = rand(100000, 999999);
        if ($verify_code) {
            $verify_code->count > 3 ? $verify_code->count : $verify_code->count += 1;
            $verify_code->expires_at = Carbon::now()->addMinutes(10);
            $verify_code->code = $code;
            $verify_code->save();
        } else {
            VerifyCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(env("CODES_EXPIRATION_MINUTES", 3)),
                'count' => 1
            ]);
        }

        Mail::raw("Используйте данный код чтобы войти: $code", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Ваш код доступа');
        });

        return response()->json(['message' => 'Code send'], 200);
    }

    public function registration(Registration $request)
    {
        try {
            DB::beginTransaction();

            $userData = $request->createDTO();

            $user = User::create([
                'username' => $userData->username,
                'email' => $userData->email,
                'password' => bcrypt($userData->password),
                'birthday' => $userData->birthday
            ]);
    
            UsersAndRoles::create([
                'user_id' => $user->id,
                'role_id' => '3',
                'created_by' => '1'
            ]);
            $Log = new LogsController;
            $Log->createLogs('users', 'registration', $user->id, null, $user, $user->id);

            DB::commit();
    
            return response()->json($user, 201);
        }
        catch (\Exception $err) {
            DB::rollback();
            throw $err;
        }
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "user" => $user
        ]);
    }

    public function out(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function tokens(Request $request)
    {
        $tokens = $request->user()->tokens;

        return response()->json(
            [
                "tokens" => $tokens
            ]
        );
    }

    public function outAll(Request $request)
    {
        $userTokens = $request->user()->tokens;
        foreach ($userTokens as $token) {
            $token->revoke();
        }

        return response()->json(["All tokens is logout"], 200);
    }

    public function getCode(Request $request) {
        $username = $request->username;

        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $verify_code = VerifyCode::where('user_id', $user->id)->first();
            
        if($verify_code) {
            if($verify_code->count >= env("MAX_CODE_COUNT", 3)) {
                $now = Carbon::now();
                $oldestCode = VerifyCode::where('user_id', $user->id)->oldest()->first();
                $verify_code->count += 1;
                if ($now->diffInSeconds($oldestCode->updated_at) <= 30) {
                    return response()->json(['message' => 'You need to wait ' . 30 - $now->diffInSeconds($oldestCode->updated_at) . ' seconds'], 401);
                }
            }

            $verify_code->count > 3 ? $verify_code->count : $verify_code->count += 1;
            $verify_code->expires_at = Carbon::now()->addMinutes(env("CODES_EXPIRATION_MINUTES", 3));
            $verify_code->code = rand(100000, 999999);
            $verify_code->save();
            Mail::raw("Используйте данный код чтобы войти: $verify_code->code", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Ваш код доступа');
            });
            return response()->json(['message' => 'Code send'], 200);
        } 
        else {
            return response()->json(['message' => 'You need to login and request first code'], 401);
        }
    }

    public function verify(Request $request) {
        $username = $request->username;
        $code = $request->code;
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        $verify_code = VerifyCode::where('user_id', $user->id)->first();
        if ($code == $verify_code->code && Carbon::now() <= $verify_code->expires_at) {

            if (env('MAX_ACTIVE_TOKENS') == 0) {
                return response()->json([
                    'message' => 'change env MAX_ACTIVE_TOKENS'
                ], 401);
            }
    
            $userActiveTokens = $user->tokens()->where('revoked', false);
            $userTokenCount = $userActiveTokens->count();
    
            while ($userTokenCount >= env('MAX_ACTIVE_TOKENS', 3)) {
                $oldestToken = $userActiveTokens->orderBy('created_at', 'asc')->first();
                $oldestToken->revoke();
                $userTokenCount = $user->tokens()->where('revoked', false)->count();
            }
    
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addDays(env('TOKEN_EXPIRATION_DAYS', 15));
            $token->save();
    
            $verify_code->delete();
            return response()->json([
                "access_tocken" => $tokenResult->accessToken
            ], 200);
        } else {
            return response()->json([
                'message' => 'Wrong code or time explode'
            ], 401);
        }
    }
}
