<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public static function RESPONSE_SERVER_ERROR($error)
    {
        return [
            'message' => 'Internal Server Error',
            'error' => $error->getMessage()
        ];
    }

    public static function RESPONSE_NOT_FOUND()
    {
        return [
            'data' => [],
            'response_code' => '01',
            'response_status' => false,
            'response_message' => 'Data not found'
        ];
    }

    protected function respondWithUserInfo($userInfo, $role)
    {
        if (!$userInfo) {
            return response()->json(self::RESPONSE_NOT_FOUND(), 404);
        }

        $data = [
            'id' => $userInfo->id,
            'name' => $userInfo->name,
            'role' => $userInfo->role,
            'email' => $userInfo->email,
            'avatar' => $userInfo->avatar,
            'status' => $userInfo->status,
        ];

        if ($role == 'driver') {
            $data['address'] = $userInfo->driver->address;
            $data['phone_number'] = $userInfo->phone_number;
            $data['alt_phone_number'] = $userInfo->driver->alt_phone_number;
        }

        return response()->json([
            'data' => $data,
            'response_code' => '00',
            'response_status' => true,
            'response_message' => 'Data found'
        ], 200);
    }

    public function getAdminInfo()
    {
        try {
            $user = Auth::user();
            $userInfo = User::where('id', $user->id)->where('role', '<>', 'driver')->first();
            return $this->respondWithUserInfo($userInfo, '');
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }

    public function getDriverInfo()
    {
        try {
            $user = Auth::user();
            $userInfo = User::where('id', $user->id)->where('role', 'driver')->first();
            return $this->respondWithUserInfo($userInfo, 'driver');
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }
}
