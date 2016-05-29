@extends('user.layouts.default')

@section('content')


	<div id="users">

		<div id="users_list">

			<table class="table table-hover">
				<thead>
				<tr>
					<th>{{trans("users::default.USR_ID")}}</th>
					<th>{{trans("users::default.USR_NAME_FULL")}}</th>
					<th>{{trans("users::default.USR_LANGUAGE")}}</th>
					<th>{{trans("users::default.USR_ROLE")}}</th>
					<th>{{trans("users::default.USR_REGISTRATION_DATE")}}</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>

				@foreach ($users as $user)
					<tr>
						<td>{{ $user->id }}</td>
						<td>{{ $user->getFullName() }}</td>
						<td>{!! $user->language_code !!}</td>
						<td>{{ $user->role }}</td>
						<td>{{ $user->created_at }}</td>
						<td class="col-md-1"><a href="{{url('users',$user->id)}}" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-search"></span> {{trans("users::default.USR_SHOW")}}</a></td>
						<td class="col-md-1"><a href="{{route('users.edit',$user->id)}}" class="btn btn-warning btn-block"><span class="glyphicon glyphicon-edit"></span> {{trans("users::default.USR_EDIT")}}</a></td>
						<td class="col-md-1">
							{!! Form::open(['method' => 'DELETE', 'route'=>['users.destroy', $user->id]]) !!}
							{!! Form::button('<span class="glyphicon glyphicon-remove"></span> '.trans("users::default.USR_DELETE"), ['class' => 'btn btn-danger btn-block', 'type'=>'submit']) !!}
							{!! Form::close() !!}
						</td>
					</tr>
				@endforeach

				</tbody>
				<tfoot>
				<tr>
					<th>{{trans("users::default.USR_ID")}}</th>
					<th>{{trans("users::default.USR_NAME_FULL")}}</th>
					<th>{{trans("users::default.USR_LANGUAGE")}}</th>
					<th>{{trans("users::default.USR_ROLE")}}</th>
					<th>{{trans("users::default.USR_REGISTRATION_DATE")}}</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				</tfoot>
			</table>
		</div>

	</div>

@stop