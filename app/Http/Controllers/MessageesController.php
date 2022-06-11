<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use Throwable;
use App\Models\User;
use App\Models\Recipients;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

class MessageesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index($id)
    {
        $user=Auth::user();
        $conversation=$user->conversations() //return conversation of user
            ->with(['participants'=>function($builder)use($user){
                $builder->where('id','<>',$user->id);
            }])
            ->find($id);

       return [
           'conversation'=> $conversation,
           'messages'=> $conversation->messages()->with('user')->paginate()
        ];

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'body'=>['required','string'],
            'conversation_id'=>[
                 Rule::requiredIf( function () use ($request){
                    return !$request->input('user_id');
                   }),'int', 'exists:conversations,id'],

            'user_id'=>[
                Rule::requiredIf(function () use ($request) {
                    return !$request->input('conversation_id');
                }),'int','exists:users,id']
        ]);

        $user = Auth::user();

        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');

        

        DB::beginTransaction();
        try{
            if($conversation_id){
                $conversation =$user->conversations()->findOrFail($conversation_id);
              }else{
                   // user sent and user received 
                   // must define that conversation type not group
                  $conversation = Conversation::where('type','=','peer')
                  ->whereHas('participants', function ($builder)use($user_id , $user){
                     $builder->join('participants as participants2','participants2.conversation_id','=','participants.conversation_id')
                             ->where('participants.user_id' ,'=',$user_id)
                             ->where('participants2.user_id' ,'=',$user->id);
    
                })->first();
                
                // 1) make conversation if not exist
                // 2) add users to participants
                // 3) add message in conversation
                // 4) add recipients for this message
    
                if(!$conversation){
                    $conversation = Conversation::create([
                        'user_id'=>$user->id,
                        'type'=>'peer'
                    ]);
               
                    // create participants table
                    $conversation->participants()->attach([
                        $user_id => ['joined_at'=> now()],
                        $user->id =>['joined_at'=> now()]
                    ]);
                }
              } 

                 // create messages table
                 $message = $conversation->messages()->create([
                     'user_id'=>$user->id,
                     'body'=>$request->post('body')
                    ]);
    //   ناتج السيليكت هو اللى هيتعمله انسيرت والطريقه دى اسرع من ماستخدم الموديل  
    //   امرر القيم بترتيب علامه الاستفهام

                     // create recipients table
               DB::statement('INSERT INTO recipients (user_id , message_id) 
                       SELECT user_id ,? FROM participants
                       WHERE conversation_id =? ',[
                       $message->id ,$conversation->id
                       ]);

            $conversation->update([
                'last_message_id'=>$message->id ,
            ]);
                       // if success make commit
                       DB::commit();
                       $message->load('user');
                       broadcast(new MessageCreated($message));

                    }catch(Throwable $e){
                        DB::rollBack();
                        throw $e ;
                    }
                    return $message;
        }

       

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Recipients::where([
            'user_id'   =>Auth::user()->id,
            'message_id'=>$id
        ])->delete();
        return([
            'message'=>'deleted',
        ]);
    }
}
