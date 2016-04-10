@extends('user.layouts.default')

@section('content')

    <div class="navbar">
        <ul class=" nav nav-pills col-sm-offset-1">
            <li role="presentation" {{$enviroment=='matlab' ? 'class=active' : ''}}><a href="{{route('controller.create','matlab')}}">Matlab</a></li>
            <li role="presentation" {{$enviroment=='openmodelica' ? 'class=active' : ''}}><a href="{{route('controller.create','openmodelica')}}">Openmodelica</a></li>
            <li role="presentation" {{$enviroment=='scilab' ? 'class=active' : ''}}><a href="{{route('controller.create','scilab')}}">Scilab</a></li>
        </ul>
    </div>
    {!! Form::open(['method' => 'POST','route'=>'controller.store', 'class' => 'form-horizontal', 'id' => 'controllerForm']) !!}
    @include('controller::partials.form')
    @if($enviroment=='openmodelica')
        <div class="form-group">
            {!! Form::label('openmodelica-final', 'Final regulator', ['class' => 'control-label col-md-2']) !!}
            <div class="col-sm-10">
                {!! Form::textarea("openmodelica-final",
                    "model\nequation\n\nend ;",
                    ['class' => 'form-control', 'readonly', 'style' => 'font-family:consolas'])
                !!}
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-1">
                <button class="btn btn-default addParameterButton" type="button"><span class="glyphicon glyphicon-plus" style="color: green"></span> {{trans("controller::default.CTRL_ADD_PARAMETER")}}</button>
            </div>
            <div class="col-sm-offset-4 col-sm-1">
                <button class="btn btn-default addVariableButton" type="button"><span class="glyphicon glyphicon-plus" style="color: green"></span> {{trans("controller::default.CTRL_ADD_VARIABLE")}}</button>
            </div>
        </div>

        <div class="row">
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
    @if($enviroment == 'openmodelica')
    <script type="text/javascript">
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
                text += "\t"+$(this).find("select[name = variableType]").first().val()
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
    </script>
    @endif
@stop
