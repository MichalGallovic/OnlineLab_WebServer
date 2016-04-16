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
                    <td><span class="label label-success">{{ $server->name }}</span></td>
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