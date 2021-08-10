<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|string|min:6',
        ]);
        if ($validator->fails()) {

            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        User::create($request->toArray());
        if (!auth()->attempt($validator)) {
            return response(['message' => 'Invalid Credentials'], 422);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $role = auth()->user()->role;
        $roleArr = explode(" ", $role);

        auth()->user()->role = $roleArr;

        return response(["ok" => true, "message" => "You have successfully registered", "user" => auth()->user(), "token" =>  $accessToken], 200);
    }
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials'], 422);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $role = auth()->user()->role;
        $roleArr = explode(" ", $role);

        auth()->user()->role = $roleArr;

        return response()->json(['user' => auth()->user(), 'token' => $accessToken,   "ok" => true], 200);
    }
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
            return response()->json(["ok" => true]);
        }
    }
}
