<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageesController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\ConversationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->group(function(){
    Route::get('conversations', [ ConversationController::class ,'index']);
    Route::get('conversations/{conversation}', [ ConversationController::class ,'show']);
    Route::post('conversations/{conversation}/participants', [ ConversationController::class ,'addparticipant']);
    Route::delete('conversations/{conversation}/participants', [ ConversationController::class ,'removeparticipant']);
    Route::get('conversation/{id}/messages', [ MessageesController::class ,'index']);
    Route::post('messages', [ MessageesController::class ,'store'])->name('api.messages.store');
    Route::delete('messages/{id}', [ MessageesController::class ,'destroy']);
    Route::get('friends/{id}',[MessengerController::class,'friends']);


 });
 Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});