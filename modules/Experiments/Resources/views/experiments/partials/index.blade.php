<div class="panel-body breaker">
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>Device</th><th>Software</th><th>Available at</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($experiments as $experiment)
                <tr>
                    <td>{{ $experiment->device->name }}</td>
                    <td>{{ $experiment->software->name }}</td>
                    <td>
                        @foreach($experiment->servers()->wherePivot('available',true)->get() as $server)
                            <span class="label label-success">{{ $server->name }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Device</th><th>Software</th><th>Available at</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>