<?php
namespace YiZan\Utils\Image;

use YiZan\Utils\Time;
use Config, Request;

class ServerImage{
    public function __construct() {
        if (!defined('SERVER_IMAGE_UPLOAD_URL')) {
            define('SERVER_IMAGE_UPLOAD_URL', Config::get('app.image_config.server.upload_url'));
            define('SERVER_IMAGE_TOKEN', Config::get('app.image_config.server.token'));
            define('SERVER_IMAGE_URL', Config::get('app.image_config.server.url'));
            define('SERVER_IMAGE_SAVE_PATH', Config::get('app.image_config.server.save_path'));
        }
    }

    public function getFormArgs($path) {
        $data = [
            'save_path' => [
                'name'  => 'key',
                'path'  => SERVER_IMAGE_SAVE_PATH.$path,
            ],
            'file_name' => 'file',
            'action'    => SERVER_IMAGE_UPLOAD_URL,
            'wap_action'    => SERVER_IMAGE_UPLOAD_URL,
            'image_url' => SERVER_IMAGE_URL.$path
        ];

        $token = md5(http_build_query($data).'&'.SERVER_IMAGE_TOKEN.'&'.Request::ip());

        $data['args'] = [
            'token' => $token,
            'success_action_redirect' => u('Resource/callback'),
        ];
        return $data;
    }

    public function remove($paths) {
        return true;
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $key => $path) {
            $url = parse_url($path);
            $paths[$key] = $url['path'];
        }
    }

    public function move($formPath, $dir) {
        return $formPath;
    }
}