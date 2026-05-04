<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CooperativeRequestController;

// Routes สำหรับ Authentication (ไม่ต้องใช้ Token ในการเรียก)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'role:public'])->group(function () {
    
    // ดูรายการคำขอของตัวเอง
    Route::get('/coop-requests', [CooperativeRequestController::class, 'index']);
    
    // ยื่นคำขอใหม่
    Route::post('/coop-requests', [CooperativeRequestController::class, 'store']);
    
});

Route::middleware(['auth:sanctum', 'role:staff'])->group(function () {
    
    // เส้นทางสำหรับดูคำขอทั้งหมด
    Route::get('/admin/coop-requests', [CooperativeRequestController::class, 'allRequests']);
    
    // เส้นทางสำหรับ Review (Approve/Reject)
    Route::patch('/admin/coop-requests/{id}/review', [CooperativeRequestController::class, 'review']);
    
});