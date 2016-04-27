@extends('user.layouts.default')

@section('content')

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="display: none">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">WebRTC Video Chat</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <!-- chatroom name form -->
                <form class="navbar-form navbar-right form-inline">
                    <div class="form-group">
                        <input class="form-control" type="text" id="room-name" placeholder="Room name"/>
                    </div>
                    <button class="btn btn-primary" id="btn-video-start">Start</button>
                    <button class="btn btn-default"  id="btn-video-join">Join</button>
                    <button class="btn btn-default"  disabled id="btn-video-stop">Stop</button>
                </form>
            </div>

            <!--/.navbar-collapse --> </div>

    </nav>
    <div class="container main">
        <div class="row videos">
            <div class="remote-video">
                <video width="280" height="250" autoplay="true" id="remote-video"></video>
            </div>
            <div class="local-video">
                <video width="280" height="250" autoplay="true" id="local-video" muted></video>
            </div>
        </div>
    </div>
@stop

@section('page_js')
    @parent
    <script type="text/javascript" src="{{ asset('js/videochat.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/signalling.js') }}"></script>
    <script type="text/javascript">
        localIsCaller = {!! Session::has('caller') ? "true" : "false" !!};
        connect("{{$id}}");
    </script>
@stop
