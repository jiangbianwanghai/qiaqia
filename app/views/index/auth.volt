<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>权限验证 - WeQia Live IM</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>

<body>
    <div class="lowin lowin-blue">
        <div class="lowin-brand">
            <img src="/images/avatar/avator_02.jpg" alt="logo">
        </div>
        <div class="lowin-wrapper">
            <div class="lowin-box lowin-login">
                <div class="lowin-box-inner">
                    <form id="signin-form">
                        <p>登录系统</p>
                        <div class="lowin-group">
                            <label>邮箱 <a href="#" class="login-back-link">Sign in?</a></label>
                            <input type="email" autocomplete="email" name="email" class="lowin-input">
                        </div>
                        <div class="lowin-group password-group">
                            <label>密码 <a href="#" class="forgot-link">忘记密码？</a></label>
                            <input type="password" name="password" autocomplete="current-password" class="lowin-input">
                        </div>
                        <button type="submit" id="signin_bt" class="lowin-btn login-btn">
                            登录
                        </button>

                        <div class="text-foot">
                            如果你还没有账户？ <a href="" class="register-link">注册一个吧</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lowin-box lowin-register">
                <div class="lowin-box-inner">
                    <form id="signup-form">
                        <p>创建你的账户</p>
                        <div class="lowin-group">
                            <label>用户名</label>
                            <input type="text" name="username" autocomplete="name" class="lowin-input">
                        </div>
                        <div class="lowin-group">
                            <label>邮箱</label>
                            <input type="email" autocomplete="email" name="email" class="lowin-input">
                        </div>
                        <div class="lowin-group">
                            <label>密码</label>
                            <input type="password" name="password" autocomplete="current-password" class="lowin-input">
                        </div>
                        <button type="submit" id="signup_bt" class="lowin-btn">
                            注册
                        </button>

                        <div class="text-foot">
                            如果您有帐号？ <a href="" class="login-link">登录</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="lowin-footer">
            &copy; 2018 <a href="http://weqia.live">weqia.live</a>
        </footer>
    </div>
    <script type="text/javascript" src="/css/jquery.js"></script>
    <script src="//cdn.bootcss.com/jquery.form/3.20/jquery.form.min.js"></script>
    <script src="/js/auth.js"></script>
    <script>
        Auth.init({
            login_url: '#login',
            forgot_url: '#forgot'
        });
      $(function(){
        $("#signin_bt").click(function() {
          $("#signin-form").ajaxSubmit({
            type:"post",
            url: "/signin",
            dataType: "JSON",
            success: function(data) {
              if(data.code) {
                alert(data.msg);
              } else {
                window.location.href=data.url;
              }
            }
          });
          return false;
        });
        $("#signup_bt").click(function() {
          $("#signup-form").ajaxSubmit({
            type:"post",
            url: "/signup",
            dataType: "JSON",
            success: function(data) {
              if(data.code) {
                alert(data.msg);
              } else {
                window.location.href=data.url;
              }
            }
          });
          return false;
        });
      });
    </script>
</body>
</html>
