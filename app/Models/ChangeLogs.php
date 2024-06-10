<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'action_name',
        'row_id',
        'value_before',
        'value_after',
        'created_by',
    ];
}
