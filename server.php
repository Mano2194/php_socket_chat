<?php
$host="192.168.1.111";
$port = 95;
$null = NULL;

$socket = socket_create(AF_INET, SOCK_STREAM,SOL_TCP) or die("could not create\n");
socket_bind($socket,$host,$port) or die("could not bind \n");
socket_listen($socket)or die("could not listen \n");
p("Listening...");

$connections = array($socket);

while(true){
	$reads=$connections;
	socket_select($reads,$null,$null,0);
	if (in_array($socket, $reads)) {
		$new_connection = socket_accept($socket);
		$connections[]=$new_connection;
		socket_write($new_connection,"Welcome",strlen("Welcome"));
		$index = array_search($socket, $reads);
		unset($reads[$index]);
	}

	foreach ($reads as $key => $value) {
		$data = socket_read($value,1024);
		if(!empty($data)){
			foreach ($connections as $iKey => $iValue) {
				if($iKey===0)continue;
				socket_write($iValue,$data,strlen($data));
			}
		}else if($data===''){
			unset($connections[$key]);
			socket_close($value);
		}
	}
}
socket_close($socket);

function p($message){
	echo "\n----------\n".$message."\n----------\n";
}