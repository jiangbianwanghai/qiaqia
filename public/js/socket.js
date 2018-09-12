$.ajax({
  type: "GET",
  dataType:'jsonp',
  url: "http://weqia.live/kh/live",
  success : function(data) {
    if (data.code == 200) {
        if (data.data) {
            var url=window.location.href;
            var arg = url.split("#");
            var param = arg[1].split("!");
            var leftmenu = '';
            for(var p in data.data){
                var onlinepaopao = '';
                if (data.data[p].online) {
                    onlinepaopao = "<span style=\" margin-left:-10px; position:absolute; margin-top:24px;width: 10px;height: 10px;line-height: 8px; border-radius: 50%; background-color:#80d3ab;\"></span>";
                }
                var act = '';
                if (param[1] == data.data[p].uid) {
                    act = ' active';
                    $("#editor").css('display','block');
                    $("#top_nav").css('display','block');
                    //读取聊天日志
                    $.ajax({
                      type: "GET",
                      dataType:'jsonp',
                      url: "http://weqia.live/chatlogkf/"+param[1],
                      success : function(data) {
                        if (data.code == 200) {
                            if (data.data) {
                                $("#ms-scrollbar-right").append("<div style=\"text-align: center; font-size: 10px; margin-top: 20px; margin-bottom: 10px;color: #0000cd\">以下是之前的部分聊天记录，<a href=\"javascript:;\">查看全部</a></div>");
                                for(var p in data.data){
                                  if (data.data[p].me) {
                                    $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                                  } else {
                                    $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                                  }
                                }

                                $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
                            }
                        }
                      }
                    })
                }
                leftmenu = leftmenu + "<div class=\"lv-item media chat"+act+"\" data-id=\""+data.data[p].uid+"\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\""+data.data[p].avatar+"\">"+onlinepaopao+"</div><div class=\"media-body\"><div class=\"lv-title\">"+data.data[p].uid+"<span id=\"k_"+data.data[p].uid+"\"></span></div><div class=\"lv-small ua\">"+data.data[p].ua+"</div></div></div>";
            }
            $("#kh_left_menu").html(leftmenu);
        }
    }
  }
})

$('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );

var socket = new WebSocket('ws://192.168.1.110:9502');
socket.onopen = function () {
    console.log('Connected!');
    var messageObj = {kf:1,uid:kfid};
    var messageJson = JSON.stringify(messageObj);
    socket.send(messageJson);
};

//监听键盘回车键
$("body").keydown(function(event) {
     if (event.ctrlKey && event.keyCode == 13) {
         $('#push_button').click();
     }
 });

//发送消息
$("#push_button").click(function(){
    var text = $("#text").val();
    var khid = $("#top_nav").attr('data-id');
    if (text) {
        var messageObj = {post:1,role:'kf',msg:text,khid:khid};
        var messageJson = JSON.stringify(messageObj);
        socket.send(messageJson);
        $("#text").val("");
        $('#text').css("background-color","white");
    } else {
        $('#text').focus();
        $('#text').css("background-color","yellow");
    }
});

socket.onmessage = function (event) {
    Eventjson = JSON.parse(event.data);
    //刷新左侧客户列表
    if (Eventjson.op == 'flash_kh_menu') {
        $.ajax({
          type: "GET",
          dataType:'jsonp',
          url: "http://weqia.live/kh/live",
          success : function(data) {
            if (data.code == 200) {
                if (data.data) {
                    var leftmenu = '';
                    for(var p in data.data) {
                        var onlinepaopao = '';
                        if (data.data[p].online) {
                            onlinepaopao = "<span style=\" margin-left:-10px; position:absolute; margin-top:24px;width: 10px;height: 10px;line-height: 8px; border-radius: 50%; background-color:#80d3ab;\"></span>";
                        }
                        leftmenu = leftmenu + "<div class=\"lv-item media chat\" data-id=\""+data.data[p].uid+"\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\""+data.data[p].avatar+"\">"+onlinepaopao+"</div><div class=\"media-body\"><div class=\"lv-title\">"+data.data[p].uid+"<span id=\"k_"+data.data[p].uid+"\"></span></div><div class=\"lv-small ua\">"+data.data[p].ua+"</div></div></div>";
                    }
                    $("#kh_left_menu").html(leftmenu);
                } else {
                    $("#kh_left_menu").text('');
                }
            }
          }
        })
    }
    //更新右侧列表
    if (Eventjson.op == 'send_msg') {
        if (Eventjson.me) {
            $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"/images/"+Eventjson.avatar+"\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
        } else {
            var currkh = $("#top_nav").attr('data-id');
            if (currkh == Eventjson.from) {
                $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+Eventjson.avatar+"\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
            } else {
                lightX(Eventjson.from);
                lightT();
            }
        }
        $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
    }
};

//与服务器连接断开触发
socket.onclose = function () {
    console.log('Lost connection!');
};

//与服务器连接出现错误触发
socket.onerror = function () {
    console.log('Error!');
};

//获取cookie
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start!=-1) {
            c_start = c_start + c_name.length + 1
            c_end=document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}

