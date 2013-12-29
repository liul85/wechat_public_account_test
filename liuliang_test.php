<?php

  //load message template
//include_once("wx_template.php");
  $wechatobj = new wechatMsgAction();
  $wechatobj->responseMsg();

  class wechatMsgAction
  {
    public function responseMsg()
    {
      include_once("wx_template.php");
      //get data from weixin server
      $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      //return reply data
      if (!empty($postStr))
      {
        //analyze data
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
          
        //get fromUserID
        $fromUsername = $postObj->FromUserName;
        
        //get toUsername
        $toUsername = $postObj->ToUserName;
        //get msg type
        $form_MsgType = $postObj->MsgType;
        
        if($form_MsgType == "text")
        {
          $form_content = trim($postObj->Content);
    
          if($form_content == "tq")
          {
            $msgType = "text"; 
            $contentStr = $this->getWeather();
              //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
            $resultStr = sprintf("<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>",$fromUsername, $toUsername, time(), $msgType, $contentStr);
            echo $resultStr;
            exit;
          }
          else
          {
            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, "回复tq获取西安天气预报");
            echo $resultStr;
            exit;
          }
        }
      
        if($form_MsgType == "event")
        {
          //get event type
          $form_EventType = $postObj->Event;
          //if event is subscriber
          if($form_EventType == "subscribe")
          {
            $msgType = "news";
            $articleCount = 1;
            $Title = "欢迎关注刘亮微信公众测试账号";
            $description = "关注西安雾霾";
            $picUrl = "http://webchatliuliang-log.stor.sinaapp.com/liuliang_head.jpg";
            $url = "http://www.cnpm25.cn/City/xian.html";
            $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, time(), $msgType, $articleCount, $Title, $description, $picUrl, $url);
            echo $resultStr;
            exit;
          }
        }
        else
        {
          echo "";
          exit;
        }
      }
      else
      {
        echo "";
        exit;
      }
    }

    private function getWeather()
    {
      $url = "http://m.weather.com.cn/data/101110101.html";
      $output = file_get_contents($url);
      $weather = json_decode($output, true);
      $info = $weather['weatherinfo'];
      $weather_result = "【".$info['city']."】".$info['date_y']." 天气实况: ".$info['weather1']."\n温度: ".$info['temp1']."\n".$info[index_d]."";
      return $weather_result;
    }
  }
?>
