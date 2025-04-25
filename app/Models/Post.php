<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title','content','path_file','description','post_type','data_status','created_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
