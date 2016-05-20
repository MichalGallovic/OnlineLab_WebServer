<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
    {!! Form::label('title', trans("controller::default.CTRL_NAME").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title',null,['class'=>'form-control keyup-refresh']) !!}
        @if($errors->has('title'))
            <span class="errors">{!! $errors->first('title') !!}</span>
        @endif
    </div>
</div>

<div class="form-group {!! ($errors->has('type')) ? 'has-error' : '' !!}">
    {!! Form::label('type', trans("controller::default.CTRL_ACCESSIBILITY").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('type', ['private' => trans("controller::default.ACCES_PRIVATNY") , 'public' => trans("controller::default.ACCES_VEREJNY")], null,  ['class'=>'form-control']) !!}
        @if($errors->has('type'))
            <span class="errors">{!! $errors->first('type') !!}</span>
        @endif
    </div>
</div>

<div class="form-group">
    {!! Form::label('schema_id', trans("controller::default.CTRL_SCHEMA").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('schema_id', $schemas, null, ['class'=>'form-control', 'id' => 'schema']) !!}
    </div>
</div>
<div class=" form-group" style=" max-height: 300px;">
    <div class="col-sm-offset-2 col-sm-5">
        <div class="profile-picture-frame " style=" padding: 15px; min-height: 100%; min-height: 100%;">

            {!! Html::image('controller/schema/image/'.$schema->id , '', ['class' => 'center-block', 'style' => 'max-width:100%; max-height:180px', 'id' => 'schema-image']) !!}

        </div>
    </div>
    <div class="col-sm-5" style="min-height: 100%; min-height: 100%;">
        <div>
            {!! Form::textarea('schema-body', $schema->getFileContent(),['class'=>'form-control', 'readonly', 'style' => 'min-height=100%;overflow=auto;', 'id' => 'schema-body']) !!}
        </div>
    </div>

</div>
@if($schema->note)
    <div class="form-group {!! ($errors->has('note')) ? 'has-error' : '' !!}">
        {!! Form::label("note", trans("controller::default.CTRL_SCHEMA_NOTE"), ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('note', $schema->note, ['class' => 'form-control', 'readonly', 'style' => 'overflow-y: auto; max-height:140px;']) !!}
        </div>
        @if($errors->has('note'))
            <p>{!! $errors->first('note') !!}</p>
        @endif
    </div>
@endif
<div class="form-group text-div {!! ($errors->has('body')) ? 'has-error' : '' !!}" {!! ($schema->type != trans("controller::default.CTRL_SCHEMA_TEXT")) ? 'style="display: none"' : ''!!}>
    {!! Form::label('body', trans("controller::default.LABEL_BODY_REGULATOR").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('body',null,['class'=>'form-control keyup-refresh']) !!}
        @if($errors->has('body'))
            <span class="errors">{!! $errors->first('body') !!}</span>
        @endif
    </div>
</div>


<div class="form-group file-div {!! ($errors->has('filename')) ? 'has-error' : '' !!}" {!! ($schema->type != trans("controller::default.CTRL_SCHEMA_FILE")) ? 'style="display: none"' : ''!!}>
    {!! Form::label('filename', trans("controller::default.CTRL_UPLOAD_FILE").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::file('filename', ['class'=>'form-control']) !!}
        @if($errors->has('filename'))
            <span class="errors">{!! $errors->first('filename') !!}</span>
        @endif
    </div>
</div>


<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Image preview</h4>
            </div>
            <div class="modal-body">
                <img src="" id="imagepreview" class="center-block" style="max-width:100%; max-height: 100%;" >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

