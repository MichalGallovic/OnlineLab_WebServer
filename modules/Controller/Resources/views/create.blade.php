@extends('user.layouts.default')

@section('content')

    <div class="navbar">
        <ul class=" nav nav-pills col-sm-offset-1">
            @foreach($softwares as $software)
                <li role="presentation" {{$enviroment==$software ? 'class=active' : ''}}><a href="{{route('controller.create',$software)}}">{{$software}}</a></li>
            @endforeach
        </ul>
    </div>
    {!! Form::open(['method' => 'POST','route'=>'controller.store', 'class' => 'form-horizontal', 'id' => 'controllerForm', 'files'=>true]) !!}
    @include('controller::partials.form')
    @if($enviroment=='openmodelica')
        <div class="form-group text-div" {!! ($schema->type != trans("controller::default.CTRL_SCHEMA_TEXT")) ? 'style="display: none"' : ''!!}>
            {!! Form::label('openmodelica-final', 'Final regulator', ['class' => 'control-label col-md-2']) !!}
            <div class="col-sm-10">
                {!! Form::textarea("openmodelica-final",
                    "model\nequation\n\nend ;",
                    ['class' => 'form-control', 'readonly', 'style' => 'font-family:consolas'])
                !!}
            </div>
        </div>
        <div class="form-group text-div" {!! ($schema->type != trans("controller::default.CTRL_SCHEMA_TEXT")) ? 'style="display: none"' : ''!!}>
            <div class="col-sm-offset-2 col-sm-1">
                <button class="btn btn-default addParameterButton" type="button"><span class="glyphicon glyphicon-plus" style="color: green"></span> {{trans("controller::default.CTRL_ADD_PARAMETER")}}</button>
            </div>
            <div class="col-sm-offset-4 col-sm-1">
                <button class="btn btn-default addVariableButton" type="button"><span class="glyphicon glyphicon-plus" style="color: green"></span> {{trans("controller::default.CTRL_ADD_VARIABLE")}}</button>
            </div>
        </div>

        <div class="row text-div" {!! ($schema->type != trans("controller::default.CTRL_SCHEMA_TEXT")) ? 'style="display: none"' : ''!!}>
            <div id="parameters" class="col-md-6">
            </div>
            <div id="variables" class="col-md-6">
            </div>
        </div>
    @endif

    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-6">
            <a href="{{ url('controller')}}" class="btn btn-default">Back</a>
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::hidden('enviroment', $enviroment) !!}
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

        @if($enviroment == 'openmodelica')
        function refreshController(){

            var text = "model "+$("#controllerForm input[name = title]").first().val() + "\n";
            $("#parameters .form-group").each(function(){
                text += "\tParameter "
                        + $(this).find("select[name = parameterType]").first().val()
                        + " "
                        + $(this).find("input[name = parameterTitle]").first().val()
                        + "="
                        + $(this).find("input[name = parameterValue]").first().val()
                        + ";\n";
            });

            $("#variables .form-group").each(function(){
                text += "\tUdaqOut cIn\n\tUdaqIn cOut;\n"
                        +"\t"+$(this).find("select[name = variableType]").first().val()
                        + " "
                        + $(this).find("input[name = variableTitle]").first().val()
                        + ";\n";
            });
            text += "equation\n" + $("#controllerForm textarea[name = body]").first().val()
                    + "\nend " + $("#controllerForm input[name = title]").first().val() + ";";
            $("#openmodelica-final").val(text);
        }

        $(function() {

            $( ".keyup-refresh" ).keyup(refreshController);
        });

        $(".addParameterButton").click(function(){

            $('#parameters').append(
            $('<div>',
                {
                    class: "form-group"
                }
            ).append(
                $('<span>',
                    {
                        class: "col-sm-offset-4 col-sm-3"
                    }
                ).append(
                    $('<select>',
                        {
                            'class': 'form-control',
                            'name': 'parameterType',
                            'change': refreshController
                        }
                    ).append(
                        $('<option>',
                            {
                                value: 'Real',
                                text: 'Real'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Integer',
                                text: 'Integer'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'String',
                                text: 'String'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Boolean',
                                text: 'Boolean'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Discrete Real',
                                text: 'Discrete Real'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Discrete Integer',
                                text: 'Discrete Integer'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Discrete String',
                                text: 'Discrete String'
                            }
                        )
                    ).append(
                        $('<option>',
                            {
                                value: 'Discrete Boolean',
                                text: 'Discrete Boolean'
                            }
                        )
                    )
                )
            ).append(
                $('<span>',
                    {
                        class: "col-sm-2"
                    }
                ).append(
                    $('<input>',
                        {
                            'class': 'form-control',
                            'name': 'parameterTitle',
                            'placeholder': "Názov",
                            'keyup': refreshController
                        }
                    )
                )
            ).append(
                $('<span>',
                    {
                        class: "col-sm-2"
                    }
                ).append(
                    $('<input>',
                        {
                            'class': 'col-sm-2 form-control',
                            'name': 'parameterValue',
                            'placeholder': "Hodnota",
                            'keyup': refreshController
                        }
                    )
                )
            ).append(
                $('<button>',
                    {
                        class: "col-sm-1 btn btn-default btn-md",
                        on    : {
                            click: function() {
                                $(this).parent().remove();
                                refreshController();
                            }
                        }
                    }
                ).append(
                    $('<span>',
                        {
                            class: "glyphicon glyphicon-remove",
                            css: {
                                color: 'red'
                            }
                        }
                    )
                )
            ));
            refreshController();
        });

        $(".addVariableButton").click(function(){
            $('#variables').append(
                $('<div>',
                    {
                        class: "form-group"
                    }
                ).append(
                    $('<span>',
                        {
                            class: "col-sm-3 col-sm-offset-2"
                        }
                    ).append(
                        $('<select>',
                            {
                                'class': 'form-control',
                                'name': 'variableType',
                                'change': refreshController
                            }
                        ).append(
                            $('<option>',
                                {
                                    value: 'Real',
                                    text: 'Real'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Integer',
                                    text: 'Integer'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'String',
                                    text: 'String'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Boolean',
                                    text: 'Boolean'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Discrete Real',
                                    text: 'Discrete Real'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Discrete Integer',
                                    text: 'Discrete Integer'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Discrete String',
                                    text: 'Discrete String'
                                }
                            )
                        ).append(
                            $('<option>',
                                {
                                    value: 'Discrete Boolean',
                                    text: 'Discrete Boolean'
                                }
                            )
                        )
                    )
                ).append(
                    $('<span>',
                        {
                            class: "col-sm-2"
                        }
                    ).append(
                        $('<input>',
                            {
                                'class': 'form-control',
                                'placeholder': "Názov",
                                'name': "variableTitle",
                                'keyup': refreshController
                            }
                        )
                    )
                ).append(
                    $('<button>',
                        {
                            class: "col-sm-1 btn btn-default btn-md",
                            on    : {
                                click: function() {
                                    $(this).parent().remove();
                                    refreshController();
                                }
                            }
                        }
                    ).append(
                        $('<span>',
                            {
                                class: "glyphicon glyphicon-remove",
                                css: {
                                    color: 'red'
                                }
                            }
                        )
                    )
                )
            );
            refreshController();
        });
        @endif
    </script>

@stop
