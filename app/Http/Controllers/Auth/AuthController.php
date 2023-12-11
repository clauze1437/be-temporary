<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation'      => $validator->errors(),
                'response_code'   => '00',
                'response_status' => true,
            ], 401);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'response_code'    => '00',
                'response_status'  => true,
                'response_message' => 'Berhasil masuk dan mendapatkan token'
            ], 200);
        } else {
            return response()->json([
                'response_code'    => '01',
                'response_status'  => false,
                'response_message' => 'Kesalahan pada saat masuk'
            ], 401);
        }
    }

    public function logout()
    {
        $token = request()->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            'response_code'    => '00',
            'response_status'  => true,
            'response_message' => 'Berhasil keluar dan token dihapus'
        ], 200);
    }
}
