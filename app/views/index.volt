<!DOCTYPE html>
  <html lang="zh-CN">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>客服面板 - QiaQia WebIM</title>
      <link href="/css/bootstrap.min.css" rel="stylesheet">
      <link href="/css/messsages.css" rel="stylesheet">
      <link href="/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <style type="text/css">
        body {
          background-image: url(/images/geometry.png);
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
    <section>
      <div class="container-fluid">
        <div class="container ng-scope">
          <div class="block-header">
            <h2> </h2>
          </div>
          <div class="card m-b-0" id="messages-main" style="box-shadow:0 0 40px 1px #c9cccd;">
            <div class="ms-menu" style="overflow:scroll; overflow-x: hidden;" id="ms-scrollbar">
              <div class="ms-block">
                <div class="ms-user">
                  <img src="/images/avatar.jpg" alt="">
                  <h5 class="q-title" align="center">工号:{{ account }} <br/><b>5</b> New Messages</h5>
                </div>
              </div>
              <div class="ms-block">
                <a class="btn btn-primary btn-block ms-new" href="#"><span class="glyphicon glyphicon-envelope"></span>&nbsp; New Message</a>
              </div>
              <hr/>
              <div class="listview lv-user m-t-20">
                {% if khid is not empty %}
                {% for key,item in khid %}
                <div class="lv-item media{{ uid == key ? ' active' : '' }}">
                  <div class="lv-avatar pull-left"> <img src="/images/{{ item['avatar'] }}" alt="{{ uid }}"> </div>
                  <div class="media-body">
                    <div class="lv-title"><a href="/chat/{{ key }}">{{ key }}</a></div>
                    <div class="lv-small">{{ item['ua'] }}</div>
                  </div>
                </div>
                {% endfor %}
                {% endif %}
              </div>
            </div>
            <div class="ms-body">
              <div class="listview lv-message">
                {% if uid is not empty %}
                <div class="lv-header-alt clearfix">
                  <div id="ms-menu-trigger">
                    <div class="line-wrap">
                      <div class="line top"></div>
                      <div class="line center"></div>
                      <div class="line bottom"></div>
                    </div>
                  </div>
                  <div class="lvh-label hidden-xs">
                    <div class="lv-avatar pull-left"> <img src="/images/bhai.jpg" alt=""> </div>
                    <span class="c-black">{{ kh['uid'] }}<span style=" margin-left:8px; position:absolute; margin-top:12px;width: 8px;height: 8px;line-height: 8px; border-radius: 50%; background-color:#80d3ab;"></span></span>
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
                  <div style="font-size:12px; color:#ccc; text-align: right">TA的浏览器信息:{{ kh['ua'] }}</div>
                </div>
                {% endif %}
                <div class="lv-body" id="ms-scrollbar-right" style="overflow:scroll; overflow-x: hidden; height:520px;">
                  {% if history is not empty %}
                    {% for item in history %}
                      {% if item['me'] %}
                      <div class="lv-item media right">
                        <div class="lv-avatar pull-right"> <img src="/images/avatar.jpg" alt=""> </div>
                        <div class="media-body">
                            <div class="ms-item">{{ item['msg'] }}</div>
                            <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; {{ item['time'] }}</small>
                        </div>
                      </div>
                      {% else %}
                      <div class="lv-item media">
                        <div class="lv-avatar pull-left"> <img src="/images/bhai.jpg" alt=""> </div>
                        <div class="media-body">
                            <div class="ms-item">{{ item['msg'] }}</div>
                            <small class="ms-date"><span class="glyphicon glyphicon-time"></span>&nbsp; {{ item['time'] }}</small>
                        </div>
                      </div>
                      {% endif %}
                    {% endfor %}
                  {% endif %}
                </div>
                <div class="clearfix"></div>
                {% if uid is not empty %}
                <div class="lv-footer ms-reply"> <textarea rows="10" id="text" placeholder="请输入内容，支出换行(ctrl+回车即可发送)"></textarea> <button id="push_button" class=""><span class="glyphicon glyphicon-send"></span></button></div>
                {% endif %}
                  </div>
                </div>
              </div>
            </div>
          </div>
    </section>
    <script type="text/javascript" src="/css/jquery.js"></script>
    <script src="/css/bootstrap.min.js"></script>
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
    <script type="text/javascript"> uid ='{{ uid }}'; </script>
    <script src="/js/socket.js"></script>
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
