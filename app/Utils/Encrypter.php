<?php namespace YiZan\Utils;

use Illuminate\Encryption\Encrypter as EncrypterBase;
use Config;

class Encrypter extends EncrypterBase{
	public function encrypt($value) {
		$this->checkCipher();
		$iv = $this->createIv();

		$value = $this->addPadding(json_encode($value));
		$value = mcrypt_encrypt($this->cipher, $this->key, $value, $this->mode, $iv);
		return base64_encode($value);
	}

	private function checkCipher () {
		if (Config::has('app.cipher')) {
			$this->setCipher(Config::get('app.cipher'));
		}
	}

	protected function createIv() {
		$this->checkCipher();
		$iv_size 	= $this->getIvSize();
		$iv_index   = (int)substr(Config::get('app.iv_rule'), 0, 1);
		$iv_random  = substr($this->key, $iv_index, $iv_size);
		return substr(md5($iv_random.$this->key), $iv_index , $iv_size);
	}

	public function decrypt($value){
		$value = base64_decode($value);
		$iv = $this->createIv();
		return json_decode($this->stripPadding($this->mcryptDecrypt($value, $iv)), true);
	}
}
