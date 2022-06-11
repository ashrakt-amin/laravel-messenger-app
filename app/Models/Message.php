<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'conversation_id', 'user_id', 'body', 'type',
    ];


    public function user(){
        return $this->belongsTo(User::class)->withDefault([
            "name" => __("user")
        ]);

    }

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function recipients(){
        // relation with pivot will return only forien keys so we use withPivot
        return $this->belongsToMany(User::class,'recipients')->withPivot([
            'read_at','deleted_at',
        ]);
    }
}
