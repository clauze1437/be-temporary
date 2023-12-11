<?php

namespace App\Http\Controllers\Web;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public static function RESPONSE_VALIDATION_ERROR($validator)
    {
        return [
            'errors' => $validator->errors(),
            'response_code' => '00',
            'response_status' => true
        ];
    }

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
            'response_message' => 'Data tidak ditemukan'
        ];
    }

    public function index()
    {
        try {
            $vehicle = Vehicle::withoutTrashed()->get();

            if ($vehicle->isEmpty()) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            return response()->json([
                'data' => $vehicle,
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
            'number_plate' => 'required|unique:vehicles,number_plate',
            'merk' => 'required',
            'type' => 'required',
            'max_tonase' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(self::RESPONSE_VALIDATION_ERROR($validator), 400);
        }

        try {
            $data = $request->all();
            Vehicle::create($data);

            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Kendaraan berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat membuat Kendaraan' . $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $vehicle = Vehicle::withoutTrashed()->where('id', $id)->first();

            if (!$vehicle) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            return response()->json([
                'data' => $vehicle,
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
            'number_plate' => 'required|unique:vehicles,number_plate,' . $id,
            'merk' => 'required',
            'type' => 'required',
            'max_tonase' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(self::RESPONSE_VALIDATION_ERROR($validator), 400);
        }

        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            $vehicle->update($request->all());
            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Kendaraan berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return response()->json(self::RESPONSE_NOT_FOUND(), 404);
            }

            $deliveryOrders = $vehicle->deliveryOrder;

            if ($deliveryOrders->where('status', 'berlangsung')->isNotEmpty()) {
                return response()->json(['message' => 'Gagal menghapus kendaraan, karena ada surat jalan yang sedang berlangsung'], 400);
            }

            $vehicle->delete();

            return response()->json([
                'response_code' => '00',
                'response_status' => true,
                'response_message' => 'Kendaraan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(self::RESPONSE_SERVER_ERROR($e), 500);
        }
    }
}
