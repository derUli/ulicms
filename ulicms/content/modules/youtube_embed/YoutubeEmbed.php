<?php

class YoutubeEmbed extends MainClass
{

    public function frontendFooter()
    {
        echo '<style type="text/css">.yt-iframe-container { position:relative; margin-bottom: 30px; padding-bottom:56.25%; padding-top:25px; height:0; max-width:100%; } .yt-iframe-container iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:none; }</style>';
    }

    public function adminFooter()
    {
        $this->frontendFooter();
    }

    public function contentFilter($html)
    {
        preg_match_all("/\[youtube=(.+)]/i", $html, $matches);
        if (count($matches[0]) > 0) {
            for ($i = 0; $i < count($matches[0]); $i ++) {
                $replaceCode = $matches[0][$i];
                $url = $matches[1][$i];
                $embedCode = $this->getYoutubeEmbedHtml($url);
                $html = str_replace($replaceCode, $embedCode, $html);
            }
        }
        return $html;
    }
	
	public function thumbnail(){
		$url = Request::getVar("url");
		$number = Request::getVar("number") ? Request::getVar("number") : 0;
		
		$query = parse_url($url, PHP_URL_QUERY);
        $args = array();
        parse_str($query, $args);
        $videoId = isset($args["v"]) ? $args["v"] : null;
		$thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/{$number}.jpg";

		$image = file_get_contents_wrapper($thumbnailUrl, false);
		
		if(!$image){
			TextResult("NotFound", HttpStatusCode::NOT_FOUND);
		}
		Result($image, HttpStatusCode::OK, "image/jpeg");
	}

    public function getYoutubeEmbedHtml($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $args = array();
        parse_str($query, $args);
        $videoId = isset($args["v"]) ? $args["v"] : null;
        if (! $videoId) {
            return null;
        }
        return "<div class=\"yt-iframe-container\"><iframe src=\"https://www.youtube-nocookie.com/embed/{$videoId}\" allowfullscreen=\"\"></iframe><br /></div>";
    }
}
