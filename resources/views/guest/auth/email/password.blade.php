<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Hi, {{$user->user->getFullName()}}</h2>

<p>
    You recently requested to reset your password. Click the link below to reset it.
</p>

<a href="{{ url('password/reset/'.$token) }}" class="btn btn-success">Reset your password</a>

</body>
</html>