<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\UserNotification;
use Auth;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title','content','path_file','description','post_type','data_status','created_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(PostUpdateLog::class);
    }


    protected static function booted()
    {
        static::created(function ($post) {
            // Choose the users to notify (e.g., all admins or all users)
            $users = \App\Models\User::whereNot('id', $post->created_by)->get(); // example
            foreach($users as $user){
                if($post->post_type==MEMO){
                    $user->notify(new UserNotification(
                        'There is a new management memo. Click to read memo. ' . $post->title,
                        'accept',
                        route('v1.management-memo.read', ['id' => $post->id])
                    ));
                } else if($post->post_type==HANDBOOK){
                    $user->notify(new UserNotification(
                        'A new Employee Handbook has been uploaded. Click to read handbook. ' . $post->title,
                        'accept',
                        route('v1.employee-handbooks.read', ['id' => $post->id])
                    ));
                }

            }

        });

        static::updated(function ($post) {

            if($post->post_type==MEMO){
                $changes = $post->getChanges();
                unset($changes['updated_at']); // ignore updated_at if not needed

                if (!empty($changes)) {
                    PostUpdateLog::create([
                            'post_id' => $post->id,
                            'updated_by' => Auth::id(),
                            'changes' => $changes['content'],
                        ]);
                }
            }
            // Choose the users to notify (e.g., all admins or all users)
            $users = \App\Models\User::whereNot('id', $post->created_by)->get(); // example
            foreach($users as $user){
                if($post->post_type==MEMO){
                    $user->notify(new UserNotification(
                        'An existing management memo has been updated. Click to read updated memo. ' . $post->title,
                        'accept',
                        route('v1.management-memo.read', ['id' => $post->id])
                    ));
                } else if($post->post_type==HANDBOOK){
                    $user->notify(new UserNotification(
                        'Employee Handbook - An existing employee handbook has been updated. Click to read updated handbook. ' . $post->title,
                        'accept',
                        route('v1.employee-handbooks.read', ['id' => $post->id])
                    ));
                }
            }

        });
    }
}
