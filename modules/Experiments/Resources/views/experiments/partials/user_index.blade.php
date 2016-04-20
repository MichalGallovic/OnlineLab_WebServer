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
                        @foreach($experimentInstances->where('experiment_id',$experiment->id) as $instance)
                            <span class="label" style="background-color: {{ $instance->server->color }}">{{ $instance->server->name }}</span>
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