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
                  <img src="/images/avatar/avator_02.jpg" alt="">
                  <h5 class="q-title" align="center">jiangbianwanghai <br/><br/><b>~ | ~</b> <a href="/logout">退出</a> <b>~ | ~</b></h5>
                </div>
              </div>
              <hr/>
              <div class="listview lv-user m-t-20" id="kh_left_menu"></div>
            </div>
            <div class="ms-body">
              <div class="listview lv-message">
                {% if khid is not empty %}
                <div class="lv-header-alt clearfix">
                  <div id="ms-menu-trigger">
                    <div class="line-wrap">
                      <div class="line top"></div>
                      <div class="line center"></div>
                      <div class="line bottom"></div>
                    </div>
                  </div>
                  <div class="lvh-label hidden-xs">
                    <div class="lv-avatar pull-left"> <img src="/images/{{ curr_kh['avatar'] }}" alt="{{ curr_kh['avatar'] }}"> </div>
                    <span class="c-black">{{ curr_kh['uid'] }}<span style=" margin-left:8px; position:absolute; margin-top:12px;width: 8px;height: 8px;line-height: 8px; border-radius: 50%; background-color:#80d3ab;"></span></span>
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
                  <div style="font-size:12px; color:#ccc; text-align: right">TA的浏览器信息:{{ curr_kh['ua'] }}</div>
                </div>
                {% endif %}
                <div class="lv-body" id="ms-scrollbar-right" style="overflow:scroll; overflow-x: hidden; height:520px;">
                </div>
                <div class="clearfix"></div>
                {% if khid is not empty %}
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
    <script type="text/javascript"> kfid ='{{ account }}'; </script>
    <script type="text/javascript"> khid ='{{ khid }}'; </script>
    <script src="/js/socket.js"></script>
  </body>
</html>
