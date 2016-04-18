<div class="panel-body breaker">
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>ID</th><th>Name</th><th>IP</th><th>Port</th><th>Deployed for</th><th>Experiments</th><th></th><th></th><th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servers as $server)
                <tr>
                    <td>{{ $server->id }}</td>
                    <td>
                    @if($server->available && !$server->disabled)
                        <span class="label" style="background-color: {{ $server->color }}">{{ $server->name }}</span>
                    @else
                        <span class="label label-danger">{{ $server->name }}</span>
                        @if(! $server->reachable)
                            <span class="label label-warning">not responding</span>
                        @else
                            @if(! $server->database)
                                <span class="label label-warning">database</span>
                            @endif
                            @if(! $server->redis)
                                <span class="label label-warning">redis</span>
                            @endif
                            @if(! $server->queue)
                                <span class="label label-warning">queue</span>
                            @endif
                            @if($server->disabled)
                                <span class="label label-warning">disabled</span>
                            @endif
                        @endif
                    @endif
                    </td>
                    <td>{{ $server->ip }}</td>
                    <td>{{ $server->port }}</td>
                    <td>
                        @if($server->production)
                            <span class="label label-success">production</span>
                        @else
                            <span class="label label-warning">testing</span>
                        @endif
                    </td>
                    <td>{{ $server->sumExperimentInstances() }}</td>
                    <td class="col-md-1">
                        <a href="{{ route("servers.edit", $server->id) }}" class="btn btn-xs btn-warning btn-block"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                    </td>
                    @if(!$server->disabled)
                        <td class="col-md-1">
                            <a href="{{ route("servers.disable", $server->id) }}" class="btn btn-xs btn-danger btn-block"><i class="glyphicon glyphicon-off"></i> Disable</a>
                        </td>
                    @else
                        <td class="col-md-1">
                            <a href="{{ route("servers.enable", $server->id) }}" class="btn btn-xs btn-success btn-block"><i class="glyphicon glyphicon-off"></i> Enable</a>
                        </td>
                    @endif
                    <td class="col-md-1">
                        {!! Form::open(['method' => 'DELETE','route' => ['servers.destroy',$server->id]]) !!}
                            <button class="btn btn-xs btn-danger btn-block" type="submit"><i class="glyphicon glyphicon-remove"></i> Delete</button>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th><th>Name</th><th>IP</th><th>Port</th><th>Deployed for</th><th>Experiments</th><th></th><th></th><th></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>