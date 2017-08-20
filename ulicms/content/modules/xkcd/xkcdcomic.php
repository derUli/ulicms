<?php
/*
    xkcd-php - A simple wrapper of the XKCD API written in PHP with caching support
    Copyright (C) 2012  Zhaofeng Li (lizhaofeng1998)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class xkcdcomic{
    private $raw = array();
	private $api = null;
    public $url = '';
    
	function __construct($raw, &$api){
		$this->raw = $raw;
		$this->api = &$api;
        $this->url = 'http://xkcd.com/'.$this->num;
	}
	
	function __get($var){
		//The following lines are forked from PHP Manual, © 1997-2012 the PHP Documentation Group, licensed under Creative Commons Attribution 3.0
		if (array_key_exists($var, $this->raw)) {
		    return $this->raw[$var];
		}
		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
		return null;
		//End fork
	}
	
	function next(){
		return $this->api->get($this->num + 1);
	}
	
	function prev(){
		return $this->api->get($this->num - 1);
	}
}