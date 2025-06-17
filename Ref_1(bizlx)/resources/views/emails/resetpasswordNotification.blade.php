<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Password Reset</title>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  <p>You (or someone else) requested a password reset. Click the button below to reset your password:</p>

  <p>
    <a href="{{ $url }}"
       style="display:inline-block;
              padding:10px 20px;
              background-color:#2d3748;
              color:#fff;
              text-decoration:none;
              border-radius:4px;">
      Reset Password
    </a>
  </p>

  <p>This link will expire in 60 minutes.</p>

  <p>If you did not request a reset, just ignore this email.</p>

  <hr>
  <p>Bizlx.com</p>
</body>
</html>
