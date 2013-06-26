<?php

/**
 * Signed Message encoder/decoder
 * @author Stefano Azzolini <lastguest@gmail.com>
 */

class Mess {
	protected static $options = array(
		'secret' => 'remember_to_change_me',
		'signing_method' => 'sha256',
		'verify' => true, // flag to enable/disable signature verification
	);

	public static function init(array $options){
		foreach($options as $key => $val){
			static::$options[$key] = $val;
		}
	}

	public static function parse($payload){
		$packet = static::decode($payload);
		$data = $packet['d'];
		$signature = $packet['s'];
		if( !static::$options['verify'] || $signature === static::sign($data) ){
			return $data;
		} else {
			throw new Exception( 'Invalid payload signature or corrupted data.' );
		}
	}

	public static function pack($data){
		return static::encode(array(
			'd' => $data,
			's' => static::$options['verify'] ? static::sign($data) : false,
		));
	}

	protected static function sign($data){
		return hash_hmac(static::$options['signing_method'],serialize($data),static::$options['secret']);
	}

	protected static function encode($data){
		return @strtr(base64_encode(addslashes(gzcompress(serialize($data),9))), '+/=', '-_,');
	}

	protected static function decode($data){
		return @unserialize(gzuncompress(stripslashes(base64_decode(strtr($data, '-_,', '+/=')))));
	}

}
