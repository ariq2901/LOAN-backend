<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
  <title>Email verified 🎉</title>
  <style>
    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      font-family: 'Nunito', sans-serif;
    }
    html, body {
      background-color: rgb(250, 250, 250);
    }
    .info {
      width: 100vw;
      height: 100vh;
      display: flex;
      padding-bottom: 20vh;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .img-wrapper {
      width: 25%;
      overflow: hidden;
    }
    .img-wrapper img {
      width: 100%;
    }
    .info span {
      margin-top: 15px;
      font-size: 30px;
      font-weight: bold;
      color: rgb(0, 209, 147);
    }
    .info p {
      font-size: 15px;
    }
    /*^ Breakpoint mobile */
    @media screen and (max-width: 375px) {
      .img-wrapper {
        width: 60%;
        overflow: hidden;
      }
      .info span {
        font-size: 20px;
      }
      .info p {
        font-size: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="info">
    <div class="img-wrapper">
      <img src={{ assets('verifiedSVG.svg') }} alt="verified img">
    </div>
    <span>Your Email has been Verified</span>
    <p>back to the app and login with your account!</p>
  </div>
</body>
</html>