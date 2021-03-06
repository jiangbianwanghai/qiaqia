//载入依赖的jquery.js
var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
var printservice = document.createElement("script");
printservice.type="text/javascript";
printservice.src ="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js";
head.insertBefore( printservice,head.firstChild );

//延迟加载js
setTimeout(function(){
    $(function(){
        //测试效果，没有啥用的
        $("#bt").click(function(){
            alert('Hello World');
        });


        var username = getCookie('username');
        if (!(username != null && username != "")) {
            username = 'uid_' + new Date().getTime();
            setCookie('username', username, 365)
        }

        //输出浮动弹窗
        $("body").append('<div id="fudong" style="border: #ccc solid 1px;background: #f8f8f8;width:300px; height:450px; z-index: 9999; position: fixed ! important; right: 20px; bottom: 20px;box-shadow:0 0 40px 1px #c9cccd;"><div style="height:40px;padding:10px;width:410px;line-heigt:20px; font-size:12px; color:#7c7c7c">客服：jiangbianwanghai</div><div id="push_content" style="color:gray; font-size:12px; background: #fff; padding: 10px;margin-bottom: 10px; height: 300px; overflow-y: scroll;"></div><div><div style="padding-left:10px;"><textarea rows="10" style="width:70%;resize:none;border-style:none;border-color:Transparent;overflow:auto;font-size:12px;padding:10px;font-style:normal;height:25px" id="text" placeholder="请输入内容(ctrl+回车即可发送)"></textarea><button id="push_button" style="float:right;border:0;height:45px;width:65px;font-size:16px;background:#f8f8f8;color:#999">发送</button></div></div></div>');

        //监听端口
        var socket = new WebSocket('ws://192.168.1.110:9502');

        //监听是否连接服务器成功触发
        socket.onopen = function () {
            console.log('Connected!');
            var ua = navigator.userAgent.toLowerCase();
            var messageObj = {kh:1,uid:username,ua:ua};
            var messageJson = JSON.stringify(messageObj);
            socket.send(messageJson);

            //读取聊天日志
            $.ajax({
              type: "GET",
              dataType:'jsonp',
              url: "http://weqia.live/chatlog/"+username,
              success : function(data) {
                if (data.code == 200) {
                    if (data.data) {
                        $("#push_content").append("<div style=\"width:260px;padding-bottom:10px; height:30px;text-align:center; float:left;\">以下是之前的部分聊天记录，<a href=\"javascript:;\">查看全部</a></div>");
                        for(var p in data.data){
                          if (data.data[p].me) {
                            $("#push_content").append("<div style=\"color:#7c7c7c;text-align:right;float:right;padding-bottom:20px;\"><div style='text-align:left;width:155px; background:#ecf0f1;padding:10px;border-radius:2px;box-shadow: 0 1.5px .5px rgba(0,0,0,.13);position:relative;'>"+data.data[p].msg+"</div><br />"+data.data[p].time+" from me</div>");
                          } else {
                            $("#push_content").append("<div style=\"color:#7c7c7c;text-align:left;float:left;padding-bottom:20px;\"><div style='width:155px; background:#f5f5f5;padding:10px;border-radius:2px;box-shadow: 0 1.5px .5px rgba(0,0,0,.13);position:relative;'>"+data.data[p].msg+"</div><br />"+data.data[p].time+"</div>");
                          }
                        }
                        $('#push_content').scrollTop( $('#push_content')[0].scrollHeight );
                    }
                }
              }
            })

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
                var messageObj = {post:1,role:'kh',msg:text};
                var messageJson = JSON.stringify(messageObj);
                socket.send(messageJson);
                $("#text").val("");
                console.log($("#text").val().indexOf("\n"));
                $('#text').css("background-color","white");
            } else {
                $('#text').focus();
                $('#text').css("background-color","yellow");
            }
        });

        //接收到服务器数据时触发
        socket.onmessage = function (event) {
            Eventjson = JSON.parse(event.data);
            if (Eventjson.op == 'send_msg') {
                if (Eventjson.from == 'system') {
                    $("#push_content").append("<div style=\"padding-top:10px;padding-bottom:10px;background-color:yellow;text-align:center;position:absolute;top:340px;left:0px;opacity:0.6;width:100%;height:20px;color:#ff0000\">"+Eventjson.msg+"</div>");
                } else {
                    if (Eventjson.me) {
                        $("#push_content").append("<div style=\"color:#7c7c7c;text-align:right;float:right;padding-bottom:20px;\"><div style='text-align:left;width:155px; background:#ecf0f1;padding:10px;border-radius:2px;box-shadow: 0 1.5px .5px rgba(0,0,0,.13);position:relative;'>"+Eventjson.msg+"</div><br />"+Eventjson.time+" from me</div>");
                    } else {
                        lightT()
                        $("#push_content").append("<div style=\"color:#7c7c7c;text-align:left;float:left;padding-bottom:20px;\"><div style='width:155px; background:#f5f5f5;padding:10px;border-radius:2px;box-shadow: 0 1.5px .5px rgba(0,0,0,.13);position:relative;'>"+Eventjson.msg+"</div><br />"+Eventjson.time+"</div>");
                    }
                }
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
