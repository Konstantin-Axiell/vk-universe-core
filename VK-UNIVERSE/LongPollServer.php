<?php
if(isset($argv[1])){
	require('API.php');
	$access_token=$argv[1];
	$api=new API($access_token);
	$data=$api->messages_getLongPollServer(['use_ssl'=>1]);

	$server = $data['response']['server'];
	$key = $data['response']['key'];
	$ts = $data['response']['ts'];

	$event_file='VK-UNIVERSE/events';
	file_put_contents($event_file, '');

	while(TRUE){
		usleep(10);
		$request = file_get_contents('https://'.$server.'?act=a_check&key='.$key.'&ts='.$ts."&wait=25&mode=234&version=2");
		file_put_contents('logs/lp', $server.'::'.$key.'::'.$ts.'::'.$request."\n", FILE_APPEND);
		$request = json_decode($request,1);



		if(isset($request['failed'])){
			switch($request['failed']){
				case 1:
				$ts = $request['ts'];
				break;
				case 2:
				$request = $api->messages_getLongPollServer(['use_ssl'=>1]);
				$key = $request['response']['key'];
				break;
				case 3:
				$request = $api->messages_getLongPollServer(['use_ssl'=>1]);
				$key = $request['response']['key'];
				$ts = $request['response']['ts'];
				break;
			}
		}else{
				//if(filesize($event_file)>10000){file_put_contents($event_file, '');}
				if(isset($request['updates'])){
					foreach ($request['updates'] as $upd) {
						file_put_contents($event_file, json_encode($upd, JSON_UNESCAPED_UNICODE)."\n", LOCK_EX | FILE_APPEND);
					}
					$ts=$request['ts'];
				}
		}
	}
}
?>