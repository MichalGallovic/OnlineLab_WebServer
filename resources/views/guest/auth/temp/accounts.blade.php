@extends('guest.layouts.default')

@section('content')
    <div class="container-fluid">
        <div class="row" style="margin-top: 40px">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Login</div>
                    <div class="panel-body">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class=" col-md-12">
                                <a href="{{ url('/accounts/firstLogin') }}" class="btn btn-primary btn-lg btn-block">This is my first time logging in</a>
                            </div>
                        </div>
                            <div class="row" style="margin-top: 10px">
                                <div class=" col-md-12">
                                    <button id="link-accounts-button" type="button" class="btn btn-default btn-lg btn-block">I already have an account</button>
                                </div>
                            </div>
                        <div id="link-accounts-form" class="row" style="margin-top: 10px; display: none;">
                            <form class="form-horizontal" role="form" method="POST" action="{{ url('accounts/sendVerifMain') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                <div class="form-group">
                                    <label class="col-md-4 control-label">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="otherEmail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">Link accounts</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    @parent
    <script>
        $( "#link-accounts-button" ).click(function() {
            $( "#link-accounts-form" ).slideDown("slow");
        });
    </script>
@endsection