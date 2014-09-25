<?php

// Nuestro token
$deviceToken = '4a9988acca4d63f7434279de17290eab37fd4006d57d66879d4c743937db958c';

// El password del fichero .pem
$passphrase = 'testpush';

// El mensaje push
$message = '¡Mi primer mensaje Push!';

$ctx = stream_context_create();
//Especificamos la ruta al certificado .pem que hemos creado
stream_context_set_option($ctx, 'ssl', 'local_cert', '../certs/TestPushCK.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Abrimos conexión con APNS
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp) {
	exit("Error de conexión: $err $errstr" . PHP_EOL);
}

echo 'Conectado al APNS' . PHP_EOL;

// Creamos el payload
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'bingbong.aiff',
	'badge' => 35
	);

// Lo codificamos a json
$payload = json_encode($body);

// Construimos el mensaje binario
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Lo enviamos
$result = fwrite($fp, $msg, strlen($msg));

if (!$result) {
	echo 'Mensaje no enviado' . PHP_EOL;
} else { 
	echo 'Mensaje enviado correctamente' . PHP_EOL;
}

// cerramos la conexión
fclose($fp);