//设置cookie
function setCookie(c_name,value,expiredays) {
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

$("body").on("click", ".chat", function () {
    var avatar = $(this).find('img').attr('src');
    var ua = $(this).find('.ua').text();
    var khid = $(this).attr('data-id');
    window.location.href = '/#chat!'+khid;
    $('.chat').removeClass("active");
    $(this).addClass("active");
    $("#editor").css('display','block');
    $("#top_nav").css('display','block');
    $("#top_nav").attr('data-id', khid);
    $("#curr_kh_avatar").attr('src', avatar);
    $("#curr_kh_uid").text(khid);
    $("#curr_kh_ua").text('TA的浏览器信息：'+ua);
    $("#ms-scrollbar-right").empty();
    //读取聊天日志
    $.ajax({
      type: "GET",
      dataType:'jsonp',
      url: "http://weqia.live/chatlogkf/"+khid,
      success : function(data) {
        if (data.code == 200) {
            if (data.data) {
                $("#ms-scrollbar-right").append("<div style=\"text-align: center; font-size: 10px; margin-top: 20px; margin-bottom: 10px;color: #0000cd\">以下是之前的部分聊天记录，<a href=\"javascript:;\">查看全部</a></div>");
                for(var p in data.data){
                  if (data.data[p].me) {
                    $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                  } else {
                    $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                  }
                }

                $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
            }
        }
      }
    })
});

function lightT() {
    var timerArr = showT();
    setTimeout(function() {//此处是过一定时间后自动消失
        clearT(timerArr);
    }, 5000);
}

function showT() { //有新消息时在title处闪烁提示
    var step=0, _title = document.title;
    var timer = setInterval(function() {
        step++;
        if (step==3) {step=1};
        if (step==1) {document.title='【　　　　　　】'+_title};
        if (step==2) {document.title='【您有新的消息】'+_title};
    }, 500);
    return [timer, _title];
}
function clearT(timerArr) {
    //去除闪烁提示，恢复初始title文本
    if(timerArr) {
        clearInterval(timerArr[0]);
        document.title = timerArr[1];
    };
}


function lightX(obj) {
    var timerArr = showX(obj);
    setTimeout(function() {//此处是过一定时间后自动消失
        clearX(timerArr);
    }, 5000);
}

function showX(obj) { //有新消息时在title处闪烁提示
    var step=0;
    var timer = setInterval(function() {
        step++;
        if (step==3) {step=1};
        if (step==1) {$("#k_"+obj).empty()};
        if (step==2) {$("#k_"+obj).html('<span style="margin-left:7px; position:absolute; margin-top:0px;width: 10px;height: 10px;line-height: 8px; border-radius: 50%; background-color:#ff0000;"></span>')};
    }, 500);
    return [timer];
}
function clearX(timerArr) {
    //去除闪烁提示，恢复初始title文本
    if(timerArr) {
        clearInterval(timerArr[0]);
    };
}
