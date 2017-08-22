@extends('admin._layouts.base')
@section('css')
<style type="text/css">
    .x-post{padding: 20px;}
    .x-post1{padding: 10px 0; border-bottom: 1px solid #ddd;}
    .x-post1 .pic{width: 50px; height: 50px; border-radius: 100%;}
    .x-post1 .postr{margin-left: 60px;}
    .x-post1 .lz{padding: 2px 3px; background: #3399ff; color: #fff; border-radius: 2px;}
    .x-post1 .from{color: #999;}
    .x-pmain .x-ptt{font-size: 15px; font-weight: bold;}
    .x-pmain img{width: 520px; margin-bottom: 10px;}
    .x-post1 .edit{padding: 2px 8px; border: 1px solid #999; border-radius: 5px; font-size: 12px; color: #1e1e1e;}
    .x-post1 .delete{padding: 2px 8px; border: 1px solid #999; border-radius: 5px; font-size: 12px; color: #ff0000;}
    .x-post1 a:hover{text-decoration: none;}
    .x-lzmain{background: #f2f2f2;}
    .x-lzmain .b{font-weight: bold;}
</style>
@stop
@section('right_content')
	<div class="x-post">
        <div class="x-post1">
            <div class="f12">
                <img src="{{$data['user']['avatar']}}" class="pic fl" />
                <div class="postr">
                    <p class="pt5">{{$data['user']['name']}}<span class="lz ml5">楼主</span><span class="fr">{{formatTime($data['createTime'])}}之前</span></p>
                    <p class="from">{{$data['plate']['name']}}</p>
                </div>
            </div>
            <div class="x-pmain mt20">
                <p class="x-ptt tc mb20">{{$data['title']}}</p>
                <p class="mb20">{!! $data['content'] !!}</p>
                @foreach(explode(',', $data['images']) as $image)
                <p class="tc"><img src="{{$image}}" /></p>
                @endforeach 
            </div>
            <div class="clearfix">
                <a href="{{u('ForumPosts/destroy',['id'=>$data['id']])}}" class="delete fr">删除</a>
                <a href="{{u('ForumPosts/edit',['id'=>$data['id']])}}" class="edit fr mr20">编辑</a>
            </div>
        </div>
        @foreach($data['list'] as $item)
        <php>
        $i++;
        </php>
        <div class="x-post1">
            <div class="f12">
                <img src="{{$item['user']['avatar']}}" class="pic fl" />
                <div class="postr">
                    <p class="pt5">{{$item['user']['name']}}<span class="fr">{{$i}}楼</span></p>
                    <p><span class="from">{{$item['plate']['name']}}</span><span class="fr">{{formatTime($item['createTime'])}}之前</span></p>
                    <p>{{$item['content']}}</p> 
                    @if($item['posts'])
                    <div class="x-lzmain p10 mb10">
                        <p class="b">引用 {{$item['user']['name']}} 的回复</p>
                        <p>{!! $item['posts']['content'] !!}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="clearfix">
                <a href="{{u('ForumPosts/destroy',['id'=>$item['id']])}}" class="delete fr">删除</a>
                <a href="{{u('ForumPosts/edit',['id'=>$item['id']])}}" class="edit fr mr20">编辑</a>
            </div>
        </div>
        @endforeach 
        @include('admin._layouts.pager')
    </div>
    <script type="text/javascript">

    </script>
@stop