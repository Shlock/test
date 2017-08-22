<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>编辑器</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{ asset('js/jquery.1.8.2.js') }}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('static/ueditor/ueditor.config.js')}}"></script>
    <script type="text/javascript" charset="utf-8" src="{{ asset('static/ueditor/ueditor.all.js')}}"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="{{ asset('static/ueditor/lang/zh-cn/zh-cn.js')}}"></script>

    <style type="text/css">
        div{
            width:100%;
			height:400px;
        }
        #submit{
            margin-top:10px;
            width: 100%;
            height:40px;
            line-height: 40px;;
            background: #ff2d4b;
            display: block;
            text-shadow:none;
            border-radius:5px; /*{global-radii-buttons}*/
            text-align:center;
            text-decoration: none;
            color:#fff;
            z-index:999;
            position:relative;

        }
    </style>
</head>
<body>
<div>
    <script id="editor" type="text/plain">{!!$data['brief']!!}</script>
    {{--<a href="javascript:;" id="submit">保存</a>--}}
</div>
    <script type="text/javascript">

        //实例化编辑器
        //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
        var ue = UE.getEditor('editor');
        function getBrief() {
            var content = ue.getContent();
            if (window.stub) {
                window.stub.jsMethod(content);
            } else {
                return content;
            }
        }

    </script>
</body>
</html>