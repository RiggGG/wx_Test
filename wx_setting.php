<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                default:
                    $result = "unknow msg type:".$RX_TYPE;
                    break;
            }
            echo $result;
        }else {
            echo "";
            exit;
        }
    }


    private function receiveEvent($object)
    {
        $content = "";
        switch($object->Event)
        {
            case "subscribe":
                $content = "welcome to father's room";
                break;
            case "unsubscribe":
                $content = "see you!my son.hhhh...";
                break;
            case "LOCATION":
                $content = "upload location:latitude".$object->Latitude."; longitude".$object->Longitude;
                break;
            default:
                $content = "receive a new Event: ".$object->Event;
                break;       
        }
        $result = $this->transmitText($object,$content);
        return $result;
    }


    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        $content = "Now is:".date("Y-m-d H:i:s",time());
        $result = $this->transmitText($object,$content);
        return $result;
    }


    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object,$content);
        return $result;
    }


    private function receiveLocation($object)
    {
        $content = "your send location is(latitude,longitude):(".$object->Location_X.",".$object->Location_Y.") and scale level is:".$object->Scale.", and location is:".$object->Label;
        $result = $this->transmitText($object,$content);
        return $result;
    }


    private function receiveVoice($object)
    {
	 if(empty($object->Recognition)){
            $content = "you can't use this service!";
            $result = $this->transmitText($object.$content);
        }else{
            $content = "you said:".$object->Recognition;
            $result = $this->transmitText($object,$content);
        }
        return $result;
    }


    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId,"ThumbMediaId"=>$object->ThumbMediaId,"Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object,$content);
        return $result;
    }


    private function transmitText($object,$content)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
        $result = sprintf($textTpl,$object->FromUserName,$object->ToUserName,time(),$content);
        return $result;
    }
    

    private function transmitImage($object,$imageArray)
    {
        $itmpTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
        </Image>";
        $item_str = sprintf($itmpTpl,$imageArray['MediaId']);
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        $item_str
                        </xml>";
        $result = sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
        return $result;
    }


    private function transmitVoice($object,$voiceArray)
    {
        $itmpTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
        </Voice>";
        $item_str = sprintf($itmpTpl,$voiceArray['MediaId']);
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
			$item_str
                        </xml>";
        $result = sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
        return $result;
    }


    private function transmitVideo($object,$videoArray)
    {
        $itmpTpl = "<Video>
        <MediaId><![CDATA[%s]]></MediaId>
        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        </Video>";
        $item_str = sprintf($itmpTpl,$videoArray['MediaId'],$videoArray['ThumbMediaId'],$videoArray['Title'],$videoArray['Description']);
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        $item_str
                        </xml>";
        $result = sprintf($textTpl,$object->FromUserName,$object->ToUserName,time());
        return $result;
    }
}
?>
