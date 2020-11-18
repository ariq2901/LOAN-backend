<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Reset Password</title>
</head>
<body>
  <form action="/api/reset-password" method="post">
    @csrf
    @method("POST")
    <input type="hidden" name="token" value={{ $token }}>
    <input type="email" name="email" id="email" placeholder="email">
    <input type="password" name="password" id="password" placeholder="pass">
    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="pass_c">
    <button type="submit">submit</button>
  </form>
</body>
</html>