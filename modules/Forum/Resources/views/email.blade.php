<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
</head>
<body>
<h4>Máte nový komentár na vlákno: {{$thread->title}}</h4>


<div>
    Nájdete ho na stránke:{{ URL::to('forum/thread/'.$thread->id) }}.<br/>
</div>

</body>
</html>