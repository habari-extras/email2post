#!/usr/bin/php
<?php

// read from php://stdin
$email = file_get_contents('php://stdin');

class Email
{
	const MULTIPART = 0;
	const SINGLE = 1;
	
	private $email_raw;
	public $body_raw;
	
	public $headers;
	public $content_type;
	public $body;
	public $body_parts;
	public $type;
	public $boundary;
	
	public function __construct($email)
	{
		$this->email_raw = $email;
		$this->parse_email($email);
	}
	
	public function parse_email($email)
	{
		list($headers, $body) = explode("\n\n", $email, 2);
		$this->headers = new EmailHeaders(trim($headers));
		$this->parse_body($body);
	}
	
	public function parse_body($body)
	{
		$this->body_raw = $body;
		if (preg_match('#^multipart/.*boundary=(\'|")(.*)(\1)#i', $this->headers->get('content-type'), $m)) {
			$this->type = self::MULTIPART;
			$this->boundary = $m[2];
			
			$body = substr($body, strlen($this->boundary)+2);
			$bodies = explode("--$this->boundary", $body, -1);
			
			foreach ($bodies as $bod) {
				$this->body_parts[] = new EmailBodyPart($bod);
			}
		}
		else {
			$this->type = self::SINGLE;
			$this->body = $body;
		}
	}
}

include "emailheaders.php";
include "emailbodypart.php";

$e = new Email($email);

?>
