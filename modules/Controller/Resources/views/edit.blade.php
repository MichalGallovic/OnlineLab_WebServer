@extends('user.layouts.default')

@section('content')
    {!! Form::model($regulator,['method' => 'PATCH','route'=>['controller.update',$regulator->id], 'class' => 'form-horizontal']) !!}
    <div class="form-group">
        {!! Form::label('Id',  trans("controller::default.ID").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>
    @include('controller::partials.form');
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <a href="{{ url('controller')}}" class="btn btn-default">{{trans('controller::default.BACK_TO_CONTROLLERS')}}</a>
            {!! Form::submit(trans('controller::default.CTRL_SAVE'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop