<script src="http://res.xiami.net/pc/lay/lib.js"></script> 
<script src="{{ asset('static/layer/layer/layer.min.js') }}"></script> 
<script>
//信息框
	function msgBox(msgtype,msgtitle,msgid,msgmsg) {
		var pageii = $.layer({
				type: msgtype, //0：信息框（默认），1：页面层，2：iframe层，3：加载层，4：tips层
				title: msgtitle, //标题栏
				area: ["auto", "auto"],//宽高
				border: [0], //去掉默认边框
				shade: [0.5,'#000'],//[1,'#000']不透明  [0.5,'#000']半透明 [0,'#000']全透明
				shadeClose: false,//关闭遮罩层
				bgcolor: '#fff',//弹窗窗体背景色
				closeBtn: [0, true], //关闭按钮
				shift: "top", //左上(left-top),上(top), 右上(right-top),右下(right-bottom),下(bottom),左下(left-bottom),左('left')
				信息框
				if(msgtype==1){
					if(!msgid){
						var msgid = 17;
					}
					dialog: {
						//0感叹号 1绿色勾 3红色禁止(斜杠) 4问号 5红色禁止(横杠) 7锁 8哭脸 9笑脸 14邮件 15下载 16等待 大于16小信息图标
					    type: msgid,
					    msg: msgmsg,
					}
				}
				//页面层
				else if(msgtype==2){
					page: {
						dom: '#'+msgid, //自定义文档内容容器
						//html:"<div style='width:400px;height:200px;'>文档区域</div>",
					}
				}
				//加载层
				else if(msgtype==3){
					if(!msgid){
						var msgid = 2;
					}
					loading: {
					    type: msgid,
					}
				}

			});
	}
</script>