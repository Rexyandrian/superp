<?php
	class pay_class
	{
		public function verify($reserve_id,$refId)
		{
			$conf = new conf;
			$out = FALSE;
                        $client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;//'http://interfaces.core.sw.bps.com/';
                        $terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
        	        $verifySaleOrderId = $orderId;
                	$verifySaleReferenceId = $refId;

			$parameters = array(
			'terminalId' => $terminalId,
			'userName' => $userName,
			'userPassword' => $userPassword,
			'orderId' => $orderId,
			'saleOrderId' => $verifySaleOrderId,
			'saleReferenceId' => $verifySaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpVerifyRequest', $parameters, $namespace);
			return $result;
		}
		public function settle($reserve_id,$refId)
		{
                        $conf = new conf;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName =$conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$settleSaleOrderId = $reserve_id;
			$settleSaleReferenceId = $refId;

			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $settleSaleOrderId,
				'saleReferenceId' => $settleSaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpSettleRequest', $parameters, $namespace);
			return $result;
		}
		function revers($reserve_id,$refId)
		{
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$reversalSaleOrderId = $reserve_id;
			$reversalSaleReferenceId = $refId;
			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $reversalSaleOrderId,
				'saleReferenceId' => $reversalSaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpReversalRequest', $parameters, $namespace);

			// Check for a fault
			if ($client->fault) {
				//
			} 
			else {
				$resultStr = $result;
				$err = $client->getError();
				if ($err) {
					// Display the error
				} 
				else 
					$out = $resultStr;
			}
			return $out;
		}
		function Inquiry($reserve_id,$refId)
		{
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$inquirySaleOrderId = $reserve_id;
			$inquirySaleReferenceId = $refId;

			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $inquirySaleOrderId,
				'saleReferenceId' => $inquirySaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpInquiryRequest', $parameters, $namespace);

			// Check for a fault
			if ($client->fault) {
				//
			} 
			else {
				$resultStr = $result;
			
				$err = $client->getError();
				if ($err) {
					// Display the error
				} 
				else 
					$out = $resultStr;
			}// end Check for errors
			return $out;
		}
		public function pl_pay($pardakht_id,$amount)
		{
			$conf = new conf;
		        function send($url,$api,$amount,$redirect)
		        {
		                $ch = curl_init();
                		curl_setopt($ch,CURLOPT_URL,$url);
		                curl_setopt($ch,CURLOPT_POSTFIELDS,"api=$api&amount=$amount&redirect=$redirect");
		                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		                $res = curl_exec($ch);
		                curl_close($ch);
                		return $res;
		        }
		        $url = $conf->payline_url;;//"http://payline.ir/payment-test/gateway-result-second" 
		        $api = $conf->payline_api;//"adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck1244567";
			$redirect = $conf->payline_redirectAddr;//"http://superparvaz.gcom.ir/main/payline_purchase.php";
			$out = send($url,$api,(int)$amount,$redirect);
			return($out);
		}
		public function pl_get($trans_id,$id_get)
		{
			function getPayline($url,$api,$trans_id,$id_get)
			{
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
			    	curl_setopt($ch,CURLOPT_POSTFIELDS,"api=$api&id_get=$id_get&trans_id=$trans_id");
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				$res = curl_exec($ch);
				curl_close($ch);
				return $res;
			}
			$conf = new conf;
			$url = $conf->payline_get;
		        $api = $conf->payline_api;
			//$out = "($url,$api,$trans_id,$id_get)";
			$out = getPayline($url,$api,$trans_id,$id_get);
			return $out;
		}
		public function ps_pay($pardakht_id,$amount)
		{
			$conf = new conf;
			require_once("../class/RSAProcessor.class.php"); 
			include_once("../simplejson.php");
			$processor = new RSAProcessor("../class/certificate.xml",RSAKeyType::XMLFile);
			$pardakht = new pardakht_class((int)$pardakht_id);
			$merchantCode = $conf->ps_merchantCode;
			$terminalCode = $conf->ps_terminalCode;
			$redirectAddress = $conf->ps_redirectAddress;
			$invoiceNumber = $pardakht_id;
			$timeStamp = str_replace('-','/',$pardakht->tarikh);
			$invoiceDate = str_replace('-','/',$pardakht->tarikh);
			$action = "1003"; 	// 1003 : براي درخواست خريد 
			$data = "#". $merchantCode ."#". $terminalCode ."#". $invoiceNumber ."#". $invoiceDate ."#". $amount ."#". $redirectAddress ."#". $action ."#". $timeStamp ."#";
			$data = sha1($data,true);
			$data =  $processor->sign($data); // امضاي ديجيتال 
			$result =  base64_encode($data); // base64_encode
			$out['invoiceNumber'] = $invoiceNumber;
			$out['invoiceDate'] = $invoiceDate;
			$out['amount'] = $amount;
			$out['terminalCode'] = $terminalCode;
			$out['merchantCode'] = $merchantCode;
			$out['redirectAddress'] = $redirectAddress;
			$out['timeStamp'] = $timeStamp;
			$out['action'] = $action;
			$out['sign'] = $result;
			$outJson = toJSON($out);
			return($outJson);
		}
		public function pay($reserve_id,$amount)
		{
/*
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
		        $namespace=$conf->mellat_namespace;//'http://interfaces.core.sw.bps.com/';
			$terminalId = $conf->mellat_terminalId;
        	        $userName = $conf->mellat_userName;
	                $userPassword = $conf->mellat_userPassword;
			$orderId = $reserve_id;
			$amount =audit_class::perToEn($amount);
			$localDate = date("Ymd");
			$localTime = date("His");
			$additionalData = '';
			$callBackUrl = $conf->mellat_callBackUrl;
			$payerId = 0;
			$parameters = array(
			'terminalId' => $terminalId,
			'userName' => $userName,
			'userPassword' => $userPassword,
			'orderId' => $orderId,
			'amount' => $amount,
			'localDate' => $localDate,
			'localTime' => $localTime,
			'additionalData' => $additionalData,
			'callBackUrl' => $callBackUrl,
			'payerId' => $payerId);

		// Call the SOAP method
			$result = $client->call('bpPayRequest', $parameters, $namespace);
			if ($client->fault)
			{
			//
			} 
			else 
			{
				// Check for errors
			
				$resultStr  = $result;

				$err = $client->getError();
				if ($err)
				{
					// Display the error
				} 
				else 
				{
					$out = $result;	
				}				
			}
			return($out);
*/
		}
	}
?>
