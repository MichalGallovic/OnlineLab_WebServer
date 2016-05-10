@extends('user.layouts.default')

@section('content')
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">{{$room->title}}</div>
            <div id="chat-panel-body" class="panel-body" style='max-height: 800px; overflow-y: auto;  overflow-x: hidden;'>
                @if($messages->count()>15)
                    <div id="old-messages-div" class="row">
                        <div class="col-md-4 col-md-offset-5">
                            <a id="old-messages" href="#">load older messages</a>
                        </div>
                    </div>
                @endif
                <ul id="chat_body" class="media-list" >

                    @foreach ($messages->slice(-15) as $message)
                        <li class="media">
                            <div class="media-left media-middle">
                                {!! Html::image('images/profile/' . $message->user->id, 'Generic placeholder image', ['class' => 'media-object','style' => 'max-height: 30px; max-width: 30px']) !!}
                            </div>
                            <div class="media-body">
                                <p>{{$message->body}}</p>
                                <small class="text-muted">{{$message->user->getFullName().' | '.$message->created_at}}</small>
                            </div>

                        </li>
                        <hr style="margin-top: 5px; margin-bottom: 5px">
                    @endforeach
                </ul>
            </div>
            <div class="panel-footer">
                <div class="input-group">
                    <input id="chat_text" type="text" class="form-control" placeholder="Enter message">
					<span class="input-group-btn">
						<button id="chat_send" class="btn btn-primary">
                            Send
                        </button>
					</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="panel panel-default ">
            <div class="panel-heading">
                <span>Chatroom members</span>
            </div>
            <div class="panel-body">
                <ul id="logged_users" class="list-group">
                </ul>
            </div>
            <div class="panel-footer">
                <div class="input-group select2-bootstrap-append">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <select id="search-box" class="form-control"></select>
                    <span class="input-group-btn">
                        <button id="addUser" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus" style="color:green"></span></button>
                    </span>
                    <div id="suggesstion-box">
                        <ul>

                        </ul>
                    </div>
                </div><!-- /input-group -->
            </div>
        </div>

        @if(Auth::user()->user->chatrooms->count()>1)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span>Other chatrooms</span>
                </div>
                <div class="panel-body">
                    <div id="other-chatrooms" class="list-group">
                        @foreach(Auth::user()->user->chatrooms as $chatroom)
                            @if($chatroom->id != $room->id)
                                <a href="{{route('chat.chatroom', $chatroom->id)}}" id="chatroom_id_{{$chatroom->id}}"class="list-group-item">{{$chatroom->title}}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>

