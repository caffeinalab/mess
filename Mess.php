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

/*
Example:

// Init and set your secret key.
Mess::init(array(
	'secret' => 'this_is_my_secret_key',
));

// Example data
$data = array(
	'identity' => array(
		'name' => 'Jon',
		'surname' => 'Snow',
		'house' => 'Stark',
	),
	'count' => 12345,
	'bar' => 'baz',
);


echo "Starting Data : ",print_r($data,true),"\n";

// Encode payload
$payload = Mess::pack($data);
echo "Encoded Payload : ",$payload,"\n\n";

// Decode received payload
$data2 = Mess::parse($payload);
echo "Decoded Payload : ",print_r($data2,true),"\n";



// Simulate an error in payload (maybe a tentative of hacking)
echo "-- Simulate error -- \n\n";
$payload2 = 'F00'.$payload;
echo "Encoded Payload2 : ",$payload2,"\n\n";

try {
	$data3 = Mess::parse($payload2);
	echo "Decoded Payload : ",print_r($data3,true),"\n";
} catch (Exception $e){
	echo 'Error catched : ',$e->getMessage(), "\n\n";
}

*/