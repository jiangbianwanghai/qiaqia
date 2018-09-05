var socket = new WebSocket('ws://192.168.1.110:9502');
socket.onopen = function () {
    console.log('Connected!');
    var messageObj = {kf:1,uid:"1101"};
    var messageJson = JSON.stringify(messageObj);
    socket.send(messageJson);
};

socket.onmessage = function (event) {
    //alert(event.data);
    //document.getElementById("ms-scrollbar").innerHTML = event.data;
    $("#ms-scrollbar-right").append("<div class=\"lv-item media\"><div class=\"lv-avatar pull-left\"> <img src=\"./images/bhai.jpg\" alt=\"\"></div><div class=\"media-body\"><div class=\"ms-item\"> <span class=\"glyphicon glyphicon-triangle-left\" style=\"color:#000000;\"></span> "+event.data+"</div><small class=\"ms-date\"><span class=\"glyphicon glyphicon-time\"></span>&nbsp; 05/10/2015 at 09:00</small></div></div>");

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
