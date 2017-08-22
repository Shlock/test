@extends('admin._layouts.base')

@section('right_content')
<php>
	$status = [
		0 => '待处理',
		1 => '进行中',
		2 => '已完成',
	];

</php>
	@yizan_begin
    <yz:list>
    	<search> 
			<row>
				<item label="物业公司">
					{{$seller['name']}}
				</item>
				<item label="小区名称">
					{{$seller['district']['name']}}
				</item>
				<item name="sellerId" type="hidden"></item>
			</row>
			<row>
				<item name="build" label="楼宇"></item>
				<item name="room" label="单元"></item>
				<item name="name" label="业主名 "></item>
				<!-- <item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>  
				<item label="状态">
					<yz:select name="status" options="0,1,2,3" texts="全部,待处理,处理中,已完成" selected="$search_args['status']"></yz:select>
				</item>-->
				<btn type="search"></btn>
			</row>
		</search> 
		<btns>
			<linkbtn label="导出到EXCEL">
				<attrs>
					<url>{{ u('Property/repairexport', ['sellerId'=>$sellerId] ) }}</url>
				</attrs>
			</linkbtn>
		</btns>
		<tabs>
            <navs>
                <nav label="待处理">
                    <attrs>
                        <url>{{ u('Property/repairindex',['sellerId'=>$sellerId ,'status'=>'0','nav'=>'1']) }}</url>
                        <css>@if( $nav == 1) on @endif</css>
                    </attrs>
                </nav>
                <nav label="进行中">
                    <attrs>
                        <url>{{ u('Property/repairindex',['sellerId'=>$sellerId ,'status'=>'1','nav'=>'2']) }}</url>
                        <css>@if( $nav == 2) on @endif</css>
                    </attrs>
                </nav>
                <nav label="已完成">
                    <attrs>
                        <url>{{ u('Property/repairindex',['sellerId'=>$sellerId ,'status'=>'2','nav'=>'3']) }}</url>
                        <css>@if( $nav == 3) on @endif</css>
                    </attrs>
                </nav>
            </navs>
        </tabs>
		<table>
			<columns>
				<column code="id" label="编号" width="50"></column>
				<column code="type" label="类型" width="50">
					<p>{{ $list_item['types']['name'] }}</p>
				</column>
				<column code="build" label="楼栋号" width="100">
					<p>{{ $list_item['build']['name'] }}</p>
				</column>
				<column code="roomNum" label="房间号" >
					<p>{{ $list_item['room']['roomNum'] }}</p>
				</column>
				<column code="owner" label="业主" >
					<p>{{ $list_item['room']['owner'] }}</p>
				</column>
				<column code="mobile" label="电话" >
					<p>{{ $list_item['room']['mobile'] }}</p>
				</column>
				<column code="status" label="状态" >
					<p>{{ $status[$list_item['status']] }}</p>
				</column>
				<column code="createTime" label="提交日期" >
					<p>{{ yzday($list_item['createTime']) }}</p>
				</column>
				<actions width="80">
					<action label="查看详情" >
						<attrs>
							<url>{{ u('Property/repairdetail',['sellerId'=>$sellerId, 'id'=> $list_item['id']]) }}</url>
						</attrs>
					</action>
				</actions>
			</columns>
		</table>
    </yz:list>
    @yizan_end

@stop
@section('js')
@stop
