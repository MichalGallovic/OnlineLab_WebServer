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

@section('page_js')
    @parent

    <script type="text/javascript">
        $('#schema').on('change', function() {
            $('#schema-image').attr('src', '{{route('controller.schema.image')}}'+'/'+this.value);
            $.get('{{route('controller.schema.data')}}', {id: this.value} ,function(data, status){
                $('#schema-body').val(data.fileContent);

                switch (data.type){
                    case "{{trans("controller::default.CTRL_SCHEMA_FILE")}}":
                        $(".text-div").hide();
                        $(".file-div").show();
                        break;
                    case "{{trans("controller::default.CTRL_SCHEMA_TEXT")}}":
                        $(".file-div").hide();
                        $(".text-div").show();
                        break;
                    case "{{trans("controller::default.CTRL_SCHEMA_NONE")}}":
                        $(".text-div").hide();
                        $(".file-div").hide();
                        break;
                }
            });
        });

        $("#schema-image").on("click", function() {
            $('#imagepreview').attr('src', $('#schema-image').attr('src')); // here asign the image to the modal when the user click the enlarge link
            $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
        });
    </script>
@endsection