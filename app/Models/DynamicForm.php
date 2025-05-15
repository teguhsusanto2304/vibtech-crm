<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'form_structure',
    ];

    protected $casts = [
        'form_structure' => 'array', // auto-cast JSON to array
    ];
}
