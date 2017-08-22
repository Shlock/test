@if(!empty($data))
    @foreach ($data as $item)
        <php>
            if($item['countGoods'] > 0 && $item['countService'] == 0){
                $url = u('Goods/index',['id'=>$item['id'],'type'=>1, 'urltype'=>2]);
            }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                $url = u('Goods/index',['id'=>$item['id'],'type'=>2, 'urltype'=>2]);
            }else{
                $url = u('Seller/detail',['id'=>$item['id'], 'urltype'=>2]);
            }
        </php>
    <li class="clearfix" data-id="{{$item['id']}}" onclick="window.location.href='{{ $url }}'" @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif>
        <a href="javascript:;">
            <div class="x-naimg">
                <img src="@if(!empty($item['logo'])) {{formatImage($item['logo'],200,200)}} @else {{ asset('wap/community/client/images/x5.jpg')}} @endif" />
            </div>
            <div class="x-index4r">
                <p class="c-black">{{$item['name']}}</p>
                <p class="x-pjlstct  c-green f12 mt5" style="margin-left:0px;">
                        <span class="star-rank">
                            <!-- 五颗星总长70px，此时星级的长度用百分比控制 -->
                            <span class="star-score" style="width:{{$item['score'] * 20}}%;"></span>
                        </span>
                        @if($item['orderCount'] > 0)
                        <span style="line-height:25px;padding-left:5px;">已售 {{$item['orderCount']}}</span>
                    @else
                        <span style="line-height:25px;padding-left:5px;"></span>
                        @endif
                        <span class="fr">
                            <i class="x-addico"></i>
                            <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                        </span>
                </p>
                <p class="c-green f12 mt5">{!! $item['freight'] !!}</p>
            </div>
        </a>
    </li>
    @endforeach
@endif