<?php

class EmailBodyPart
{
	public $body;
	public $headers = array();
	
	public function __construct($body)
	{
		$this->parse_body_part($body);
	}
	
	public function parse_body_part($body)
	{
		list($headers, $body) = explode("\n\n", $body, 2);
		$this->body = $body;
		$this->headers = new EmailHeaders(trim($headers));
	}
}
