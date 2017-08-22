<div id="tab1" class="tab active">
    <div class="list-block media-list y-sylist">
        <ul>
            @foreach ($data as $item)
                <?php
                    if($item['countGoods'] > 0 && $item['countService'] == 0){
                        $url = u('Goods/index',['id'=>$item['id'],'type'=>1, 'urltype'=>2]);
                    }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                        $url = u('Goods/index',['id'=>$item['id'],'type'=>2, 'urltype'=>2]);
                    }else{
                        $url = u('Seller/detail',['id'=>$item['id'], 'urltype'=>2]);
                    }
                ?>
                <li data-id="{{$item['id']}}" >
                    <a href="{{ $url }}" class="item-link item-content">
                        <div class="item-media">
                            <img src="@if(!empty($item['logo'])) {{formatImage($item['logo'],200,200)}} @else {{ asset('wap/community/client/images/x5.jpg')}} @endif" width="73" />
                        </div>
                        <div class="item-inner">
                            <div class="item-subtitle y-sytitle">{{$item['name']}}</div>
                            <div class="item-title-row f12 c-gray mt5 mb5">
                                <div class="item-title">
                                    <div class="y-starcont">
                                        <div class="c-gray4 y-star">
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        </div>
                                        <div class="c-red f12 y-startwo" style="width:{{$item['score'] * 20}}%;">
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        </div>
                                    </div>
                                    <span class="c-gray f12">已售 {{ $item['orderCount'] ? $item['orderCount'] : 0 }}</span>
                                </div>
                                <div class="item-after">
                                    <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                                </div>
                            </div>
                            <div class="item-subtitle c-gray">
                                <span class="mr10 ">@if($item['serviceFee'] == 0)无起送价@else<label class="c-red">{{$item['serviceFee']}}</label>元起送费@endif</span>运费
                                <span class="c-red">{{$item['deliveryFee']}}</span>元起
                                <!-- <span class="c-red ml10">28</span>包邮 -->
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
