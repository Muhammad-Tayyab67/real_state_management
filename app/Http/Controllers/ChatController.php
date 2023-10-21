<?php

namespace App\Http\Controllers;
use App\Models\{ChatRoom, Message};
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    //Send New Message
    public function sendMessage(Request $request)
    {
        // Validating request data
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|string|max:255|exists:users,id',
            'message' => 'required|string|max:255',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //check if the receiver is a valid user
        if(auth()->user()->id == $request->receiver_id){
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'You cannot send message to yourself',
            ]);
        }

        //check if the chat room already exists
        $chatRoom = ChatRoom::where('participant_1', auth()->user()->id)
            ->where('participant_2', $request->receiver_id)
            ->orWhere('participant_1', $request->receiver_id)
            ->where('participant_2', auth()->user()->id)
            ->first();

        if($chatRoom){
            //store the message
            $message = Message::create([
                'chat_room_id' => $chatRoom->id,
                'message' => $request->message,
                'sender_id' => auth()->user()->id,
            ]);

            //return success message
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message,
            ]);
        }

        //create new chat room
        $chatRoom = ChatRoom::create([
            'name' => 'Chat Room',
            'participant_1' => auth()->user()->id,
            'participant_2' => $request->receiver_id,
        ]);

        //store the message
        $message = Message::create([
            'chat_room_id' => $chatRoom->id,
            'message' => $request->message,
            'sender_id' => auth()->user()->id,
        ]);

        //return success message
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }

    //Get All Chat Rooms
    public function getChatRooms()
    {
        //get all chat rooms
        $chatRooms = ChatRoom::where('participant_1', auth()->user()->id)
            ->orWhere('participant_2', auth()->user()->id)
            ->get();

        //return success message
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Chat Rooms fetched successfully',
            'data' => $chatRooms,
        ]);
    }

    //Get All Messages of a Chat Room
    public function getChatRoomMessages($chatRoomId)
    {
        // Validating request data
        $validator = Validator::make(['chat_room_id' => $chatRoomId], [
            'chat_room_id' => 'required|integer|exists:chat_rooms,id',
        ]);

        // returning error message on validation fail
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //get all messages of a chat room
        $messages = Message::where('chat_room_id', $chatRoomId)->get();

        //return success message
        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => 'Messages fetched successfully',
            'data' => $messages,
        ]);
    }
}
