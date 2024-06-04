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
use Carbon\Carbon;

class UserController extends Controller
{
    public function auth(Auth $request)
    {
        $userData = $request->createDTO();

        $user = User::where('username', $userData->username)->first();

        if (!$user || !Hash::check($userData->password, $user->password)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (intval(env('MAX_ACTIVE_TOKENS')) <= 0) {
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

        return response()->json([
            "access_tocken" => $tokenResult->accessToken
        ], 200);
    }

    public function registration(Registration $request)
    {

        $userData = $request->createDTO();

        $user = User::create([
            'username' => $userData->username,
            'email' => $userData->email,
            'password' => bcrypt($userData->password),
            'birthday' => $userData->birthday
        ]);

        return response()->json($user, 201);
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
}
