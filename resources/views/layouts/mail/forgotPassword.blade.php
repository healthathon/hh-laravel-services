<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Forgot Password</title>
</head>
<body>
<div class="container-fluid">
    <p> Hello {!! $name !!}, </p>
    <p> Our System has generated an password for you, please use this for during login operation and you
        change your password from the application. </p>
    <p>Your new login credentials: <br/><br/>
        <b>Email:</b> {!! $email !!}<br/>
        <b>Password:</b> {!! $password !!}
    </p>
    <p><b>Regards</b><br/>Mansi<br/>Happily Health</p>
</div>
</body>
</html>