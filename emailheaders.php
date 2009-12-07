<?php

class EmailHeaders
{
	public $headers_raw = array();
	public $headers = array();
	
	public function __construct($headers)
	{
		$this->headers_raw = $headers;
		$this->parse_headers($headers);
	}
	
	public function get($header)
	{
		if (array_key_exists($header, $this->headers)) {
			return $this->headers[$header];
		}
		else {
			return false;
		}
	}
	
	public function all()
	{
		$head = '';
		foreach ($this->headers as $h => $v) {
			if (is_array($v)) {
				$head .= $this->implode($h, $v);
			}
			elseif ($h) {
				$head .= "$h: $v\n";
			}
			else {
				$head .= "$v\n";
			}
		}
		return $head;
	}
	
	private function implode($header, $value)
	{
		$head = '';
		foreach ($value as $v) {
			if (is_array($v)) {
				$head .= $this->implode('', $v);
			}
			else {
				$head .= "$header: $v\n";
			}
		}
		return $head;
	}
	
	public function set($header, $value)
	{
		$this->headers[$header] = $value;
		return $this->headers[$header];
	}
	
	public function remove($header)
	{
		unset($this->headers[$header]);
	}
	
	private function parse_headers($headers)
	{
		$last_head = '';
		$lines = explode("\n", $headers);

		foreach ($lines as $line) {
			if (preg_match("/^([a-zA-Z0-9\-_]+): (.*)/", $line, $m)) {
				$this->add_header(strtolower($m[1]), $m[2]);
				$last_head = strtolower($m[1]);
			}
			else {
				// it's a multi-line header
				if (is_array($this->headers[$last_head])) {
					$this->headers[$last_head][count($this->headers[$last_head])-1] .= " $line";
				}
				else {
					$this->headers[$last_head] .= " $line";
				}
			}
		}
	}
	
	public function add_header($header, $value)
	{
		if (array_key_exists($header, $this->headers)) {
			if (!is_array($this->headers[$header])) {
				$this->headers[$header] = array($this->headers[$header]);
			}
			$this->headers[$header][] = $value;
		}
		else {
			$this->headers[$header] = $value;
		}
	}
}
