<div class="panel-body breaker">
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>Device</th><th>Software</th><th>Available at</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($experiments as $physicalExperiments)
                <tr>
                    <td>{{ $physicalExperiments->first()->experiment->device->name }}</td>
                    <td>{{ $physicalExperiments->first()->experiment->software->name }}</td>
                    <td>
                        @foreach($physicalExperiments as $physicalExperiment)
                            @if($physicalExperiment->server->isAvailable())
                                <span class="label" style="background-color: {{ $physicalExperiment->server->color }}">{{ $physicalExperiment->server->name }}</span>
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