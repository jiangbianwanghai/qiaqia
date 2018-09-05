var socket = new WebSocket('ws://192.168.1.110:9502');
socket.onopen = function () {
    console.log('Connected!');
    var messageObj = {kf:1,uid:"1101"};
    var messageJson = JSON.stringify(messageObj);
    socket.send(messageJson);
};
//与服务器连接断开触发
socket.onclose = function () {
    console.log('Lost connection!');
};

//与服务器连接出现错误触发
socket.onerror = function () {
    console.log('Error!');
};
