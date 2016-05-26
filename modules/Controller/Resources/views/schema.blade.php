@extends('user.layouts.default')

@section('content')
    {!! Form::model($schema,['method' => 'PATCH','route'=>['controller.schema.update',$schema->id], 'class' => 'form-horizontal', 'enctype' => "multipart/form-data"]) !!}
    <div class="form-group">
        {!! Form::label('Id',  trans("controller::default.CTRL_SCHEMA_ID").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('id',null,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>
    <div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
        {!! Form::label('title', trans("controller::default.CTRL_SCHEMA_TITLE").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('title', null ,['class'=>'form-control']) !!}
            @if($errors->has('title'))
                <span class="errors">{!! $errors->first('title') !!}</span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {!! Form::label("software", trans("controller::default.CTRL_SCHEMA_SOFTWARE"), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select("software", $softwares, $schema->experiment->software->id, ['class' => 'form-control', 'id' => 'software']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label("experiment_id", trans("controller::default.LABEL_SYSTEM"), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select("experiment_id", $experiments, $schema->experiment->id, ['class' => 'form-control', 'id' => 'experiment']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label("type", trans("controller::default.CTRL_SCHEMA_TYPE"), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::select("type", [trans("controller::default.CTRL_SCHEMA_TEXT") => trans("controller::default.CTRL_SCHEMA_TEXT_LEGEND"), trans("controller::default.CTRL_SCHEMA_FILE") => trans("controller::default.CTRL_SCHEMA_FILE_LEGEND"), trans("controller::default.CTRL_SCHEMA_NONE") => trans("controller::default.CTRL_SCHEMA_NONE_LEGEND")], null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('body', trans("controller::default.CTRL_SCHEMA_CONTENT").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('body', $file,['class'=>'form-control', 'readonly']) !!}
        </div>
    </div>

    <div class="form-group {!! ($errors->has('filename')) ? 'has-error' : '' !!} ">
        {!! Form::label('filename', trans("controller::default.CTRL_SCHEMA").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::file('filename',['class'=>'form-control']) !!}
            @if($errors->has('filename'))
                <span class="errors">{!! $errors->first('filename') !!}</span>
            @endif
        </div>
    </div>

    @if($schema->image)
        <div class=" form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="profile-picture-frame " style=" padding: 15px; min-height: 200px">
                    {!! Html::image('controller/schema/image/' . $schema->id, '', ['class' => 'center-block', 'style' => 'max-width:100%;']) !!}
                </div>
            </div>

        </div>
    @endif


    <div class="form-group {!! ($errors->has('image')) ? 'has-error' : '' !!} ">
        {!! Form::label('image', trans("controller::default.CTRL_SCHEMA_IMG").':', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::file('image',['class'=>'form-control']) !!}
            @if($errors->has('image'))
                <span class="errors">{!! $errors->first('image') !!}</span>
            @endif
        </div>
    </div>

    <div class="form-group {!! ($errors->has('note')) ? 'has-error' : '' !!}">
        {!! Form::label("note", trans("controller::default.CTRL_SCHEMA_NOTE"), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('note', $schema->note, ['class' => 'form-control']) !!}
        </div>
        @if($errors->has('note'))
            <p>{!! $errors->first('note') !!}</p>
        @endif
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <a href="{{ url('controller')}}" class="btn btn-default">{{trans('controller::default.BACK_TO_CONTROLLERS')}}</a>
            {!! Form::submit(trans('controller::default.CTRL_SAVE'), ['class' => 'btn btn-primary']) !!}
        </div>
    </div>


    {!! Form::close() !!}
@stop


@section('page_js')
    @parent
    <script type="text/javascript">
        $('#software').on('change', function() {

            $.get(ROOT_PATH + 'experiments/software/'+this.value, function(data, status){
                $('#experiment').empty();
                $.each(data , function(i, val) {
                    $('#experiment').append($('<option>', {
                        text: val.device.name,
                        value: val.id
                    }))
                });
            });
        });
    </script>
@stop