<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <script src="{{ asset('wap/community/client/js/jquery-2.1.4.min.js') }}" type="text/javascript"></script>
    <title>测试支付</title>
</head>
<body style="font-size:larger">
    <h2>测试支付</h2>
    支付方法:
    <select id="payment">
        <option value="alipay">支付宝</option>
        <option value="weixin">微信支付</option>
        <option value="unionapp">银联支付</option>
    </select>
    <input type="button" value="测试支付" onclick="btnOK_onclick()" />
    <p>
        回调函数 PayComplete(string result), result值Success或Fail
    </p>
    <script type="text/javascript">
        function btnOK_onclick()
        {
            try
            {
                alert("window.App" + window.App);
                if (window.App)
                {
                    var result = $.ajax({ url: "{{u('Index/createpaylog')}}", async: false });

                    alert("window.App.pay_sdk" + window.App.pay_sdk);

                    window.App.pay_sdk(result);
                }
            }
            catch (ex)
            {
                alert(ex.toString());
            }
        }
        // 回调函数
        function PayComplete(result)
        {
            alert(result);
        }
    </script>
</body>
</html>