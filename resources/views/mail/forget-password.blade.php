<!DOCTYPE html>
<html>
<head>
	<title>MakeWebBetter | Reset Password</title>
	<style>
        /* Inline CSS styles */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #F9F9F9;
            border-radius: 5px;
        }
        .logo {
            text-align: center;
        }
        .logo img {
            width: 100px;
        }
        .message {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://spent.rf.gd/transparent_logo.png" alt="TASC Logo">
        </div>
        <div class="message">
            <p>Hello ' . {{$mail_data['name']}} . ',</p>
            <p>We received a request to reset your password. If you did not make this request, please ignore this email.</p>
            <p>To reset your password, click the button below:</p>
            <p><a style="color:#ffff;" href="' . {{$mail_data['link']}}  . '" class="button">Reset Password</a></p>
            <p>If you cannot click the button, please copy and paste the following URL into your browser:</p>
            <p>' . $reset_link . '</p>
            <p>This link will expire in 1 hr for security reasons.</p>
            <p>If you have any questions, please contact us at test@gamil.com.</p>
        </div>
    </div>
</body>
</html>