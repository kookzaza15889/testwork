<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    // ระบบสมัครสมาชิก (สำหรับประชาชนทั่วไป)
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:public,staff',
        ], [
            // บังคับกรอกชื่อ
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            // อีเมล
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

            // รหัสผ่าน
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านยืนยันไม่ตรงกัน',

            // ประเภทผู้ใช้งาน
            'role.required' => 'กรุณาระบุประเภทผู้ใช้งาน',
            'role.in' => 'กรุณาเลือกประเภทผู้ใช้งานให้ถูกต้อง (public หรือ staff)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // 2. ส่ง Success Response กลับไป 
        return response()->json([
            'status' => 'success',
            'message' => 'สมัครสมาชิกสำเร็จ',
            'data' => $user,
            'access_token' => $token,
        ], 201);
    }

    // ระบบเข้าสู่ระบบ
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // ตรวจสอบอีเมลและรหัสผ่าน
        if (!Auth::attempt($validated)) {
            return response()->json([
                'status' => 'error',
                'message' => 'อีเมลล์หรือรหัสผ่านไม่ถูกต้อง'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // สร้าง Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

}