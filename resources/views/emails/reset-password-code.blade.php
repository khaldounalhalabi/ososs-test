<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name')}} - Reset Your Password</title>
</head>
<body>
<p>Your Reset Password Code is :</p>
<p><strong>{{$code}}</strong></p>
<p>This verification code is valid until : {{$validUntil}}</p>
</body>
</html>
