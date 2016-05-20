@extends('user.layouts.default')

@section('content')


	<div id="users">

		<div id="users_list">

			<table class="table table-hover">
				<thead>
				<tr>
					<th>Id.</th>
					<th>Full name</th>
					<th>Language</th>
					<th>Role</th>
					<th>Registration Date</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>

				@foreach ($users as $user)
					<tr>
						<td>{{ $user->id }}</td>
						<td>{{ $user->name.' '.$user->lastName }}</td>
						<td>{!! $user->language_code !!}</td>
						<td>{{ $user->role }}</td>
						<td>{{ $user->created_at }}</td>
						<td class="col-md-1"><a href="{{url('users',$user->id)}}" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-search"></span> Show</a></td>
						<td class="col-md-1"><a href="{{route('users.edit',$user->id)}}" class="btn btn-warning btn-block"><span class="glyphicon glyphicon-edit"></span> Update</a></td>
						<td class="col-md-1">
							{!! Form::open(['method' => 'DELETE', 'route'=>['users.destroy', $user->id]]) !!}
							{!! Form::button('<span class="glyphicon glyphicon-remove"></span> Delete', ['class' => 'btn btn-danger btn-block', 'type'=>'submit']) !!}
							{!! Form::close() !!}
						</td>
					</tr>
				@endforeach

				</tbody>
				<tfoot>
				<tr>
					<th>Id.</th>
					<th>Full name</th>
					<th>Language</th>
					<th>Role</th>
					<th>Registration Date</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				</tfoot>
			</table>
		</div>

	</div>

@stop