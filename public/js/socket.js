var socket = new WebSocket('ws://192.168.1.110:9502');
socket.onopen = function () {
    console.log('Connected!');
    var messageObj = {kf:1,uid:"1101"};
    var messageJson = JSON.stringify(messageObj);
    socket.send(messageJson);
};

//发送消息
$("#push_button").click(function(){
    var text = $("#text").val();
    if (text) {
        var messageObj = {post:1,role:'kf',msg:text};
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
    if (Eventjson.me) {
        $("#ms-scrollbar-right").append("<div class=\"lv-item media right\"><div class=\"lv-avatar pull-right\"> <img src=\"./images/avatar.jpg\" alt=\"\"> </div><div class=\"media-body\"><div class=\"ms-item\"> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
    } else {
        $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"./images/bhai.jpg\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+Eventjson.msg+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; "+Eventjson.time+"</small></div></div>");
    }
    $('#ms-scrollbar-right').scrollTop( $('#ms-scrollbar-right')[0].scrollHeight );
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
