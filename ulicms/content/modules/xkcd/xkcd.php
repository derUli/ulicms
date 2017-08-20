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
class xkcd{
    private $cache = array(); //comic cache
	private $cacheindex = array();
	private $latestnum = 0; //the ID of the latest comic
	public $cachelimit = 50; //limitation of the number of the entries in cache
	
	function __construct(){
		$this->refresh();
	}
	
	public function refresh(){
		$raw = json_decode(file_get_contents('http://xkcd.com/info.0.json'), true); //get the latest comic info
		$comic = new xkcdcomic($raw, $this); //initalize a new comic object
		$this->addcache($comic);
		$this->latestnum = $comic->num;
	}
	
	public function get($num){
		$num = (int)$num;
		if($num > $this->latestnum || $num < 1){
            throw new Exception('Wrooooong comic ID specified!'); return null;
		}
		else{
			if(array_key_exists($num, $this->cache)){
				return $this->cache[$num];
			}else{
				$raw = json_decode(file_get_contents('http://xkcd.com/'.$num.'/info.0.json'), true);
				$comic = new xkcdcomic($raw, $this);
				$this->addcache($comic);
				return $comic;
			}
		}
	}
	
	public function random(){
		$rand = rand(1, $this->latestnum);
		return $this->get($rand);
	}
    
    public function latest(){
        return $this->get($this->latestnum);
    }
	
	private function addcache(xkcdcomic $comic){
		if(array_key_exists($comic->num, $this->cache)){ //already exists in cache
			$this->cache[$comic->num] = $comic; //update cache
		}else{
			while(count($this->cache) >= $this->cachelimit){ //cache limit exceeded
				foreach($this->cacheindex as $key => $num) break; //HACK: A quick'n'dirty way to get the first item
				unset($this->cache[$num]);
				unset($this->cacheindex[$key]);
			}
			$this->cache[$comic->num] = $comic;
			$this->cacheindex[] = $comic->num;
		}
	}
	
	public function clearcache(){
		$this->cache = array();
		$this->cacheindex = array();
	}
}
