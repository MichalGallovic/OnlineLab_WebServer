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

class ChatController extends Controller {
	
	public function index()
	{

		$user = Auth::user()->user;
		$user_id = $user->id;
		$publicChatrooms = Chatroom::whereIn('type', ['public_open', 'public_closed'])->get();
		$myChatrooms = $user->chatrooms;
		//$myChatrooms = Chatroom::where('type', 'private')->get();

		return view('chat::index', compact('publicChatrooms', 'myChatrooms', 'user_id'));
	}

	public function chatroom($id){
		$room = Chatroom::with('users')->find($id);
		$user = Auth::user()->user;
		$room_id = $id;
		$title = $room->title;
		$user_id = $user->id;
		$user_name = $user->getFullName();

		$members = [];
		foreach ($room->users as $user) {
			$members[$user->id] = $user->getFullName();
		}
		//event(new UserSignedUp(Auth::user(), $id));

		$perm = Permission::where(['user_id' => $user->id, 'chatroom_id' => $id])->get();


		if(count($perm)==0){
			if($room->type == 'public_open'){
				Permission::create(['user_id' => $user->id, 'chatroom_id' => $id, 'type' => 'member']);
			}else if($room->type == 'public_closed'){
				Permission::create(['user_id' => $user->id, 'chatroom_id' => $id, 'type' => 'spectator']);
			}
		}

		$messages = Message::with('user')->where('chatroom_id', $id)->get();
		return view('chat::chatroom', compact('user_id', 'user_name', 'room', 'members', 'messages'));
	}

	public function findUsers(Request $request){
		$users = User::where(function($query) use ($request){
			$query->where('name', 'like', $request->q.'%')->orWhere('surname', 'like', $request->q.'%');
		})->whereDoesntHave('chatrooms', function($query) use ($request){
			$query->where('id', $request->chatroom);
		})->get();
		$result = [];
		foreach ($users as $user) {
			array_push($result, ['id'=>$user->id, 'text'=>$user->getFullName()]);
		}
		return  response()->json(['items'=>$result]);
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
		return response()->json($members);
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
}

//stanik 33
//upload suboru mdl, x, mo
//upload naparsovania