<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Recipients extends Pivot
{
    use HasFactory;
    public $timestamps= false ;
    protected $casts = [
        'read_at'=> 'datetime'    ];

        public function messege(){
            return $this->belongsTo(Message::class);
        }

        public function user(){
            return $this->belongsTo(User::class);
        }

}
