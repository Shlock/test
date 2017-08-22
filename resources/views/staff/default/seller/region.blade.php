@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" id="J_save_c">保存</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('content')
    <div class="admin-shop-setting-address">
        <div class="list-block">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-input">
                            <select id="s_province" name="s_province" class="select_border">
                                <option value="-1">请选择</option>
                                @foreach($region as $v)
                                    <option value="{{ $v['id']}}">{{ $v['name']}}</option>
                                @endforeach
                            </select>
                            <i class="icon iconfont">&#xe609;</i>
                        </div>
                        <div class="item-input">
                            <select id="s_city" name="s_city" class="select_border">
                                <option value="-1">请选择</option>
                            </select>
                            <i class="icon iconfont">&#xe609;</i>
                        </div>
                        <div class="item-input area" id="area">
                            <select id="s_county" name="s_county" class="select_border">
                                <option value="-1">请选择</option>
                            </select>
                            <i class="icon iconfont">&#xe609;</i>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
    $(function(){
           $('.select_border').on("change", function(){
                var id = $(this).val();
               var data = {!! json_encode($region) !!};
                $.each(data,function(i,v){
                     if( id == v.id){
                         $("#s_city,#s_county").html('');
                         $("#s_city,#s_county").append("<option value=-1>请选择</option>");
                         $.each( v.city,function(i,vs){
                             $("#s_city").attr({"data-pid":id});
                             $("#s_city").append("<option  value="+vs.id+">"+vs.name+"</option>");
                         });
                     }
                });
            });

       $('#s_city').on("change", function(){
           var id = $(this).val();
           var pid = $(this).data('pid');
           var data =  {!! json_encode($region) !!};
            $("#s_county").html('');
            $("#s_county").append("<option value=-1>请选择</option>");
           $.each(data,function(i,v){
               if( pid == v.id){
                   $.each( v.city,function(i,city){
                       if( id == city.id){
                           if(city.area){
                               document.getElementById("area").style.display="";//显示  
                               // $(".area").removeClass('none');
                               $.each( city.area,function(i,area){
                                   $("#s_county").append("<option data-pid ="+id+" value="+area.id+">"+area.name+"</option>");
                               });
                           }else{
                               document.getElementById("area").style.display="none";//隐藏  
                               // $(".area").addClass('none');
                           }
                       }
                   });
               }
           });
       });

        $('#J_save_c').on("click", function(){
            var provinceId = document.getElementById('s_province').value;
            var cityId     = document.getElementById('s_city').value;
            var areaId     = document.getElementById('s_county').value;
            if(provinceId == -1){
                $.toast("请选择城市");
                return false;
            }
            if(cityId == -1){
                $.toast("请选择市区");
                return false;
            }
            areaId = areaId != -1 ? areaId : '';
            var data = {
                'provinceId':provinceId,
                'cityId':cityId,
                'areaId':areaId
            }
            $.showIndicator();
            $.post("{{ u('Seller/savecity') }}",{'data':data},function(){
                var url ="{{ u('Seller/info') }}";
                JumpURL(url,'#seller_info_view',2)
            },"json");
        });
});
</script>
@stop