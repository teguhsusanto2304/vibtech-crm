<?php

// app/Models/PostUpdateLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostUpdateLog extends Model
{
    protected $fillable = ['post_id', 'updated_by', 'changes'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

