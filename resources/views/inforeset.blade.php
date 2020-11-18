<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>reset password Info</title>
  <style>
    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
    }
    body {
      width: 100vw;
      height: 100vh;
    }
    .container{
      height: 90vh;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .image-wrapper {
      width: 100%;
      display: flex;
      justify-content: center;
      overflow: hidden;
    }
    .image-wrapper img {
      width: 30%;
    }
    .info-wrapper {
      margin-top: 20px;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      font-weight: 600;
      font-size: 30px;
      color: rgb(133, 77, 245);
    }
    .footer {
      width: 100%;
      height: 10vh;
      display: flex;
      justify-content: space-between;
    }
    .footer .img1 {
      height: 100%;
    }
    @media screen and (max-width: 375px) {
      .image-wrapper img {
        width: 50%;
      }
      .info-wrapper {
          font-size: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-wrapper">
      <img src={{asset("grup.png")}} alt="grup">
    </div>
    <div class="info-wrapper">
      <p>{{ $status }}</p>
    </div>
  </div>
  <div class="footer">
    <img src={{asset("grup2.png")}} class="img1">
    <img src={{asset("grup3.png")}} alt="img2" class="img2">
  </div>
</body>
</html>