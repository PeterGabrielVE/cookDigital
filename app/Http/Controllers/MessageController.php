<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class MessageController extends Controller
{
    public function index()
    {
        $objUser = Auth::user();

        if($objUser->type != 'super admin' && $objUser->type != 'client')
        {
            if($objUser->type == 'company')
            {
                $users = User::where('created_by', '=', $objUser->creatorId())->where('type', '!=', 'client')->get();
            }
            elseif($objUser->type == 'employee')
            {
                $users = User::where('created_by', '=', $objUser->creatorId())->where('id', '!=', $objUser->id)->where('type', '!=', 'client')->orWhere('id', '=', $objUser->creatorId())->get();
            }
            else
            {
                $users = User::where('created_by', '=', $objUser->creatorId())->where('id', '!=', $objUser->id)->where('type', '!=', 'client')->orWhere('id', '=', $objUser->creatorId())->get();
            }

            return view('chats.index', compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getMessage($user_id)
    {
        $objUser = Auth::user();

        if($objUser->type != 'super admin' && $objUser->type != 'client')
        {
            $my_id = $objUser->id;

            // Make read all unread message
            Message::where(
                [
                    'from' => $user_id,
                    'to' => $my_id,
                ]
            )->update(['is_read' => 1]);

            // Get all message from selected user
            $messages = Message::where(
                function ($query) use ($user_id, $my_id){
                    $query->where('from', $user_id)->where('to', $my_id);
                }
            )->oRwhere(
                function ($query) use ($user_id, $my_id){
                    $query->where('from', $my_id)->where('to', $user_id);
                }
            )->get();

            return view('chats.message', ['messages' => $messages]);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function sendMessage(Request $request)
    {
        $objUser = Auth::user();

        if($objUser->type != 'super admin' && $objUser->type != 'client')
        {
            $from    = $objUser->id;
            $to      = $request->receiver_id;
            $message = $request->message;

            $data          = new Message();
            $data->from    = $from;
            $data->to      = $to;
            $data->message = $message;
            $data->is_read = 0; // message will be unread when sending message
            $data->save();

            // pusher
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            );

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options
            );

            $data = [
                'from' => $from,
                'to' => $to,
            ]; // sending from and to user id when pressed enter
            $pusher->trigger('my-channel', 'my-chat', $data);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getMessagePopup()
    {
        $user     = Auth::user();
        $messages = Message::whereIn(
            'id', function ($query) use ($user){
            $query->select(\DB::raw('MAX(id)'))->from('messages')->where('to', '=', $user->id)->where('is_read', '=', 0);
        }
        )->orderBy('id', 'desc')->get();

        return view('chats.popup', compact('messages'));
    }

    public function messageSeen()
    {
        $user = Auth::user();
        Message::where('to', '=', $user->id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }
}
