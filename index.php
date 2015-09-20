<?php
  
  define("TOKEN",$app_token);
  define("APPID",$app_id);
  define("APPSECRET",$app_securet);// the 3 variables is created by authorizor
  $acc_obj = new wechat_mp();
  //$acc_obj->is_valid();
  //$acc_obj->userResponse();
  $access_token = $acc_obj->get_access_token(APPID, APPSECRET);
  $acc_obj->create_menu($access_token);
  
  class wechat_mp {
				private function CheckSignature() { // ���ڷ�������֤signature��ȷ��
					$signature = $_GET['signature'];
					$timestamp = $_GET['timestamp'];
					$nonce = $_GET['nonce'];
					$token = TOKEN;
					$tmpArr = array($token,$timestamp,$nonce);
					sort($tmpArr);
					$tmpArr = implode($tmpArr);
					$tmpArr = sha1($tmpArr);
				  return ( $tmpArr == $signature );
				}
				
				public function is_valid() { // ΢�ŷ�������֤���ڷ�����URL��Ч��
				  $echoStr = $_GET['echostr'];
				  if( $this->CheckSignature() ) 
				    echo $echoStr;
				  else 
				  	echo "Signature check failed.";				  					
				}
				
				public function userResponse() {
					$postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
					if( empty($postStr) ) {
						echo "���������ݰ�...";
					  return;	
					}					
					$postObj = simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
					$usrName = $postObj->FromUserName;
					$devName = $postObj->ToUserName;
					$keyWord = trim($postObj->Content); 
					$time = time();
					$textTpl = "<xml>
					            <ToUserName><![CDATA[%s]]></ToUserName>
					            <FromUserName><![CDATA[%s]]></FromUserName>
					            <CreateTime><![CDATA[%s]]></CreateTime>
					            <MsgType><![CDATA[%s]]></MsgType>
					            <Content><![CDATA[%s]]></Content>
					            <FuncFlag>0</FuncFlag>
					            </xml>";
          if( !empty($keyWord) ) {
            $msgType = "text";
            $contentStr = "thank you for attention!";
            $resultStr = sprintf($textTpl,$usrName,$devName,$time,$msgType,$contentStr);	
            echo $resultStr;
          } else {
            echo "������ؼ���...";	
          }
				}
				
				public function get_access_token($id,$secret) {
					$mp_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$id&secret=$secret";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $mp_url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $result = curl_exec($ch);
          if ( curl_errno($ch) ) {
            echo 'Errno'.curl_error($ch);	
          }					
          curl_close($ch);
          $json_info = json_decode($result,true);
          return $json_info['access_token'];
				}
				
				public function create_menu($token) {
					$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
					$ch = curl_init();
					//echo $token;
					$data = "{
						\"button\":[
						  {
						  	\"type\":\"view\",
						  	\"name\":\"�ٶ���ҳ\",
						  	\"url\":\"http://www.baidu.com\"
						  },
						  {
						    \"type\":\"view\",
						    \"name\":\"������ҳ\",
						    \"url\":\"http://www.sina.com.cn\"
						  },
						  {
						    \"type\":\"view\",
						    \"name\":\"������ҳ\",
						    \"url\":\"http://www.homelink.com.cn\"
						  }]	
					}";
					curl_setopt($ch, CURLOPT_URL, $url);
					//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
					$result = curl_exec($ch);
          if ( curl_errno($ch) ) {
            echo 'Errno'.curl_error($ch);	
          }					
          curl_close($ch);
          //return $result;					
				}
  }
	
?>
