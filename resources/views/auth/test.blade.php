<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <title>Reset Your Password</title>
  <style>
    * {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    html, body {
      overflow-x: hidden;
    }
    meter {
      width: 40%;
    }
    meter::-webkit-meter-bar {
      border: 1px solid rgb(230, 226, 226);
      background: none;
    }

        /* Webkit based browsers */
    meter[value="1"]::-webkit-meter-optimum-value { background: red; }
    meter[value="2"]::-webkit-meter-optimum-value { background: rgb(225, 219, 54); }
    meter[value="3"]::-webkit-meter-optimum-value { background: rgb(229, 255, 0); }
    meter[value="4"]::-webkit-meter-optimum-value { background: rgb(8, 255, 8); }

    /* Gecko based browsers */
    meter[value="1"]::-moz-meter-bar { background: red; }
    meter[value="2"]::-moz-meter-bar { background: rgb(225, 219, 54); }
    meter[value="3"]::-moz-meter-bar { background: rgb(255, 247, 0); }
    meter[value="4"]::-moz-meter-bar { background: rgb(8, 255, 8); }

    .container {
      width: 100vw;
      height: 100vh;
      display: flex;
      flex-direction: row;
      margin-top: -5vh;
      justify-content: center;
      align-items: center;
    }
    .image-wrapper {
      width: 40%;
      overflow: hidden;
    }
    .image-wrapper img {
      width: 70%;
    }
    .card {
      margin-top: 15px;
      width: 30%;
      height: 45%;
      box-shadow: -1px 7px 14px 1px rgba(0,0,0,.2);
    }
    .card::before {
      content: "";
      position: absolute;
      top: 18%;
      left: 35%;
      background-image: url("/hiasan.png");
      width: 80%;
      background-repeat: no-repeat;
      transform: scale(.3);
      height: 100%;
      z-index: -1;
    }
    .form {
      padding: 20px 20px;
    }
    .title {
      font-size: 30px;
      font-weight: 500;
      color: #212529;
    }
    .subtitle {
      margin-top: 2;
      font-size: 13px;
      color: #67686a;
    }
    .form form {
      margin-top: 4%;
    }
    input {
      border: 1px solid #999999;
      height: 35px;
      width: 100%;
      border-radius: 3px;
      padding: 0 15px;
      font-size: 17px;
    }
    input:focus {
      outline: none;
    }
    .meter-pass {
      margin-top: 5px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    #password_confirmation {
      margin-top: 15px;
    }
    .submit {
      margin-top: 15px;
      width: 100%;
      border: none;
      height: 35px;
      border-radius: 2px;
      background-color: #8e4bfa;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
    }
    .submit:focus {
      border: none;
      outline: none;
    }
    .submit:active {
      background-color:#8336ff;
    }
    #password-strength-text {
      font-size: 13px;
      color: #6e7074;
    }
    @media screen and (max-width: 375px){
      .container {
        flex-direction: column !important;
      }
      .image-wrapper {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
      .card {
        width: 80% !important;
        height: 40% !important;
      }
      .card::before {
        display: none;
      }
      .title {
        font-size: 21px;
      }
      .subtitle {
        font-size: 10px;
      }
    }
    
    @media screen and (max-width: 1300px){
      .card {
        width: 40%;
        height: 45%;
      }
      .card::before {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
    }
    @media screen and (max-width: 1300px){
      .card {
        width: 60%;
        height: 45%;
      }
      .image-wrapper {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
      .card::before {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
    }
    @media screen and (max-width: 970px){
      .card {
        width: 50%;
        height: 45%;
      }
      .image-wrapper {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
      .card::before {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
    }
    @media screen and (max-width: 530px){
      .card {
        width: 80%;
        height: 45%;
      }
      .image-wrapper {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
      .card::before {
        display: none;
        visibility: hidden;
        pointer-events: none;
      }
    }
    .alert {
      widows: 100%;
      height: 30px;
      padding: 0 10px;
      background-color: rgb(243, 26, 73);
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-wrapper">
      <img src={{asset("forget.png")}}>
    </div>
    <div class="card">
      <div class="alert" id="alert">
        <p id="alert-info"></p>
      </div>
      <div class="form">
        <p class="title">Change password</p>
        <p class="subtitle">change with your new passwod here</p>
        <form action="" method="post">
          <input type="password" id="password" required placeholder="password">
          <div class="meter-pass">
              <meter id="password-strength-meter"
              min="0" max="4">
            </meter>
            <p id="password-strength-text"></p>
          </div>
          <input type="password" onchange="check()" id="password_confirmation" required placeholder="Confirm your password">
          <button type="submit" class="submit">Reset Password</button>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
  <script>
    var strength = {
      0: "Worst",
      1: "Bad",
      2: "Weak",
      3: "Good",
      4: "Strong"
    }
    var password = document.getElementById('password');
    var meter = document.getElementById('password-strength-meter');
    var text = document.getElementById('password-strength-text');

    password.addEventListener('input', function() {
      var val = password.value;
      var result = zxcvbn(val);
      console.log('result.score', result.score);
      // Update the password strength meter
      meter.setAttribute("value", result.score);

      // Update the text indicator
      if (val !== "") {
        text.innerHTML = "Strength: " + strength[result.score]; 
      } else {
        text.innerHTML = "";
      }
    });
    
  </script>
  <script async>
    function check() {
      const password2 = document.getElementById('password').value;
      const password_confirmation = document.getElementById('password_confirmation').value;
      const alert = document.getElementById('alert');
      const alert_info = document.getElementById('alert-info');
      if( password_confirmation !== password2 ) {
        alert.style.display = "block";
        alert.style.color = "white";
        alert_info.innerHTML = "Password confirmation doesn't match";
      } else {
        alert.style.display = "none";
        alert_info.innerHTML = "";
      }
    }
  </script>
</body>
</html>