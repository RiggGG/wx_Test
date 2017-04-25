<?php
	
$data = '{
    "button": [
        {
            "name": "configure", 
            "sub_button": [
                {
                    "type": "click", 
                    "name": "photo", 
                    "key": "myselfmenu_0_0"
                }
            ]
        }
    ]
}';
$a = new wx_test();
$a->create_menu($data);









class wx_test
{
	var $appid = "wx6f3d79f5ab019568";
	var $appsecret = "2adc9468529a61f6349f2a5f43be9a82";

	public function __construct($appid = NULL, $appsecret = NULL)
	{
		if($appid){
			$this->appid = $appid;
		}
		if($appsecret){
			$this->appsecret = $appsecret;
		}

		$this->lasttime = 1395049256;
		$this->access_token = "6fReJmq0P3m3Zxt-RWyuD0ed6WerC7wgSBSO9JfaRtM386GhgIiRn-0vUIIG_x43kfhLWqgUGGF
		mOBn14w0avWB9mFAVXpMm8Iu-0MjZZlAQZLdAJAYHT";

		if(time() >($this->lasttime + 7200))
		{
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
			$res = $this->https_request($url);
			$result = json_decode($res,true);

			$this->access_token = $result["access_token"];
			$this->lasttime = time();
		}
	}



	public function create_menu($data)
	{
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$this->access_token";
		$res = $this->https_request($url,$data);
		return json_decode($res,true);
	}




	protected function https_request($url,$data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELD, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}	
}
	


?>
