<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'data_status'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function users1()
    {
        return $this->belongsToMany(User::class, 'chat_group_members');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'chat_group_id', 'user_id')
            ->withTimestamps(); // optional if you're tracking timestamps
    }

    public function members()
    {
        return $this->hasMany(ChatGroupMember::class, 'chat_group_id');
    }
}
