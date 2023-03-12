<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutlineClos extends Model
{
    use HasFactory;
    protected $table = "OutlineClos";
    protected $fillable = array('OutlineClosId', 'objId', 'OutlineId','added_by','created_at', 'updated_at');

}
