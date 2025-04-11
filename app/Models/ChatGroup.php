<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_group_members');
    }

    public function members()
{
    return $this->hasMany('chat_group_members', 'chat_group_id');
}
}
