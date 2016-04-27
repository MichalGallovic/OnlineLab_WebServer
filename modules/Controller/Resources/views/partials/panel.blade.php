<div class="panel-body breaker">
    <table class="table table-hover">
        <thead>
        <tr>
            <th class="col-sm-1">{{ trans("controller::default.ID") }}</th>
            <th class="col-sm-2">{{ trans("controller::default.CTRL_NAME") }}</th>
            <th class="col-sm-1">{{ trans("controller::default.LABEL_SYSTEM") }}</th>
            <th class="col-sm-2">{{ trans("controller::default.CTRL_DATE") }}</th>
            <th class="col-sm-2">{{ trans("controller::default.CTRL_AUTHOR") }}</th>
            <th class="col-sm-1">{{ trans("controller::default.CTRL_ACCESSIBILITY") }}</th>
            <th class="col-sm-1"></th>
            <th class="col-sm-1"></th>
            <th class="col-sm-1"></th>
        </tr>
        </thead>
        <tbody>

        @foreach ($regulators as $regulator)
            <tr>
                <td>{{ $regulator->id }}</td>
                <td>{{ $regulator->title }}</td>
                <td>{{$regulator->system_id}}</td>
                <td>{{ $regulator->created_at }}</td>
                <td>{{$regulator->user->getFullName()}}</td>
                <td>{{ $regulator->type }}</td>
                <td><a href="{{url('controller',$regulator->id)}}" class="btn btn-sm btn-block btn-primary"><span class="glyphicon glyphicon-search"></span> {{ trans("controller::default.PREVIEW_TITLE") }}</a></td>
                @if($regulator->filename)
                    <td><a href="{{route('controller.download',$regulator->id)}}" class="btn btn-sm btn-block btn-info"><span class="glyphicon glyphicon-save-file"></span> {{ trans("controller::default.CTRL_DOWNLOAD_FILE") }}</a></td>
                @else
                    <td><a href="{{route('controller.edit',$regulator->id)}}" class="btn btn-sm btn-block btn-warning"><span class="glyphicon glyphicon-edit"></span> {{ trans("controller::default.SETTINGS_TITLE") }}</a></td>
                @endif
                <td class="col-md-1">
                    {!! Form::open(['method' => 'DELETE', 'route'=>['controller.destroy', $regulator->id]]) !!}
                    {!! Form::button('<span class="glyphicon glyphicon-remove"></span> '.trans("controller::default.TRASH_TITLE"), ['class' => 'btn btn-sm btn-block btn-danger', 'type'=>'submit']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach

        </tbody>
        <tfoot>
        <tr>
            <th>{{ trans("controller::default.ID") }}</th>
            <th>{{ trans("controller::default.CTRL_NAME") }}</th>
            <th>{{ trans("controller::default.LABEL_SYSTEM") }}</th>
            <th>{{ trans("controller::default.CTRL_DATE") }}</th>
            <th>{{ trans("controller::default.CTRL_AUTHOR") }}</th>
            <th>{{ trans("controller::default.CTRL_ACCESSIBILITY") }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>