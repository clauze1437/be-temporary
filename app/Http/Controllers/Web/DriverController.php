<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public static function RESPONSE_VALIDATION_ERROR($validator)
    {
        return [
            'errors' => $validator->errors(),
            'response_code' => '00',
            'response_status' => true
        ];
    }

    public static function RESPONSE_NOT_FOUND()
    {
        return [
            'data' => [],
            'response_code' => '01',
            'response_status' => false,
            'response_message' => 'Data tidak ditemukan'
        ];
    }

    public static function RESPONSE_SERVER_ERROR($error)
    {
        return [
            'message' => 'Internal Server Error',
            'error' => $error->getMessage()
        ];
    }

    public function index()
    {
        try {
            $driver = User::where('role', 'driver')->get();

            if ($driver->isEmpty()) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            $result = $driver->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'phone_number' => $item->phone_number,
                    'alt_phone_number' => $item->driver->alt_phone_number,
                    'address' => $item->driver->address,
                    'role' => $item->role,
                    'status' => $item->status,
                    'avatar' => $item->avatar,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at
                ];
            });

            return response()->json([
                'data' => $result,
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Data ditemukan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'alt_phone_number' => 'required',
            'address' => 'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(self::RESPONSE_VALIDATION_ERROR($validator), 400);
        }

        try {
            $avatarFile = $request->file('avatar');

            if ($avatarFile) {
                $filename = time() . '_' . $avatarFile->getClientOriginalName();

                Storage::putFileAs('public/avatars', $avatarFile, $filename);
            }

            $driver = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'role' => 'driver',
                'status' => 1,
                'avatar' => $filename,
                'password' => bcrypt($request->input('password'))
            ]);

            Driver::create([
                'user_id' => $driver->id,
                'alt_phone_number' => $request->input('alt_phone_number'),
                'address' => $request->input('address')
            ]);

            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Pengemudi berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat membuat pengemudi' . $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $driver = User::where('id', $id)->where('role', 'driver')->first();

            if (!$driver) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            $result = [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'phone_number' => $driver->phone_number,
                'alt_phone_number' => $driver->driver->alt_phone_number,
                'address' => $driver->driver->address,
                'role' => $driver->role,
                'status' => $driver->status,
                'avatar' => $driver->avatar,
                'created_at' => $driver->created_at,
                'updated_at' => $driver->updated_at
            ];

            return response()->json([
                'data' => $result,
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Data ditemukan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required|unique:users,phone_number,' . $id,
            'alt_phone_number' => 'required',
            'address' => 'required',
            'status' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg'
        ]);

        if ($validator->fails()) {
            return response()->json(self::RESPONSE_VALIDATION_ERROR($validator), 400);
        }

        try {
            $driver = User::where('id', $id)->where('role', 'driver')->first();

            if (!$driver) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            if ($request->hasFile('avatar')) {
                if ($driver->avatar) {
                    Storage::delete('public/avatars/' . $driver->avatar);
                }

                $avatarFile = $request->file('avatar');
                $filename = time() . '_' . $avatarFile->getClientOriginalName();
                Storage::putFileAs('public/avatars', $avatarFile, $filename);

                $driver->avatar = $filename;
            }

            $driver->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'status' => $request->input('status'),
                'updated_at' => now()
            ]);

            $driver->driver()->update([
                'alt_phone_number' => $request->input('alt_phone_number'),
                'address' => $request->input('address')
            ]);

            $driver->touch();

            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Pengemudi berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $driver = User::where('id', $id)->where('role', 'driver')->first();

            if (!$driver) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            $avatarFileName = $driver->avatar;
            $deliveryOrders  = $driver->driverDeliveryOrder;

            if ($deliveryOrders->where('status', 'berlangsung')->isNotEmpty()) {
                return response()->json(['message' => 'Gagal menghapus kendaraan, karena ada surat jalan yang sedang berlangsung'], 400);
            }

            foreach ($deliveryOrders as $deliveryOrder) {
                // hapus gambar bukti pengiriman
                $deliveryOrder->delete();
            }

            if (!empty($avatarFileName)) {
                Storage::delete('public/avatars/' . $avatarFileName);
            }

            $driver->delete();

            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Pengemudi berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }
}
