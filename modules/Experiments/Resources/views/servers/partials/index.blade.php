<div class="panel-body breaker">
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>ID</th><th>Name</th><th>IP</th><th>Port</th><th>Experiments</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($servers as $server)
                <tr>
                    <td>{{ $server->id }}</td>
                    <td>
                    @if($server->available)
                        <span class="label label-success">{{ $server->name }}</span>
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
                        @endif
                    @endif
                    </td>
                    <td>{{ $server->ip }}</td>
                    <td>{{ $server->port }}</td>
                    <td>{{ $server->experiments()->wherePivot("available",true)->count() }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th><th>Name</th><th>IP</th><th>Port</th><th>Experiments</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>