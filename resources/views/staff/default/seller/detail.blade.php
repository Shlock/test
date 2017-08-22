@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
@stop
@section('content')
    <div class="fine-bor pl-085 bg_fff">
        <div class="fine-bor pr-085  info-tr">
            <a href="#"  class=" w_b">
                <p class="w_b_f_1 ">店铺名称: {{$seller['name']}}</p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
        <table width="100%" class="fine-bor info-tr">
            <tr>
                <td >
                    <p class="w_b_f_1 ">店铺LOGO:</p>
                </td>
                <td class="w340">
                    <img  class="img_box" src="{{$seller['img']}}" alt=""/>
                </td>
                <td  class="tr w130">
                    <i class="icon iconfont">&#xe64b;</i>
                </td>
            </tr>
        </table>
        <div class="fine-bor pr-085 info-tr">
            <a href="#" class=" w_b">
                <p class="w_b_f_1 text-overflow">店铺公告: {{$seller['article']}}</p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
    </div>

    <div class="blank070"></div>

    <div class="fine-bor fine-bor-top pl-085 bg_fff">
        <div class="fine-bor pr-085  info-tr">
            <div href="#"  class=" w_b">
                <p class="w_b_f_1 ">营业状态: 正常营业</p>
                <label class="label-switch">
                    <input type="checkbox" value="2" name="checkbox">
                    <div class="checkbox"></div>
                </label>
            </div>
        </div>
        <div class="fine-bor pr-085 info-tr">
            <a href="#" class=" w_b">
                <p class="w_b_f_1 text-overflow">营业时间: &nbsp;{{$seller['businessHour']['weeks']}}-{{$seller['businessHour']['hours']}}</p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
        <div class="fine-bor pr-085 info-tr">
            <a href="#" class=" w_b">
                <p>配送时间: &nbsp;</p>
                <p class="w_b_f_1 text-overflow">
                    @foreach($seller['deliveryTime']['stimes']  as $k => $v)
                        {{$v}}-{{$seller['deliveryTime']['etimes'][$k]}}<br/>
                    @endforeach
                </p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
        <div class="fine-bor pr-085 info-tr">
            <a href="#" class=" w_b">
                <p class="w_b_f_1 text-overflow">联系电话:{{$seller['tel']}}</p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
    </div>

    <div class="blank070"></div>

    <div class="fine-bor fine-bor-top pl-085 bg_fff">
        <div class="pr-085 info-tr">
            <a href="#" class=" w_b">
                <p class="w_b_f_1 text-overflow">服务范围: {{$seller['region']}} </p>
                <i class="icon iconfont">&#xe64b;</i>
            </a>
        </div>
    </div>

    <div class="blank070"></div>

    <div class="fine-bor  fine-bor-top pl-085 bg_fff">
        <table width="100%" class="fine-bor info-tr">
            <tr>
                <td >
                    <p class="w_b_f_1 ">店铺介绍:</p>
                    <p class="p_con">
                        {{$seller['brief']}}
                    </p>
                </td>
                <td  class="tr w130">
                    <i class="icon iconfont">&#xe64b;</i>
                </td>
            </tr>
        </table>

    </div>
@stop
