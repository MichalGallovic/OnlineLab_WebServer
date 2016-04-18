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
                        @foreach($experiment->servers()->available()->hasExperiments()->get() as $server)
                            @for($i = 0; $i < $server->pivot->instances; $i++)
                                <span class="label" style="background-color: {{ $server->color }}">{{ $server->name }}</span>
                            @endfor
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