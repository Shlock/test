@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">添加服务</span>
					<a href="" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="g-crtserct" style="width:100%;">
						<div class="clearfix">
							<a href="javascript:;" class="f-wzmsbtn fl ml20"></a>
							<ul class="fl m-serlstct u-addser">
								<li>
									<img src="{{ u('images/fwimg1.png') }}" alt="">
									<div class="u-fwjsct clearfix">
										<span class="fl f-fg">简约</span>
										<p class="fl tc">收入：99/小时</p>
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
								<li>
									<img src="{{ u('images/fwimg1.png') }}" alt="">
									<div class="u-fwjsct clearfix">
										<span class="fl f-fg">简约</span>
										<p class="fl tc">收入：99/小时</p>
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
								<li>
									<img src="{{ u('images/fwimg1.png') }}" alt="">
									<div class="u-fwjsct clearfix">
										<span class="fl f-fg">简约</span>
										<p class="fl tc">收入：99/小时</p>
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
								<li>
									<img src="{{ u('images/fwimg1.png') }}" alt="">
									<div class="u-fwjsct clearfix">
										<span class="fl f-fg">简约</span>
										<p class="fl tc">收入：99/小时</p>
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
								<li>
									<img src="{{ u('images/fwimg1.png') }}" alt="">
									<div class="u-fwjsct clearfix">
										<span class="fl f-fg">简约</span>
										<p class="fl tc">收入：99/小时</p>
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
								<li>
									<img src="{{ u('images/mor.png') }}" alt="">
									<div class="u-fwjsct clearfix tc">
										暂无
									</div>
									<p class="u-zzct none">
										<img src="{{ u('images/ico/rightico.png') }}" alt="">
									</p>
								</li>
							</ul>
						</div>
						<p class="tc" style="margin-top:35px;">
							<a href="" class="btn f-170btn ml20">完成</a>
						</p>
						<div class="m-fwwzc">
							<p>
								当平台推荐的服务没有您所从事的，可通过新增服务制定自己个性服务，制定的服务通过总后台工作人员审核(审核1至2个工作日完成)，
								全平台服务人员将可以选择您的个性化服务！！

							</p>
						</div>
						<p class="tc" style="margin-top:35px;">
							<a href="{{ u('Goods/addnewgoods') }}" class="btn f-170btn ml20">新增服务</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
@stop
