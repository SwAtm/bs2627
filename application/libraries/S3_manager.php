<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Adjust this path to where you saved the AWS SDK
require APPPATH.'aws/aws-autoloader.php'; 

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3_manager {
    protected $CI;
    protected $s3Client;
    protected $bucketName;
    

    public function __construct() {
        $this->CI =& get_instance();
		$this->CI->config->load('secrets');
		$this->bucketName = $this->CI->config->item('s3_bucket_name');
        // Initialize the S3 Client
        $this->s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $this->CI->config->item('s3_region'), // e.g., 'us-east-1'
            'credentials' => [
                'key'    => $this->CI->config->item('s3_access_key'),
                'secret' => $this->CI->config->item('s3_secret_key'),
            ],
        ]);
    }

    public function upload_private_file($filePath, $fileName) {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => 'uploads/' . $fileName, // Folder path inside S3
                'SourceFile' => $filePath,
                'ACL'    => 'private',// Set to 'private' since using signed URLs
				'ContentType' => 'application/pdf'
            ]);

            
            return 'uploads/' . $fileName;
            
        } catch (AwsException $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }
    
    /*
		* Generate a temporary link
		 * @param string $s3Key The 'Key' returned by the upload function
		 * @param string $expires Time string (e.g., '+20 minutes')
		 */
		public function get_presigned_url($s3Key, $expires = '+1 day') {
			$cmd = $this->s3Client->getCommand('GetObject', [
				'Bucket' => $this->bucketName,
				'Key'    => $s3Key
			]);

			$request = $this->s3Client->createPresignedRequest($cmd, $expires);

			// This returns the full URL with a long signature attached
			return (string)$request->getUri();
		}
}
