<?php

/**
 * Amazon S3 Upload PHP class
 *
 * @version 0.1
 */
class S3_upload {

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('s3');

		$this->CI->config->load('s3', TRUE);
		$s3_config = $this->CI->config->item('s3');
		$this->bucket_name = $s3_config['bucket_name'];
		$this->folder_name = $s3_config['folder_name'];
		$this->s3_url = $s3_config['s3_url'];
	}

	function upload_file($file_path,$sub_folder = null)
	{
		// generate unique filename
		$file = pathinfo($file_path);
		$s3_file = date("Y-m-d-h-i-s").$file['filename'].'.'.$file['extension'];
		$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);

		$saved = $this->CI->s3->putObjectFile(
			$file_path,
			$this->bucket_name,
			$this->folder_name."/".$sub_folder.$s3_file,
			S3::ACL_PUBLIC_READ,
			array(),
			$mime_type
		);
		if ($saved) {
			$result = array(
				'url'=>$this->s3_url."/".$this->bucket_name."/".$this->folder_name."/".$sub_folder.$s3_file,
				'key'=>$this->folder_name."/".$sub_folder.$s3_file
			);
			return $result;
		}
	}

	function delete_file($file_path){
		//https://s3.eu-central-1.amazonaws.com/financial-projects/financial-stage/attachments/2019-04-29-05-58-59doc.pdf
		$url = str_replace($this->s3_url."/".$this->bucket_name."/","",$file_path);
		$deleted = $this->CI->s3->deleteObject($this->bucket_name,$url);
	}


	function get_file($key){
		$fileUrl = $this->CI->s3->getObject(
			$this->bucket_name,
			$key
		);

		return $fileUrl;
	}

}