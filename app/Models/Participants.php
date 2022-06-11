<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Participants extends Pivot
{
    use HasFactory;
    public $timestamps= false ;
    // casts will Returns coulms with certain data type 
    protected $casts = [
        'joined_at'=> 'datetime'];

        public function conversation(){
            return $this->belongsTo(Conversation::class);
        }

        public function user(){
            return $this->belongsTo(User::class);
        }
}
