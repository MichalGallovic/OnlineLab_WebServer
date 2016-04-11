<?php namespace Modules\Users\Http\Controllers;

use App\User;
use Pingpong\Modules\Routing\Controller;
use Session;
use Request;

class UsersController extends Controller {
	
	public function index()
	{
		$users = User::all();
		return view('users::index' , compact('users'));
	}

	public function edit($id)
	{
		$user=User::find($id);
		return view('users::edit',compact('user'));
	}

	public function update($id)
	{
		$userUpdate=Request::all();
		$user=User::find($id);
		$user->update($userUpdate);
		return redirect('users');
	}

	public function show($id)
	{
		$user=User::find($id);
		return view('users::show',compact('user'));
	}

	public function destroy($id)
	{
		$user = User::findOrFail($id);

		$user->delete();

		Session::flash('flash_message', 'User successfully deleted!');

		return redirect('users');
	}
	
}