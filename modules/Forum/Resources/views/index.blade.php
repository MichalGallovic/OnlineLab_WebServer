@extends('user.layouts.default')

@section('content')



	@foreach($groups as $group)
		<div class="panel panel-primary">
			<div class="panel-heading">

				<div class="clearfix">
					<h3 class="panel-title pull-left">{!! $group->title !!}</h3>
					@if(Auth::user()->user->isAdmin())
					<a href="#" id="add-category-{!! $group->id  !!}" class="btn btn-success btn-xs pull-right new_category" data-toggle="modal" data-target="#category_modal_{!! $group->id !!}">New category</a>
					{!! Form::open(['method' => 'DELETE', 'route'=>['forum.delete.group', $group->id]]) !!}
					{!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs pull-right']) !!}
					{!! Form::close() !!}
					@endif
				</div>
			</div>
			<div class="panel-body breaker">
				<div class="list-group breaker">
					@foreach($group->categories as $category)
						<a href="{!! URL::route('forum.category', $category->id) !!}" class="list-group-item">{!! $category->title !!}</a>
					@endforeach
				</div>
			</div>
		</div>

		<div class="modal fade" id="category_modal_{!! $group->id !!}" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true"&times;></span>
							<span class="sr-only">Close</span>
						</button>
						<h4 class="modal-title">New Category</h4>
					</div>
					<div class="modal-body">
						{!! Form::open(array('id' => 'category_form_'.$group->id, 'method' => 'post', 'route' => array('forum.store.category', $group->id))) !!}
						<div class="form-group {!! ($errors->has('category_name')) ? 'has-error' : '' !!}">
							<label for="category_name">Category Name:</label>
							<input type="text" id="category_name" name="category_name" class="form-control">
							@if($errors->has('category_name'))
								<p>{!! $errors->first('category_name') !!}</p>
							@endif
						</div>
						{!! Form::token() !!}
						{!! Form::close() !!}
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						{!! Form::submit('Save', array('form' => 'category_form_'.$group->id, 'class' => 'btn btn-primary')) !!}
						<!--<button type="button" form="category_form_{!! $group->id !!}" class="btn btn-primary" data-dismiss="modal" id="category_submit">Save</button>-->
					</div>
				</div>
			</div>
		</div>
	@endforeach

	@if(Auth::check() && Auth::user()->user->isAdmin())
		<div class="modal fade" id="group_form" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true"&times;></span>
							<span class="sr-only">Close</span>
						</button>
						<h4 class="modal-title">New Group</h4>
					</div>
					<div class="modal-body">
						{!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'forum.store.group')) !!}
							<div class="form-group {!! ($errors->has('group_name')) ? 'has-error' : '' !!}">
								<label for="group_name">Group Name:</label>
								<input type="text" id="group_name" name="group_name" class="form-control">
								@if($errors->has('group_name'))
									<p>{!! $errors->first('group_name') !!}</p>
								@endif
							</div>
							{!! Form::token() !!}
						{!! Form::close() !!}
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						{!! Form::submit('Save', array('form' => 'target_form', 'class' => 'btn btn-primary')) !!}
					</div>
				</div>
			</div>
		</div>
	@endif

	@if(Auth::check() && Auth::user()->user->isAdmin())
		<div>
			<a href="#" class="btn btn-default" data-toggle="modal" data-target="#group_form">Add group</a>
		</div>
	@endif

@stop

@section('page_js')
	@parent
	@if(Session::has('modal'))
	<script type="text/javascript">
		$("{!! Session::get('modal') !!}").modal('show');
	</script>
	@endif
@stop