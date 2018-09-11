$('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );

var socket = new WebSocket('ws://192.168.1.110:9502');
socket.onopen = function () {
    $("#ms-scrollbar-right").append("<div style=\"text-align: center; font-size: 10px; margin-bottom: 150px; color: #ccc\">-—— 连接服务器成功 ——-</div>");
    console.log('Connected!');
    var messageObj = {kf:1,uid:kfid};
    var messageJson = JSON.stringify(messageObj);
    socket.send(messageJson);
    if (khid) {
        //读取聊天日志
        $.ajax({
          type: "GET",
          dataType:'jsonp',
          url: "http://qiaqia.im/chatlogkf/"+khid,
          success : function(data) {
            if (data.code == 200) {
                if (data.data) {
                    for(var p in data.data){
                      if (data.data[p].me) {
                        $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                      } else {
                        $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+data.data[p].msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+data.data[p].time+"</small></div></div>");
                      }
                    }
                    $("#ms-scrollbar-right").append("<hr /><div style=\"text-align: center; font-size: 10px; margin-bottom: 150px; color: #0000cd\">以上是之前的聊天记录</div>");
                    $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
                }
            }
          }
        })
    }
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
                    for(var p in data.data){
                        leftmenu = leftmenu + "<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+data.data[p].avatar+"\" alt=\""+data.data[p].avatar+"\"> </div><div class=\"media-body\"><div class=\"lv-title\"><a href=\"/#chat!"+data.data[p].uid+"\">"+data.data[p].uid+"</a></div><div class=\"lv-small\">"+data.data[p].ua+"</div></div></div>";
                    }
                    $("#kh_left_menu").html(leftmenu);
                }
            }
          }
        })
    }
    //更新右侧列表
    if (Eventjson.op == 'msg') {
        if (Eventjson.me) {
            $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"/images/"+Eventjson.avatar+"\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
        } else {
            $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"/images/"+Eventjson.avatar+"\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
        }
        $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
    }
};

//与服务器连接断开触发
socket.onclose = function () {
    $("#ms-scrollbar-right").append("<div style=\"text-align: center; font-size: 10px; margin-bottom: 150px; color: #ccc\">-—— 与服务器连接断开 ——-</div>");
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
