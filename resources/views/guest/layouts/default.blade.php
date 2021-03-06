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
    <link rel="icon" href="" type="image/x-icon" />
    <link rel="shortcut icon" href="" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{ asset('css/jquery-ui-custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/default.css') }}" rel="stylesheet" type="text/css" />
    @yield('page_css')

</head>
<body>
    @yield('content')

    <script type="text/javascript">
        var ROOT_PATH = '{ROOT_PATH}';
    </script>
    <script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/default.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    @yield('page_js')
</body>
</html>
