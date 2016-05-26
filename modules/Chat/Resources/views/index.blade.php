@extends('user.layouts.default')

@section('content')

	<div class="row">
		<div id="tag-cloud" class="center-block" style="height: 260px; width:50%"></div>
	</div>



	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">{{trans("chat::default.CHAT_ROOMS_MY")}}
				<a href="#"  class="btn btn-success btn-xs pull-right new-chatroom" data-toggle="modal" data-target="#chatroom_modal">{{trans("chat::default.CHAT_ROOMS_ADD")}}</a>
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

	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">{{trans("chat::default.CHAT_ROOMS_PUBLIC")}}</div>
			<div class="panel-body">
				<div class="list-group">
					@foreach ($publicChatrooms as $chatroom)
						<a href="{{route('chat.chatroom',$chatroom->id)}}" class="list-group-item">{{$chatroom->title}}</a>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">{{trans("chat::default.CHAT_ROOMS_VIDEO")}}
				<a href="#"  class="btn btn-success btn-xs pull-right new-chatroom" data-toggle="modal" data-target="#video_modal">{{trans("chat::default.CHAT_ROOMS_VIDEO_ADD")}}</a></div>
			<div class="panel-body">
				<div id="video-chat-list" class="list-group"></div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="video_modal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true"&times;></span>
						<span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title">{{trans("chat::default.CHAT_ROOMS_VIDEO_ADD")}}</h4>
				</div>
				<div class="modal-body">
					{!! Form::open(array('id' => 'video_form', 'method' => 'post', 'route' => 'chat.new.video')) !!}
					<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
						{!! Form::label('title', trans("chat::default.CHAT_ROOM_TITLE")) !!}
						{!! Form::text('title', '', array('class' => 'form-control')) !!}
						@if($errors->has('title'))
							<p>{!! $errors->first('title') !!}</p>
						@endif
					</div>
					<div class="form-group" {!! ($errors->has('invite')) ? 'has-error' : '' !!}>
						<select name="invite" id="search-box" class="form-control"></select>

						@if($errors->has('invite'))
							<p>{!! $errors->first('invite') !!}</p>
						@endif
					</div><!-- /input-group -->
					{!! Form::close() !!}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					{!! Form::submit('Save', array('form' => 'video_form', 'class' => 'btn btn-primary')) !!}
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
						<span class="sr-only">{{trans("chat::default.CHAT_CLOSE")}}</span>
					</button>
					<h4 class="modal-title">{{trans("chat::default.CHAT_ROOMS_ADD")}}</h4>
				</div>
				<div class="modal-body">
					{!! Form::open(array('id' => 'target_form', 'method' => 'post', 'route' => 'chat.new.chatroom')) !!}
					<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
						{!! Form::label('title', trans("chat::default.CHAT_ROOM_TITLE")) !!}
						{!! Form::text('title', '', array('class' => 'form-control')) !!}
						@if($errors->has('title'))
							<p>{!! $errors->first('title') !!}</p>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('type', trans("chat::default.CHAT_ROOM_ACCESIBILITY")) !!}
						{!! Form::select('type', array('private' => trans("chat::default.CHAT_ROOM_PRIVATE"), 'public' => trans("chat::default.CHAT_ROOM_PUBLIC")), 'private', array('class' => 'form-control')) !!}
					</div>
					{!! Form::close() !!}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{trans("chat::default.CHAT_CLOSE")}}</button>
					{!! Form::submit(trans("chat::default.CHAT_SAVE"), array('form' => 'target_form', 'class' => 'btn btn-primary')) !!}
				</div>
			</div>
		</div>
	</div>
@stop

@section("page_css")
	@parent
	<link rel="stylesheet" type="text/css" href="{{ asset('css/statistics/jqcloud.css') }}">
@stop

@section('page_js')
	@parent
	<script type="text/javascript" src="{{ asset('js/chat/select2.full.min.js') }}"></script>
	<script src="{{ asset('js/statistics/jqcloud-1.0.4.min.js') }}"></script>
	<script type="text/javascript">
		@if(Session::has('modal'))
			$("{!! Session::get('modal') !!}").modal('show');
		@endif

		$(document).ready(function(){

			var word_list = {!! json_encode($tagCloud) !!};
			$(function() {
				$("#tag-cloud").jQCloud(word_list, {autoResize: true});
			});

			socket.on('connect', function(){
				socket.emit('getVideoChatrooms', {user_id: {{$user_id}}});
			});

			socket.on('updateVideoRooms{{$user_id}}', function(data){
				//data = JSON.parse(data);
				console.log(data);
				$("#video-chat-list").empty();

				for (var key in data){
					$("#video-chat-list").append($("<a>", {
						href: 'chat/video/' + key,
						class: "list-group-item",
						text: data[key]
					}));
				}
			});

			$("#search-box").select2({
				width: "100%",
				ajax: {
					url: "chat/findUsers",
					dataType: "JSON",
					delay: 250,
					data: function (params) {
						return {
							q: params.term, // search term
							page: params.page
						};
					},
					processResults: function (data, params) {
						params.page = params.page || 1;
						return {
							results: data.items,
							pagination: {
								more: (params.page * 30) < data.total_count
							}
						};
					},
					cache: true
				},
				escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				minimumInputLength: 1
			});
		});
	</script>
@stop
