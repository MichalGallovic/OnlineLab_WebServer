@extends('user.layouts.default')

@section('content')

    <div class="navbar">
        <ul class=" nav nav-pills col-sm-offset-1">
            <li role="presentation" {{$enviroment=='matlab' ? 'class=active' : ''}}><a href="{{route('controller.create','matlab')}}">Matlab</a></li>
            <li role="presentation" {{$enviroment=='openmodelica' ? 'class=active' : ''}}><a href="{{route('controller.create','openmodelica')}}">Openmodelica</a></li>
            <li role="presentation" {{$enviroment=='scilab' ? 'class=active' : ''}}><a href="{{route('controller.create','scilab')}}">Scilab</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-2">
            <h3>{{trans("controller::default.CTRL_SCHEMA_NONEXIST")}}</h3>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-offset-1 col-sm-6">
            <a href="{{ url('controller')}}" class="btn btn-default">Back</a>
        </div>
    </div>


@stop