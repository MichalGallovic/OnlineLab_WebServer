<div class="panel-body breaker">
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>Filled</th><th>Device</th><th>Software</th><th>Device ID</th><th>Simulation time</th><th>From</th><th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($reports as $report)
                <tr>
                    <td>
                        @if($report->filled)
                            <i class="glyphicon glyphicon-ok" style="color: #5cb85c"></i>
                        @else
                            <i class="glyphicon glyphicon-minus"></i>
                        @endif
                    </td>
                    <td><a href="{{ route('report.show', $report->id) }}">{{ $report->physicalExperiment->experiment->device->name }}</a></td>
                    <td><a href="{{ route('report.show', $report->id) }}">{{ $report->physicalExperiment->experiment->software->name }}</a></td>
                    <td><a href="{{ route('report.show', $report->id) }}">{{ $report->physicalDevice->name }}</a></td>
                    <td><a href="{{ route('report.show', $report->id) }}">{{ $report->simulation_time }}</a></td>
                    <td><a href="{{ route('report.show', $report->id) }}">{{ $report->created_at }}</a></td>
                    <td>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'route' => ['report.delete', $report->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-block btn-xs']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Filled</th><th>Device</th><th>Software</th><th>Device ID</th><th>Simulation time</th><th>From</th><th>Action</th>
            </tr>
            </tfoot>
        </table>
        <div class="pagination"> {!! $reports->render() !!} </div>
    </div>
</div>