<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    {{ $mailData['title'] }},
    {{ $mailData['firstname'] }} <br>
    {{ $mailData['body1']}}<br>
    {{ $mailData['body2']}}<br>


    <a href="{{ url('user/verify', $mailData['verification_code'])}}">Confirm my email address </a>

    <br/>
</div>

</body>
</html>