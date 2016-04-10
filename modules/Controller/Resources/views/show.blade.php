@extends('user.layouts.default')

@section('content')
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg4 toppad" >
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">{{$regulator->title}}</h3>
            </div>
            <div class="panel-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>{{ trans("controller::default.ID") }}:</td>
                        <td>{{$regulator->id}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans("controller::default.CTRL_AUTHOR") }}:</td>
                        <td>{{$regulator->user->getFullName()}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans("controller::default.CTRL_ACCESSIBILITY") }}:</td>
                        <td>{{$regulator->type}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans("controller::default.LABEL_SYSTEM") }}:</td>
                        <td>{{$regulator->system_id}}</td>
                    </tr>

                    <tr>
                    <tr>
                        <td>{{ trans("controller::default.CTRL_DATE") }}:</td>
                        <td>{{$regulator->created_at}}</td>
                    </tr>
                    <tr>
                        <td>{{ trans("controller::default.CTRL_UPDATE") }}:</td>
                        <td>{{$regulator->updated_at}}</td>
                    </tr>

                    <tr>
                    <td>{{ trans("controller::default.LABEL_BODY_REGULATOR") }}:</td>
                    <td>{{$regulator->body}}
                    </td>

                    </tr>

                    </tbody>
                </table>
                <a href="{{ url('controller')}}" class="btn btn-default">{{trans('controller::default.BACK_TO_CONTROLLERS')}}</a>
            </div>
        </div>
    </div>
@stop