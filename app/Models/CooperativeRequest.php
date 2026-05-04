<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CooperativeRequest extends Model
{
    use HasFactory;

    // อนุญาตให้บันทึกข้อมูลเหล่านี้ลง Database ได้
    protected $fillable = [
        'user_id',
        'name',
        'member_count',
        'status', 
        'remark'
    ];
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y',
            'updated_at' => 'datetime:d/m/Y',
        ];
    }  
     
}