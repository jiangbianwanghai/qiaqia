<!DOCTYPE html>
  <html lang="zh-CN">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>客服面板 - QiaQia WebIM</title>
      <link href="./css/bootstrap.min.css" rel="stylesheet">
      <link href="./css/messsages.css" rel="stylesheet">
      <link href="./fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <style type="text/css">
        body {
          background-image: url(./images/geometry.png);
        }
        #ms-scrollbar::-webkit-scrollbar-track{
          background-color:#CCCCCC;
        }
        #ms-scrollbar::-webkit-scrollbar{
          width: 7px;
          background-color: #F5F5F5;
        }
        #ms-scrollbar::-webkit-scrollbar-thumb{
          background-color:#eeeeee;
          -webkit-box-shadow: inset 0 0 0px rgba(0,0,0,0.3);
        }
        .ms-new{
          box-shadow:0 2px 5px rgba(0,0,0,0.16),0 2px 10px rgba(0,0,0,0.12);
          background-color:#2196f3;
        }
      </style>
    </head>
    <body>
      <div class="container-fluid">
        <div class="container ng-scope">
          <div class="block-header">
            <h2> </h2>
          </div>
          <div class="card m-b-0" id="messages-main" style="box-shadow:0 0 40px 1px #c9cccd;">
            <div class="ms-menu" style="overflow:scroll; overflow-x: hidden;" id="ms-scrollbar">
              <div class="ms-block">
                <div class="ms-user">
                  <img src="./images/avatar.jpg" alt="">
                  <h5 class="q-title" align="center">工号:{{ account }} <br/><b>5</b> New Messages</h5>
                </div>
              </div>
              <div class="ms-block">
                <a class="btn btn-primary btn-block ms-new" href="#"><span class="glyphicon glyphicon-envelope"></span>&nbsp; New Message</a>
              </div>
              <hr/>
              <div class="listview lv-user m-t-20">
                <div class="lv-item media active">
                  <div class="lv-avatar pull-left"> <img src="./images/bhai.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Ashwani Singh Yadav</div>
                    <div class="lv-small"> Acadnote a world class website is processing surveys for </div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/ajit.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title"><b>Ajit Gupta</b><span class="pull-right">10 new</div>
                    <div class="lv-small"><b>Hello bro whatsup , how are you</b></div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/chota.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title"><b>Deepak Yadav</b><span class="pull-right">2 new</span></div>
                    <div class="lv-small"><b>aur bhai collage kse chale rhai hai </b></div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/sumit.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Sumit kumar</div>
                    <div class="lv-small">aur suna kya haal hai bhai, aur</div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/sega.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Sage Kalia</div>
                    <div class="lv-small">abey kaha chala gya ?? mar gya kya ??</div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/gan.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Gagandeep Singh</div>
                    <div class="lv-small">yeh ley eamil address sachin.yadav1212@gmail.com</div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"><img src="./images/vasu.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Vasu</div>
                    <div class="lv-small">kal se classess start hai koi holiday nahi hai </div>
                  </div>
                </div>
                <div class="lv-item media">
                  <div class="lv-avatar pull-left"> <img src="./images/abc.jpg" alt=""> </div>
                  <div class="media-body">
                    <div class="lv-title">Deepu Singh</div>
                    <div class="lv-small">okk byee gud night dude kal baaat karte hai </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="ms-body">
              <div class="listview lv-message">
                <div class="lv-header-alt clearfix">
                  <div id="ms-menu-trigger">
                    <div class="line-wrap">
                      <div class="line top"></div>
                      <div class="line center"></div>
                      <div class="line bottom"></div>
                    </div>
                  </div>
                  <div class="lvh-label hidden-xs">
                    <div class="lv-avatar pull-left"> <img src="./images/bhai.jpg" alt=""> </div>
                    <span class="c-black">Ashwani Singh Yadav<span style=" margin-left:8px; position:absolute; margin-top:12px;width: 8px;height: 8px;line-height: 8px; border-radius: 50%; background-color:#80d3ab;"></span></span>
                  </div>
                  <ul class="lv-actions actions list-unstyled list-inline">
                    <li> <a href="#" > <i class="fa fa-check"></i> </a> </li>
                    <li> <a href="#" > <i class="fa fa-clock-o"></i> </a> </li>
                    <li> <a data-toggle="dropdown" href="#" > <i class="fa fa-list"></i></a>
                      <ul class="dropdown-menu user-detail" role="menu">
                        <li> <a href="">Latest</a> </li>
                        <li> <a href="">Oldest</a> </li>
                      </ul>
                    </li>
                    <li> <a data-toggle="dropdown" href="#" data-toggle="tooltip" data-placement="left" title="Tooltip on left"><span class="glyphicon glyphicon-trash"></span></a>
                      <ul class="dropdown-menu user-detail" role="menu">
                        <li> <a href="">Delete Messages</a> </li>
                      </ul>
                    </li>
                  </ul>
                </div>
                <div class="lv-body" id="ms-scrollbar" style="overflow:scroll; overflow-x: hidden; height:520px;">
                  <div class="lv-item media">
                    <div class="lv-avatar pull-left"> <img src="./images/bhai.jpg" alt=""> </div>
                    <div class="media-body">
                      <div class="ms-item"> <span class="glyphicon glyphicon-triangle-left" style="color:#000000;"></span> We have an aim to educate and provide you the power to make website anything. Anytime. We don't teach, we educate.We provide Tutorials for many Programming languages on our website. </div>
                      <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; 05/10/2015 at 09:00</small> </div>
                    </div>
                    <div class="lv-item media right">
                      <div class="lv-avatar pull-right"> <img src="./images/avatar.jpg" alt=""> </div>
                      <div class="media-body">
                        <div class="ms-item"> We started this site with clear mission that we want to deliver complete details knowledge of Programming to our audience. We are sharing this knowledge in all areas that you can see in our site. </div>
                        <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; 05/10/2015 at 09:30</small> </div>
                      </div>
                      <div class="lv-item media">
                        <div class="lv-avatar pull-left"> <img src="./images/bhai.jpg" alt=""> </div>
                        <div class="media-body">
                          <div class="ms-item"> It's gives the power to synthesis anything anywhere you want to. Its the ultimate tool to solve any problem. And we help you excel in that by working with you. </div>
                          <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; 20/02/2015 at 09:33</small>
                        </div>
                      </div>
                      <div class="lv-item media right">
                        <div class="lv-avatar pull-right"> <img src="./images/avatar.jpg" alt=""> </div>
                        <div class="media-body">
                          <div class="ms-item"> The basic essence of life is to learn, explore and synthesis. We provide you with the tools to make your dreams come true.Our website is totally for free and available 24/7 and does not consume your data packs and works like a charm on the supersonic lovely internet.
                          </div>
                          <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; 05/10/2015 at 10:10</small>
                        </div>
                      </div>
                      <div class="lv-item media">
                        <div class="lv-avatar pull-left"> <img src="./images/bhai.jpg" alt=""> </div>
                        <div class="media-body">
                          <div class="ms-item"> Acadnote a world class website is processing surveys for every student who wants to do something new and different in the field of academics. so it is a right place for every student to share their opinions about their present academics so this website can provide every single student requirements and it is possible for us to do if every student explains about their academics requirements. Last but not the least tell the needs and collect your study materials which we will provide to you.
                          </div>
                          <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; 05/10/2015 at 10:24</small>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="lv-footer ms-reply"> <textarea rows="10" placeholder="Write messages..."></textarea> <button class=""><span class="glyphicon glyphicon-send"></span></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script type="text/javascript" src="./css/jquery.js"></script>
    <script src="./css/bootstrap.min.js"></script>
    {% if account is empty %}
    <!-- 登录面板 -->
    <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">客服登录面板</h4>
          </div>
          <div class="modal-body">
            <form id="login-form">
              <div class="form-group">
                <label for="recipient-name" class="control-label">工号：</label>
                <input type="text" name="account" class="form-control" id="recipient-name">
              </div>
              <div class="form-group">
                <label for="message-text" class="control-label">密码：</label>
                <input type="password" name="password" class="form-control" id="recipient-name">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="submit" id="submit" class="btn btn-primary">提交</button>
          </div>
        </div>
      </div>
    </div>
    {% endif %}
    <script src="//cdn.bootcss.com/jquery.form/3.20/jquery.form.min.js"></script>
    <script src="./js/socket.js"></script>
    <script>
      $(function(){
        $('#login').modal({
          backdrop:'static',
          keyboard:false
        })
        $('#login').on('shown.bs.modal', function () {
          $('#recipient-name').focus();
        })
        $("#submit").click(function() {
          $("#login-form").ajaxSubmit({
            type:"post",
            url: "/login",
            dataType: "JSON",
            success: function(data) {
              if(data.code) {
                alert(data.msg);
              } else {
                $('#login').modal('hide');
              }
            }
          });
          return false;
        });
      });
    </script>
  </body>
</html>
