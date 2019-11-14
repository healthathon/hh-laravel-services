<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Forgot Password</title>
</head>
<body>
<div class="container-fluid">
    <p> Hello {!! $name !!}, </p>
    <p> This email confirms that your password has been changed.</p>
    <p>To log on to the application, use the following credentials<br/><br/>
        <b>Email:</b> {!! $email !!}<br/>
        <b>Password:</b> {!! $password !!}
    </p>
    <p>
        If you have any questions or encounter any problems signing in, please contact <a
                href="mailto:support@happilyhealth.com">App Administrator</a>.
    </p>
    <p><b>Regards</b><br/>Mansi<br/>Happily Health</p>
</div>
</body>
</html>