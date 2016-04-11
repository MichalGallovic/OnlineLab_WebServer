@extends('user.layouts.default')

@section('content')

	<h1>Hello World</h1>



	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">My Chatrooms
				<a href="#"  class="btn btn-success btn-xs pull-right new-chatroom" data-toggle="modal" data-target="#chatroom_modal">New chatroom</a>
			</div>
			<div class="panel-body">
				<div class="list-group">
					@foreach ($myChatrooms as $chatroom)
						<a href="{{route('chat.chatroom',$chatroom->id)}}" class="list-group-item">{{$chatroom->title}}</a>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">Public Chatrooms</div>
			<div class="panel-body">
				<div class="list-group">
					@foreach ($publicChatrooms as $chatroom)
						<a href="{{route('chat.chatroom',$chatroom->id)}}" class="list-group-item">{{$chatroom->title}}<span class="label {{$chatroom->canPost($user_id) ? 'label-primary' : 'label-warning'}} label-as-badge pull-right">   </span></a>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="chatroom_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true"&times;></span>
						<span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title">New Chatroom</h4>
				</div>
				<div class="modal-body">
					{!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'chat.new.chatroom')) !!}
					<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
						{!! Form::label('title', 'Chatroom title') !!}
						{!! Form::text('title', '', array('class' => 'form-control')) !!}
						@if($errors->has('title'))
							<p>{!! $errors->first('title') !!}</p>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('type', 'Accesibility') !!}
						{!! Form::select('type', array('private' => 'Private', 'public_open' => 'Public (open)', 'pulic_closed' => 'Public (closed)'), 'private', array('class' => 'form-control')) !!}
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
@stop

@section('page_js')
	@parent
	@if(Session::has('modal'))
		<script type="text/javascript">
			$("{!! Session::get('modal') !!}").modal('show');
		</script>
	@endif
@stop
