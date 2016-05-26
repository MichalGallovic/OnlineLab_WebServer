<?php namespace Modules\Forum\Http\Controllers;

use App\Events\CommentAdded;
use Modules\Forum\Entities\ForumCategory;
use Modules\Forum\Entities\ForumComment;
use Modules\Forum\Entities\ForumGroup;
use Modules\Forum\Entities\ForumThread;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Auth;
use Session;
use Mail;

class ForumController extends Controller {
	
	public function index()
	{
		$groups = ForumGroup::all();
		return view('forum::index', compact('groups'));
	}

	public function deleteGroup($id){
		$group = ForumGroup::findOrFail($id);

		$group->delete();

		Session::flash('flash_message', 'Group successfully deleted!');

		return redirect('forum');
	}

	public function category($id){
		$category = ForumCategory::find($id);

		if($category==null){
			return view('forum::index')->with('fail', "This category does not exist.");
		}
		return view('forum::category', compact('category'));
	}

	public function deleteCategory($id){
		$category = ForumCategory::findOrFail($id);
		$category->delete();
		Session::flash('flash_message', 'Category successfully deleted!');
		return redirect('forum');
		//Todo prerobiť ako thread
	}

	public  function  deleteThread($id){
		$thread = ForumThread::findOrFail($id);
		$category_id = $thread->category_id;

		if($thread->delete()){
			return redirect()->route('forum.category', $category_id)->with('success', "The thread was deleted.");
		}else{
			return redirect('forum.thread', $thread->id)->with('fail', "An error occured while deleting the thread.");
		}
	}

	public function thread($id){
		$thread = ForumThread::find($id);
		$comments = ForumComment::where('thread_id', $id)->with('user')->paginate(10);
		if($thread == null){
			return redirect()->route('forum.index')->with('fail', "That thread does not exist.");
		}
		return view('forum::thread', compact('thread', 'comments'));
	}

	public function storeCategory($groupId){
		$validator = Validator::make(Input::all(), array(
			'category_name' => 'required|unique:forum_categories,title'
		));
		if ($validator->fails())
		{
			return redirect()->route('forum.index')->withInput()->withErrors($validator)->with('modal', '#category_modal_'.$groupId)->with('group-id', $groupId);
		}
		else
		{
			$group = ForumGroup::find($groupId);
			if ($group == null)
			{
				return redirect()->route('forum.index')->with('fail', "That group doesn't exist.");
			}
			$category = new ForumCategory;
			$category->title = Input::get('category_name');
			$category->user_id = Auth::user()->user->id;
			$category->group_id = $groupId;
			if($category->save())
			{
				return redirect()->route('forum.index')->with('success', 'The category was created');
			}
			else
			{
				return redirect()->route('forum.index')->with('fail', 'An error occured while saving the new category.');
			}
		}
	}

	public function storeGroup(){
		$validator = Validator::make(Input::all(), array(
			'group_name' => 'required|unique:forum_groups,title'
		));

		if($validator->fails()){
			return redirect()->route('forum.index')->withInput()->withErrors($validator)->with('modal', '#group_form');
		}
		else{
			$group = new ForumGroup;
			$group->title = Input::get('group_name');
			$group->user_id = Auth::user()->user->id;

			if($group->save()){
				return redirect()->route('forum.index')->with('success', 'The group was created');
			}else{
				return redirect()->route('forum.index')->with('fail', 'An error occured while saving the group');
			}
		}
	}

	public function newThread($category_id){
		return view('forum::newthread', compact('category_id'));
	}

	public function storeComment($thread_id){
		$validator = Validator::make(Input::all(), array(
			'body' => 'required|min:5'
		));

		if($validator->fails()){
			return redirect()->route('forum.thread', $thread_id)->withErrors($validator)->with('fail', "Please fill in the form correctly");
		}else{
			$comment = new ForumComment;
			$comment->body = Input::get('body');
			$comment->user_id = Auth::user()->user->id;
			$comment->thread_id = $thread_id;

			if($comment->save()){

				$thread = ForumThread::find($thread_id);

				event(new CommentAdded(Auth::user()->user->getFullName(),$thread->title, $thread->id));

				foreach($thread->user->accounts as $account){
					if($account->notify) {
						/*Mail::send('forum::email', ['thread' => $thread, 'user_name' => Auth::user()->user->name,], function($message) use ($account) {
							$message->to($account->email)->subject('Pribudol komentár');
						});*/
					}
				}
				return redirect()->route('forum.thread', $thread_id)->with('success', "The comment was saved.");
			}else{
				return redirect()->route('forum.thread', $thread_id)->with('fail', "An error occured while saving the comment.");
			}
		}



	}


	public function storeThread($category_id){
		$category = ForumCategory::find($category_id);
		if($category == null){
			return redirect()->route('forum.thread')->with('fail', "Category does not exist.");
		}
		$validator = Validator::make(Input::all(), array(
			'title' => 'required|min:3|max:255',
			'body' => 'required|min:10|max:65000'
		));

		if($validator->fails()){
			return redirect()->route('forum.new.thread', $category_id)->withErrors($validator)->with('fail', "Input does not match requirements");
		}else{
			$thread = new ForumThread;
			$thread->title = Input::get('title');
			$thread->body = Input::get('body');
			$thread->category_id = $category_id;
			$thread->user_id = Auth::user()->user->id;

			if($thread->save()){
				return redirect()->route('forum.thread', $thread->id)->with('success', 'Thread has been saved.');
			}else{
				return redirect()->route('forum.new.thread', $category_id)->with('fail', 'An error has occured while saving the thread');
			}
		}
	}

	public function deleteComment($id){
		$comment = ForumComment::find($id);
		$thread_id = $comment->thread_id;
		if($comment->delete()){
			return redirect()->route('forum.thread', $thread_id)->with('success', 'The comment was deleted.');
		}else{
			return redirect()->route('forum.thread', $thread_id)->with('fail', 'An error occured while deleting the comment.');
		}
	}
}