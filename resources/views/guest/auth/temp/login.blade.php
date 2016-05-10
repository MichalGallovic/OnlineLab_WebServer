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
							<label class="col-md-4 control-label">E-Mail Address</label>
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
								<button name="local" type="submit" class="btn btn-info">Login</button>


								<a class="btn btn-link" href="{{ url('/auth/register') }}">Register</a>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<a class="btn btn-primary" href="provider/facebook" role="button">Login with Facebook</a>
								<a class="btn btn-danger" href="provider/google" role="button">Login with Google</a>
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
		<div class="col-md-10 col-md-offset-1">
			<div id="map" style="height: 700px"></div>
		</div>
	</div>
</div>
@endsection

@section("page_js")
	@parent
	<script>
		var map, heatmap;

		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 3,
				center: {lat: 37.775, lng: 40.434},
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				options:{
					minZoom: 2,
					maxZoom: 7
				}
			});

			heatmap = new google.maps.visualization.HeatmapLayer({
				data: getPoints(),
				map: map
			});

			heatmap.set('radius', 20);
		}


		function getPoints() {
			var data = [];
			@foreach($items as $item)
				data.push(new google.maps.LatLng({{$item->location}}));
			@endforeach
					return data;
		}

	</script>
	<script async defer
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqYpSfHr483N-c9yzrqeZ3d56BRdqHq7M&libraries=visualization&callback=initMap">
	</script>
@endsection