@stop
@section('page_js')
    <script type="text/javascript">
        //var socket = io.connect();
        $(document).ready(function(){

            @if($openChatroomJoin)
                socket.emit('addMember', {user_id: {{$user_id}}, user_name: "{{$user_name}}"});
            @endif
            var members = {!!json_encode($members)!!};
            var messages = {!!json_encode($messages->reverse()->slice(15))!!};

            $("#chat-panel-body").animate({
                scrollTop:  $('#chat-panel-body').prop("scrollHeight")
            }, "slow");


            $("#old-messages").click(function(event) {
                event.preventDefault();
                for(var i = 0; i<20; i++){
                    $("#chat_body").prepend($('<hr>', {
                        css: {
                            'margin-top': '5px',
                            'margin-bottom': '5px'
                    }})).prepend(
                        $("<li>", {
                            class: "media"
                    }).append(
                        $("<div>", {
                            class: "media-left media-middle"
                        }).append(
                            $("<img>", {
                                class: "media-object",
                                height: "30px",
                                alt: "Generic placeholder image",
                                src: ROOT_PATH+"images/profile/"+messages[0].user.id
                            })
                        )
                    ).append(
                        $("<div>", {
                            class: "media-body"
                        }).append(
                            $("<p>", {
                                text: $('<div>').html(messages[0].body).text()
                            })
                        ).append(
                            $("<small>", {
                                class: "text-muted",
                                text: $('<div/>').html(messages[0].user.name + " " + messages[0].user.surname).text()+" | " + new Date(messages[0].created_at).toLocaleString()
                            })
                        )
                    ));
                    messages.shift();
                    if(messages.length==0){
                        $('#old-messages-div').remove();
                        break;
                    }
                }
            });

            $("#search-box").select2({
                width: "100%",
                ajax: {
                    type: "POST",
                    url: "findUsers",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "JSON",
                    delay: 250,
                    data: function (params) {
                       return {
                           q: params.term, // search term
                           page: params.page,
                           chatroom: {{$room->id}}
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

            $( "#chat_send" ).click(function() {
                if($( "#chat_text" ).val() !== ''){
                    socket.emit('sendChat', { body: $( "#chat_text" ).val() });
                    $( "#chat_text" ).val(null);
                }
            });

            $(' #addUser ').click(function(e) {
                var myData = $('#search-box').val();
                $('#search-box').select2('val', '');
                // alert(myData); //example: title=test&desc=something&_token=jhadskljhfaksjhfjksadhkfjh (just made up the token for the example)
                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    data: {
                        'users': myData,
                        'chatroom': {{$room->id}}
                    },
                    url: "addUser"
                }).success(function( data ) {
                    console.log(data);
                    for(var key in data){
                        socket.emit('addMember', {user_id: key, user_name: data[key]});
                    }
                });
            });

            $('#chat_text').keypress(function (e) {
                var key = e.which;
                if(key == 13)  // the enter key code
                {
                    $('#chat_send').click();
                    return false;
                }
            });

            socket.on('connect', function(){
                socket.emit('addUser', {user_id: {{$user_id}}, user_name: '{{ $user_name }}' , room:{{$room->id}}});
            });

            socket.on('updateUsers', function(data){
                //data = JSON.parse(data);
                console.log(data);
                $("#logged_users").empty();

                for(var key in members) {
                    $("#logged_users").append($("<li>", {
                        id: 'user_id_'+key,
                        class: "list-group-item",
                        text: $('<div/>').html(members[key]).text()
                    }).append($('<span>', {
                        class: "label label-as-badge pull-right "+(data[key] ? "label-success" : "label-default"),
                        text: " "
                    })));
                }
            });

            socket.on('updateChat', function (user_name, user_id, data) {
                $("#chat_body").append($("<li>", {
                    class: "media"
                }).append(
                    $('<div>', {
                        class: "media-left media-middle",
                    }).append(
                        $("<img>", {
                            class: "media-object",
                            height: "30px",
                            alt: "Generic placeholder image",
                            src: ROOT_PATH+"images/profile/"+user_id
                        })
                    )
                ).append($("<div>", {
                    class: "media-body"
                }).append($("<p>", {
                    text: $('<div>').html(data.body).text()
                })).append($("<small>", {
                    class: "text-muted",
                    text: $('<div/>').html(user_name).text()+" | " + new Date().toLocaleString()
                })))).append($('<hr>', {
                    css: {
                        'margin-top': '5px',
                        'margin-bottom': '5px'
                    }
                }));


                $("#chat-panel-body").animate({
                    scrollTop:  $('#chat-panel-body').prop("scrollHeight")
                }, "slow");
            });

            socket.on('updateMembers', function (user_id, user_name) {
                members[user_id] = user_name;
                $("#logged_users").append($("<li>", {
                    id: 'user_id_'+user_id,
                    class: "list-group-item",
                    text: $('<div/>').html(user_name).text()
                }).append($('<span>', {
                    class: "label label-as-badge pull-right label-default",
                    text: " "
                })));
            });

            @if(Auth::user()->user->chatrooms->count()>1)
                @foreach(Auth::user()->user->chatrooms as $chatroom)
                    @if($chatroom->id != $room->id)
                        socket.on('notification-channel:chat{{$chatroom->id}}', function(){
                            if($('#notification_chat_{{$chatroom->id}}').length > 0){
                                var count = parseInt($('#notification_chat_{{$chatroom->id}}').text());
                                $('#notification_chat_{{$chatroom->id}}').text(++count);
                            }else{
                                $("#chatroom_id_{{$chatroom->id}}").append($('<span>', {
                                    id: "notification_chat_{{$chatroom->id}}",
                                    class: "label label-as-badge pull-right label-danger",
                                    text: "1"
                                }));
                            }
                        });
                    @endif
                @endforeach
            @endif
        });

    </script>
@stop