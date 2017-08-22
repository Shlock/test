<?php
namespace YiZan\Utils\Image;

use YiZan\Utils\Time;
use Config;

class OssImage{
    public function __construct() {
        if (!defined('OSS_HOST')) {
            define('OSS_HOST', Config::get('app.image_config.oss.host'));
            define('OSS_ACCESS_ID', Config::get('app.image_config.oss.access_id'));
            define('OSS_ACCESS_KEY', Config::get('app.image_config.oss.access_key'));
            define('OSS_BUCKET', Config::get('app.image_config.oss.bucket'));
            define('OSS_URL', Config::get('app.image_config.oss.url'));
        }
    }

    public function getFormArgs($path) {
        $data = [
            'save_path' => [
                'name'  => 'key',
                'path'  => $path,
            ],
            'file_name' => 'file',
            'action'    => 'http://'.OSS_BUCKET.'.'.OSS_HOST,
            'wap_action'    => u("Resource/upload"),
            'image_url' => OSS_URL.$path
        ];

        $args = [
            'OSSAccessKeyId'    => OSS_ACCESS_ID,
            'policy'            => '',
            'signature'         => '',
            'success_action_redirect' => u('Resource/callback'),
        ];
        $expiration = explode('+', Time::toDate(UTC_TIME + 86400, 'c'));
        $expiration = $expiration[0].'.000Z';

        $args['policy'] = [
            'expiration' => $expiration,
            'conditions' => [
                ['bucket' => OSS_BUCKET],
                ['starts-with', '$key', 'images/'],
                ['content-length-range', 1, 5242881]
            ]
        ];
        $args['policy']     = base64_encode(json_encode($args['policy']));
        $args['signature']  = base64_encode(hash_hmac('sha1', $args['policy'], OSS_ACCESS_KEY, true));
        $data['args']       = $args;
        return $data;
    }

    public function remove($paths) {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $key => $path) {
            $url = parse_url($path);
            $paths[$key] = substr($url['path'],0,1) == '/' ? substr($url['path'],1) : $url['path'];
        }
        $options = array(
            'quiet' => false
        );
        require_once base_path().'/vendor/oss/sdk.class.php';
        $oss = new \ALIOSS(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_HOST);
        $response = $oss->delete_objects(OSS_BUCKET,$paths,$options);
        if($response && $response->status == 200){
            return true;
        }else{
            return false;
        }
    }

    public function upload($data, $path) {
        require_once base_path().'/vendor/oss/sdk.class.php';
        $oss = new \ALIOSS(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_HOST);
        $options = array(
            'content' => $data,
            'length' => strlen($data)
        );
        $response = $oss->upload_file_by_content(OSS_BUCKET, $path, $options);
        if($response && $response->isOk()){
            return OSS_URL.$path;
        }else{
            return false;
        }
    }

    public function move($formPath, $dir) {
        $formPath = parse_url($formPath);
        $formPath = $formPath['path'];
        $formPath = substr( $formPath, 0, 1 ) == '/' ? substr($formPath,1,strlen($formPath)) : $formPath;
        $ext = explode('/', $formPath);
        $ext = explode('.', end($ext));
        $toPath = $dir.'/'.md5(uniqid()).'.'.end($ext);
        require_once base_path().'/vendor/oss/sdk.class.php';
        $oss = new \ALIOSS(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_HOST);
        $response = $oss->copy_object(OSS_BUCKET, $formPath, OSS_BUCKET, $toPath);
        if($response && $response->status == 200){
            return OSS_URL.$toPath;
        }else{
            return false;
        }
    }
}