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
                        @foreach($experiment->servers()->wherePivot('instances','>',0)->get() as $server)
                            @if($server->available && !$server->disabled)
                                @for($i = 0; $i < $server->pivot->instances; $i++)
                                    <span class="label" style="background-color: {{ $server->color }}">{{ $server->name }}</span>
                                @endfor
                            @else
                                @for($i = 0; $i < $server->pivot->instances; $i++)
                                    <span class="label label-danger">{{ $server->name }}</span>
                                @endfor
                            @endif
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