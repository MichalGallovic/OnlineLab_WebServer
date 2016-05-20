<?php namespace Modules\Chat\Http\Controllers;

use App\Events\MemberAdded;
use App\Events\UserSignedUp;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Entities\Chatroom;
use Modules\Chat\Entities\Message;
use Modules\Chat\Entities\Permission;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;

class ChatController extends Controller {
	
	public function index()
	{

		$user = Auth::user()->user;
		$user_id = $user->id;
		$publicChatrooms = Chatroom::where('type', 'public')->get();
		$myChatrooms = $user->chatrooms;
		//$myChatrooms = Chatroom::where('type', 'private')->get();

		$words = [];
		$tagCloud = [];

		foreach (Message::lists('body') as $message) {
			foreach (explode(" ",$message) as $word){
				$key = str_replace(array(':', '\\', '/', '*', ',', '.'), ' ', $word);
				if(strlen($key) > 4){
					if(array_key_exists($key, $words)){
						$words[$key]++;
					}else{
						$words[$key] = 1;
					}
				}
			}
		}

		foreach ($words as $word=>$weight) {
			array_push($tagCloud, ['text' => $word, 'weight' => $weight]);
		}

		usort($tagCloud, function($a, $b)
		{
			if ($a==$b) return 0;
			return ($a['weight']<$b['weight'])?1:-1;
		});

		$tagCloud = array_slice($tagCloud,0,40);


		return view('chat::index', compact('publicChatrooms', 'myChatrooms', 'user_id', 'tagCloud'));
	}

	public function chatroom($id){
		$room = Chatroom::with('users')->find($id);
		$user = Auth::user()->user;
		$room_id = $id;
		$title = $room->title;
		$user_id = $user->id;
		$user_name = $user->getFullName();

		$members = [];
		foreach ($room->users as $member) {
			$members[$member->id] = $member->getFullName();
		}
		//event(new UserSignedUp(Auth::user(), $id));

		$perm = Permission::where(['user_id' => $user->id, 'chatroom_id' => $id])->get();

		$publicChatroomJoin = false;

		if(count($perm)==0) {
			if ($room->type == 'public') {
				Permission::create(['user_id' => $user->id, 'chatroom_id' => $id, 'type' => 'member']);
				$publicChatroomJoin = true;
			}
		}

		$messages = Message::with('user')->where('chatroom_id', $id)->get();
		return view('chat::chatroom', compact('user_id', 'user_name', 'room', 'members', 'messages', 'publicChatroomJoin'));
	}

	public function findUsers(Request $request){
		$users = User::select('id', DB::raw('CONCAT(name, " ", surname) AS text'))
		->whereDoesntHave('chatrooms', function($query) use ($request){
			$query->where('id', $request->chatroom);
		})
		->where('name', 'like', $request->q.'%')->orWhere('surname', 'like', $request->q.'%')
		->get();
		return  response()->json(['items'=>$users]);
	}

	public function addUser(Request $request){
		$members = [];

		foreach ($request->users as $user) {
			$permission = new Permission();
			$permission->user_id = $user;
			$permission->chatroom_id = $request->chatroom;
			$permission->type = 'member';
			$permission->save();
			$members[$user] = User::find($user)->getFullName();

			event(new MemberAdded($user, $members[$user], Auth::user()->user->getFullName(), Chatroom::find($request->chatroom)->title, $request->chatroom));
		}

		return response()->json($request->chatroom);
	}

	public function storeChatroom(Request $request){
		$validator = Validator::make($request->all(), array(
			'title' => 'required|min:3'
		));

		if ($validator->fails()) {
			return redirect()->route('chat.index')
				->withInput()
				->withErrors($validator)
				->with('modal', '#chatroom_modal');
		} else {

			$chatroom = new Chatroom();
			$chatroom->title = $request->title;
			$chatroom->type = $request->type;

			if($chatroom->save()) {
				$permission = new Permission();
				$permission->user()->associate(Auth::user()->user);
				$permission->chatroom()->associate($chatroom);
				$permission->type = 'creator';
				$permission->save();
				return redirect()->route('chat.index')->with('success', trans('chat::default.CHAT_CREATED_SUCCESS'));
			} else {
				return redirect()->route('chat.index')
					->withInput()
					->with('fail', trans('chat::default.CHAT_CREATE_FAIL'));
			}
		}
	}

	public function joinVideo($id){

		return view('chat::video', compact('id'));
	}

	public function createVideo(Request $request){
		$validator = Validator::make($request->all(), array(
			'title' => 'required|min:3',
			'invite' => 'required'
		));

		if ($validator->fails()) {
			return redirect()->route('chat.index')
				->withInput()
				->withErrors($validator)
				->with('modal', '#video_modal');
		} else {
/*
			$chatroom = new Chatroom();
			$chatroom->title = $request->title;
			$chatroom->type = $request->type;
*/
		}
			$room = $request->title;
		$invite = $request->invite;

		$addedUserName = User::find($invite)->getFullName();
		$id = substr($this->base64url_encode(mt_rand()),0,8);
		event(new MemberAdded($invite, $addedUserName, Auth::user()->user->getFullName(), $room, $id, true));

		return redirect()->route('chat.video', [$id])->with('caller', true);
		//return view('chat::video', compact('isInit', 'room', 'invite'));
	}

	private function base64url_encode($data, $pad = null) {
		$data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
		if (!$pad) {
			$data = rtrim($data, '=');
		}
		return $data;
	}
}