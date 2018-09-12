# qiaqia
qiaqia(洽洽)是一个WEBIM，一个可以一键部署到网页端的客服工具。

beta 0.1

实现的目标。客服端登录并接收客户端的请求，相互之间可以发送消息。消息发送后会记录在redis中，这样刷新页面可以读取之前发送的消息。完善客服端左侧的列表，能够动态的列出来客户端用户的列表。
设定上线，一个客服端，最多同时处理10个客户端的请求，超出则需要分配给其他的客服。
客户端如果没有分配到用户，则需要他等待或者留言。

客户端连接服务器成功后需要将自己的头部信息发送给客服端。
客服端的头像是随机分配的。

以上是 beta 0.1 的特性冷冻阶段需要实现的。

功能列表：

- [x] [客户弹窗](#1.1)
- [x] [客服管理面板](#1.2)
- [x] [服务端](#1.3)
- [x] [支持一个客服分配多个客户](#1.4)

更新日志：

2018.9.13

- [x] 修复BUG：客服端在切换客户后，发送的信息客户端接收不到。（原因是以前的khtofd,fdtokh是一对一的关系，后面上来的客户端会覆盖前面的数据所致）
- [x] 新增功能：客服端增加了全新的登录和注册，增加了退出，增加了ACL权限控制，增加了404页面，增加了全新的头像图片。
- [x] 考虑到客服人数不会太多，数据暂时使用的文本文件存储。

2018.9.14

- [x] 新客户端上线或离线退出都会通知对应的客服端及时刷新左侧的在线客户端列表
- [ ] BUG：如果客户端未被分配或客服不在线则不推送刷新信息
- [x] 需优化：左侧客户端列表应包括在线的和以往服务过的客户端记录，在线的头像右下角标在线浮标。
- [x] 优化发送信息格式，使其更加严谨。
- [x] 优化客户端样式，去掉头像使其更简洁明了
