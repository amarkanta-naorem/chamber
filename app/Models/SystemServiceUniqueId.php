<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemServiceUniqueId extends Model
{
    use HasFactory;
    protected $table = 'system_service_unique_ids';
    protected $fillable = ['unique_id'];
}
