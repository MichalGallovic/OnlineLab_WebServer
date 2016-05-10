<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ONLINE LABORATORY MANAGER</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="Author" content="" />
    <meta name="Generator" content="" />
    <meta name="Copyright" content="" />
    <meta name="Robots" content="ALL,FOLLOW" />
    <meta name="Resource-Type" content="document" />
    <meta http-equiv="Content-Language" content="sk" />
    <meta http-equiv="Cache-Control" content="Public" />
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{ asset('css/jquery-ui-custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/helpers.css') }}">
    <link href="{{ asset('css/default.css') }}" rel="stylesheet" type="text/css" />
    @yield('page_css')

</head>
<body>
    <div class="dashboard_overlay"></div>
    <div id="content">
        <div id="left_navig_back" style="{DASHBOARD_LEFT_NAVIG_WIDTH}"></div>
        <div id="dashboard_left-navig" style="{DASHBOARD_LEFT_NAVIG_WIDTH}">
            @include('user.partials.sidebar')
        </div>

        <div id="dashboard_mainview" style="{DASHBOARD_MAINVIEW_MARGIN}">
            <div id="action_holder">
                <div id="action_listener"></div>
            </div>
            @include('user.partials.header')
            @if(Session::has('success'))
                <div class="alert alert-success">{!! Session::get('success') !!}</div>
            @elseif(Session::has('fail'))
                <div class="alert alert-danger">{!! Session::get('fail') !!}</div>
            @endif
            @if(Session::has('flash_message'))
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('flash_message') }}</p>
            @endif
            <div id="containment-wrapper" style="float:right">
                @yield('content')
            </div>

        </div>
    </div>
<div class="breaker"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.4.5/socket.io.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>

<script type="text/javascript">
   // var ROOT_PATH = '{ROOT_PATH}';
    var ROOT_PATH = 'http://iolab.sk:8013/';
    var socket = io.connect('{{env('SOCKETIO_ADDRESS')}}');

    $(function () {
        $('[data-toggle="popover"]').popover();
    })

    $('body').on('click', function (e) {
        $('[data-toggle=popover]').each(function () {
            // hide any open popovers when the anywhere else in the body is clicked
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    $('#notifications').on('click', function(){
        $('#notifications_count').text('');
    });

    function myNotify(data) {
        console.log(data);
        var count = parseInt($('#notifications_count').text());
        if(count){
            $('#notifications_count').text(++count);
        }else{
            $('#notifications_count').text(1);
        }
        if($('#notifications').data('bs.popover')) {
            if($('#notifications').data('bs.popover').options.content.length>0){
                var temp = $('#notifications').data('bs.popover').options.content;
                temp += '<hr>' + data;
                $('#notifications').data('bs.popover').options.html = true;
                $('#notifications').data('bs.popover').options.content = temp;
            }else {
                $('#notifications').data('bs.popover').options.html = true;
                $('#notifications').data('bs.popover').options.content = data;
            }
        }
        $('#notifications').popover('show');
    }

    // socket.on('notification-channel:App\\Events\\MemberAdded{{Auth::user()->user->id}}', myNotify);

    @foreach(Auth::user()->user->threads as $thread)
        // socket.on('notification-channel:App\\Events\\CommentAdded{{$thread->id}}', myNotify);
    @endforeach
    var Laravel = {
        user: {
            id: {{ Auth::user()->user->id }},
            role: '{{ Auth::user()->user->role }}'
        }
    };
</script>
@yield('page_js')
</body>
</html>
