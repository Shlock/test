<?php
error_reporting(E_ERROR);

$agent = strtolower($_SERVER['HTTP_USER_AGENT']); 

$isIphone = (strpos($agent, 'iphone')) ? true : false; 

$isIpad = (strpos($agent, 'ipad')) ? true : false; 

$isAndroid = (strpos($agent, 'android')) ? true : false; 

$downloadPath =  'http://' . $_SERVER["SERVER_NAME"] . ($_SERVER["SERVER_PORT"] == 80 ? "" : (":" . $_SERVER["SERVER_PORT"])). "/";

$existsIosApp = file_exists("app/iosstaffapp.plist");

if($existsIosApp)
{
    $iosAppUrl = file_get_contents("app/iosstaffapp.plist");
    
    $existsIosApp = $iosAppUrl != "";
}

$existsAndroidApp = file_exists("app/androidstaffapp.apk");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>下载页面</title>
    <!-- Bootstrap Core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="panel-body" style="text-align:center">
                <h1>
                    服务人员端
                </h1>
                <?php 
                      $downloadUrl = "#";
                      
                      if($existsIosApp && ($isIphone || $isIpad))
                      {
                          $downloadUrl = "{$iosAppUrl}";
                      }
                      else if($existsAndroidApp)
                      {
                          $downloadUrl = "{$downloadPath}app/androidstaffapp.apk";
                      }    
                ?>
                <div style="margin-top:1em" id="divDownload">
                    <a href="<?php echo $downloadUrl; ?>" class="btn btn-lg btn-success" style="padding:0.5em 3em 0.5em 3em">点击安装</a>
                    <script type="text/javascript">
                        document.addEventListener("WeixinJSBridgeReady", function ()
                        {
                            document.getElementById("divDownload").innerHTML = '<a href="#" class="btn btn-lg btn-success" style="padding:0.5em 0em 0.5em 0em">请点击右上角用浏览器打开</a>';
                        });
                     </script>
                </div>
                <div>
                    <?php
                        if($existsIosApp && $existsAndroidApp)
                        {
                            echo "IOS 安卓可用";
                        }
                        else if($existsIosApp)
                        {
                            echo "IOS可用";
                        }
                        else if($existsAndroidApp)
                        {
                            echo "安卓可用";
                        }
                        else
                        {
                            echo "无app可用";
                        }
                    ?>
                </div>
                <hr />
                <div>
                    <p>或者用手机扫描下面的二维码安装</p>
                </div>
                <div>
                    <img src="staffapp.png" />
                </div>
            </div>
        </div>
    </div>
</body>
</html>
