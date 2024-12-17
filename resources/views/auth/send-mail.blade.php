<!-- resources/views/emails/otp.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Mã OTP</title>
</head>
<body>
    <h1>Hi {{$user->username}}</h1>
    <p>
        <a href="{{route('password.reset', $token)}}">Click here</a> to verify your account.
    </p>
</body>
</html>
