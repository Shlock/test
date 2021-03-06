<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My菜--分享领优惠券</title>
	<script src="{{ asset('wap/community/client/js/jquery-2.1.4.min.js') }}"></script>
	<script type="text/javascript">
		
		$(document).ready(function(){
			$('.container').width($(window).width()).height($(window).height());
		});
		
	</script>
	<style>
	body{
		height: 100%;
		width: 100%;
		background:url('/wap/community/client/images/share/content_bg.jpg') no-repeat;
		overflow:scroll;
		background-size:100% 100%;
		margin: 0px;
		padding: 0px;
	}

	.container{
		width: 100%;
		height: 100%;
		margin: 0;
		padding: 0;
	}

	.content{
		width: 80%;
		height: 85%;
		margin: auto;
		margin-top: 100px;
		background:url('/wap/community/client/images/share/text_bg.png') no-repeat;
		background-size:100% 100%;
	}
	.top{
		display: block;
		padding-top: 120px;
		text-align: center;
	}
	.mid{
		display: block;
		padding-top: 150px;
		text-align: center;
	}
	.mid p, .down p{
		font-family: "微软雅黑";
		font-size:40px;
		line-height: 10px;
	}
	.down {
		display: block;
		padding-top: 80px;
		line-height: 10px;
		text-align: center;
	}
	.down p{
		font-weight:bold;
	}

	.black_overlay{  display: none;  position: absolute;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color:rgba(255,255,255,0.6);  z-index:1001;  -moz-opacity: 0.8;  opacity:.80;  filter: alpha(opacity=80);  
	}  
  	.white_content {  
  	display: none;  
  	position: absolute;  
  	top: 15%;  
  	left: 10%;
  	width: 80%;  
  	 
 /* 	border: 16px solid orange;  */
  	background-color: white;  
  	z-index:1002;  
  	overflow: auto;  
  } 
  .white_content a{
  	float: right;
  	color: black;
  	text-decoration: none;
  	font-size: 40px;
  }
  .white_content p{
  	font-family: "微软雅黑";
  	font-size: 35px;
  	padding: 0px 50px;
  }
	</style>
</head>

<body>
	<div class="container">
		<div class="content">
			<div class="top">
				<span style="font-family:'微软雅黑';font-size:50px;font-weight:500;">￥</span>
				<span style="font-family:'微软雅黑';font-size:240px;">20</span>
				<span style="font-family:'微软雅黑';font-size:50px;font-weight:500;">优惠券</span></br>
				<span style="font-family:'微软雅黑';font-size:50px;color:#e34b58;" >您的省心之选</span>
			</div>
			<div class="mid">
				<p>邀请小伙伴来注册，立送TA20元</p>
				<p>好友完成首单，再返您20元</p>
				<p>邀请越多，奖励越多</p>
			<!--	<p style="color:#e34b58;">奖励详情>></p>  -->
			<p><a href="javascript:void(0)" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'" style="color:#e34b58;text-decoration: none;">奖励详情>> </a> </p>

			</div>
			<div class="down">
				<p>您需要完成首次注册，才</p>
				<p>能邀请好友获得奖励</p>
				<button onclick="location.href='/User/regshare?sharedByUserId={{$uid}}'" style="width:70%;height:84px;background:#ed1b24;color:white;font-family:'微软雅黑';font-size:45px;color:white;border:0px;margin-top:10px;">马上去注册</button>
			</div>
		</div>
	</div>

	<div id="light" class="white_content"> 
		<a href="javascript:void(0)" onclick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">&nbsp;×&nbsp;</a></br>
		<p style="color: red;text-align: center;">奖励详情</p>
	    <p>1.邀请新用户注册，TA立刻获得100元优惠券礼包。</p>
		<p>2.被邀请到用户完成注册之后，也将获得20元优惠券。</p>
		<p>4.同一手账号视为同一个用户，新注册用户仅限成功领取一次优惠礼包。</p>
		<p>5.活动解释权归My菜上海泛微商贸有限公司所有。</p>	
    </div> 
<div id="fade" class="black_overlay"> 
</div>
</body>
</html>