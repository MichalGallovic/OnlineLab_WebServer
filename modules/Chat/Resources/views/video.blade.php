@extends('user.layouts.default')

@section('content')

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
    <script type="text/javascript" src="{{ asset('js/chat/videochat.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/chat/signalling.js') }}"></script>
    <script type="text/javascript">
        localIsCaller = {!! Session::has('caller') ? "true" : "false" !!};
        connect("{{$id}}");
    </script>
@stop
