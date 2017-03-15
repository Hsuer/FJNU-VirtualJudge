# FJNU-OnlineJudge

基于Laravel与Swoole的ACM-ICPC在线评测平台~

目前已完成VirtualJudge的功能，主要功能&特点（VirtualJudge）：

- Web端与虚拟评测服务均为PHP编写，运行部署简单快速
- 虚拟评测服务基于Swoole+Redis，高并发，高性能
- 提供虚拟评测API，轻松分离Web端与虚拟服务端
- 预览版本(Preview Version) : http://acm.fjnu.edu.cn/vjudge/ 

Web端已完成80%，预计4月中旬放出VirtualJudge完整应用，已经支持HDOJ,POJ,FOJ三大主流评测平台。

## 截图
![index](http://7lrwu1.com1.z0.glb.clouddn.com/index.png)

![problemset](http://7lrwu1.com1.z0.glb.clouddn.com/problem.png)

![problem](http://7lrwu1.com1.z0.glb.clouddn.com/download.png)

![contestset](http://7lrwu1.com1.z0.glb.clouddn.com/contest.png)

![contestshow](http://7lrwu1.com1.z0.glb.clouddn.com/contest_show.png)