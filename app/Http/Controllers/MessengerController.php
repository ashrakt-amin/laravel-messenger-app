<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function index($id =null){
        $user = Auth::user();
        $friends = User::where('id' , '<>' ,$user->id )
                         ->orderBy('name')
                         ->get();
                         
                         $chats=$user->conversations()->with([
                            'lastMessage',
                            'participants'=>function($builder)use($user){
                               $builder->where('id','<>',$user->id);
                            }
                        ])->get();

                        //dd($chats);
                        //dd($id);
        
        $messeges =[];
        $activechat = new Conversation();
        //dd($activechat);
        if($id){
            $activechat = $chats->where('id',$id)->first();
            //dd($activechat);
            $messeges =$activechat->messages()->with('user')->get();

        }

        //dd($chats);                 

        return view('messenger' ,[
            'friends'    => $friends ,
            'chats'      => $chats ,
            'messeges'   => $messeges,
            'activechat' => $activechat,
        ]);
    }

    public function friends(){

        $user=Auth::user();
        $friends=User::where('id','<>',$user->id)
            ->orderBy('name')
            ->get();
        return $friends;
    }
}
