<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chatmitra_library {

    protected $CI;
    private $api_url;
    private $token;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('secrets');
        $this->api_url = $this->CI->config->item('cm_api_url');
        $this->token = $this->CI->config->item('cm_bearer_token');
    }

    public function send_bill_notification($mobile, $bill_no, $amount, $date, $s3_url) {
        // Ensure mobile has 91 prefix
        $mobile = (strlen($mobile) == 10) ? '91' . $mobile : $mobile;
		$template_slug = "bookstore_purchase_bill_20260213214207";
		//$bill_no = str_replace('_', '-', $bill_no);
		$clean_bill = str_replace('.pdf', '', $bill_no);
		$clean_bill = str_replace('_', '-', $clean_bill);
        $date = date("d-m-Y", strtotime($date));
        $components_json = '[
					{
					"type": "header",
					"parameters": [
					{
					"type": "document",
					"document": {
					"link": "' . $s3_url . '",
					"filename": "' . $bill_no . '"
					}
					}
					]
					},
					{
					"type": "body",
					"parameters": [
					{"type": "text", "text": "' . $clean_bill . '"},
					{"type": "text", "text": "' . $amount . '"},
					{"type": "text", "text": "' . $date . '"}
					]
					}
					]';
        
        
        
					$data = [
					"recipient_mobile_number" => $mobile,
					"messages" => [[
					"kind" => "template",
					"template" => [
					"name" => $template_slug,
					"language" => "en_US",
					"components" => json_decode($components_json, true)
					]
					]],
					"customer_name" => ""

];
      return $this->_execute_curl($data);
    }

    private function _execute_curl($data) {
        $json_payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json"
        ]);
			// 🔎 ADD THESE LINES
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$error = curl_error($ch);

			

			// 1️⃣ Network / cURL error
			
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);

    curl_close($ch);

    


			// 2️⃣ Decode response safely
			$response_data = json_decode($response, true);

			// 3️⃣ Return structured result
			return [
				'status'     => in_array($http_code, [200, 201, 202]) ? 'accepted' : 'failed',
				'http_code'  => $http_code,
				'data'       => $response_data,
				'raw'        => $response,
				'debug' => $verboseLog,
				'message' => $error 
			];

    }
}
