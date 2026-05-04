<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CooperativeRequest;
use App\Models\User;

class CooperativeRequestController extends Controller
{
    // 1. ดูรายการคำขอของตัวเอง (User ทั่วไป)
    public function index(Request $request)
    {
        // เช็คก่อนว่ามี user ล็อกอินมาจริงไหม
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดไม่มีข้อมูล user'
            ], 401);
        }

        $requests = CooperativeRequest::where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ], 200);
    }

    // 2. ดูคำขอทั้งหมด พร้อมระบบกรองสถานะ (สำหรับ Staff)
    public function allRequests(Request $request)
    {
        // ตรวจสอบความถูกต้องของสถานะที่ส่งมาจาก Body JSON ใน Postman
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,approved,rejected',
        ], [
            'status.in' => 'กรุณาระบุสถานะให้ถูกต้อง (pending, approved, หรือ rejected เท่านั้น)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        // ดึงข้อมูลตามเงื่อนไขการกรอง (ถ้ามี)
        $requests = CooperativeRequest::query()
            // ->with('user') // เปิดใช้เมื่อตั้งค่า Relationship ใน Model แล้ว
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })->orderBy('updated_at', 'asc')
            ->get();

        // แยกกลุ่มข้อมูลตามสถานะ
        $groupedData = $requests->groupBy('status');

        // นับจำนวนสมาชิกในแต่ละกลุ่มแยกกัน
        $groupCounts = $groupedData->map(function ($group) {
            return $group->count();
        });

        return response()->json([
            'status' => 'success',
            'total_count' => $requests->count(),
            'group_counts' => $groupCounts,
            'data' => $groupedData
        ], 200);
    }
    // 3. ยื่นคำขอจัดตั้งสหกรณ์
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:cooperative_requests,name',
            'member_count' => 'required|integer|min:10',
        ], [
            'name.unique' => 'ชื่อที่ท่านตั้งซ้ำ มีผู้ใช้งานแล้ว กรุณาใช้ชื่ออื่น',
            'name.required' => 'กรุณาระบุชื่อสหกรณ์',
            'member_count.min' => 'จำนวนสมาชิกเริ่มต้นต้องไม่น้อยกว่า 10 คน',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        $coopRequest = CooperativeRequest::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'member_count' => $request->member_count,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cooperative request submitted successfully',
            'data' => $coopRequest
        ], 201);
    }

    // 4. ตรวจสอบและอนุมัติคำขอ (สำหรับ Staff)
    public function review(Request $request, $id)
    {
        $coopRequest = CooperativeRequest::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'remark' => 'required|string'
        ], [
            'status.in' => 'สถานะต้องเป็น approved หรือ rejected เท่านั้น',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        if ($coopRequest->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'สหกรณ์แห่งนี้ได้รับการตรวจสอบแล้ว'
            ], 422);
        }
        $coopRequest->update([
            'status' => $request->status,
            'remark' => $request->remark
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ตรวจสอบสถานะเรียบร้อยแล้ว',
            'data' => $coopRequest
        ], 200);
    }
}