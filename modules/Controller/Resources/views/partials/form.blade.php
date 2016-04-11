<div class="form-group {!! ($errors->has('title')) ? 'has-error' : '' !!}">
    {!! Form::label('title', trans("controller::default.CTRL_NAME").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title',null,['class'=>'form-control keyup-refresh']) !!}
        @if($errors->has('title'))
            <span class="errors">{!! $errors->first('title') !!}</span>
        @endif
    </div>
</div>
<div class="form-group {!! ($errors->has('body')) ? 'has-error' : '' !!}">
    {!! Form::label('body', trans("controller::default.LABEL_BODY_REGULATOR").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('body',null,['class'=>'form-control keyup-refresh']) !!}
        @if($errors->has('body'))
            <span class="errors">{!! $errors->first('body') !!}</span>
        @endif
    </div>
</div>
<div class="form-group {!! ($errors->has('system_id')) ? 'has-error' : '' !!}">
    {!! Form::label('system_id', trans("controller::default.LABEL_SYSTEM").':', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('system_id',null,['class'=>'form-control']) !!}
        @if($errors->has('system_id'))
            <span class="errors">{!! $errors->first('system_id') !!}</span>
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