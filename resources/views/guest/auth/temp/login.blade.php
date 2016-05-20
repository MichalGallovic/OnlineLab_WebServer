@extends('guest.layouts.default')

@section('content')
<div class="container-fluid">

	<div class="row" style="margin-top: 40px">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">

							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if(Session::has('fail'))
						<div class="alert alert-danger">{!! Session::get('fail') !!}</div>
					@endif

					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address / AIS username</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button name="local" type="submit" class="btn btn-primary">Login</button>


								<a class="btn btn-link" href="{{ url('/auth/register') }}">Register</a>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">

								<a class="btn btn-social btn-facebook" href="provider/facebook" role="button"><span class="fa fa-facebook"></span>Sign in with Facebook</a>
								<a class="btn btn-social btn-google" href="provider/google" role="button"><span class="fa fa-google"></span>Sign in with Google</a>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button name="ldap" class="btn btn-default" >Login with AIS</button>
								<a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div id="map"></div>
		</div>

	</div>
</div>
@endsection

@section("page_css")
	@parent
	<link href="{{ asset('css/bootstrap-social.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section("page_js")
	@parent
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script>

		google.charts.load('current', {'packages':['geochart']});
		google.charts.setOnLoadCallback(drawRegionsMap);

		var countries = {!! json_encode($items) !!}
		console.log(countries);

		var map = [['Country code', 'Number of visits']];


		for(var i in countries)
			map.push([i, parseInt(countries [i])]);


		console.log(map);

		function drawRegionsMap() {

			var data = google.visualization.arrayToDataTable(map);

			var options = {
				colorAxis: {colors: ['lightblue', 'blue']}
			};

			var chart = new google.visualization.GeoChart(document.getElementById('map'));

			chart.draw(data, options);
		}

	</script>
@endsection
