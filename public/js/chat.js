var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
var printservice = document.createElement("script");
printservice.type="text/javascript";
printservice.src ="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js";
head.insertBefore( printservice,head.firstChild );
document.write('<div id="fudong" style="border:1px solid #454545; width:250px; height:350px; z-index: 9999; position: fixed ! important; right: 20px; bottom: 20px;box-shadow: 2px 2px 5px 5px #ccc;"><div><div id="push_content" style="color:gray; font-size:12px; border: #ccc solid 1px; padding: 10px; background: #f1f1f1;margin-bottom: 10px; height: 290px; overflow-y: scroll;"></div><div><div style="text-align: center"><input type="text" id="text" style="width: 180px;" name="content" placeholder="请输入需要推送的信息"><button id="push_button">推送</button></div></div></div></div>');
setTimeout(function(){
    $(document).ready(function(){
        $("#bt").click(function(){
            alert('Hello World');
        });
        //监听端口
var socket = new WebSocket('ws://192.168.1.110:9502');

//监听是否连接服务器成功触发
socket.onopen = function () {
    console.log('Connected!');
    socket.send("客服端上线");//重要!!客户端返回服务器
};
$("body").keydown(function(event) {
     if (event.keyCode == "13") {
         $('#push_button').click();
     }
 });
$("#push_button").click(function(){
    var text = $("#text").val();
    if (text) {
        socket.send(text);
        $("#text").val("");
        $('#text').css("background-color","white");
    } else {
        $('#text').focus();
        $('#text').css("background-color","yellow");
    }
});
//接收到服务器数据时触发
socket.onmessage = function (event) {
    //document.getElementById("push_content").innerHTML = event.data;
    var Cts = event.data;
    if(Cts.indexOf("客户") >= 0 ) {
        $("#push_content").append("<div style='text-align:left'>"+event.data+"<br /><br /></div>");
    } else {
        $("#push_content").append("<div style='text-align:right'>我:"+event.data+"<br /><br /></div>");
    }

    $('#push_content').scrollTop( $('#push_content')[0].scrollHeight );
};

//与服务器连接断开触发
socket.onclose = function () {
    console.log('Lost connection!');
};

//与服务器连接出现错误触发
socket.onerror = function () {
    console.log('Error!');
};

    });
},1000);

