<?php
/**
 * WebPHPack v1.0
 * webpack PHP alternative
 * 
 * @package phreak 
 * @author Simeon Lyubenov <lyubenov@gmail.com> www.webdevlabs.com
 *
 * Usage:
 * $webphpack = new WebPHPack($htmlsource);
 * $newsource = $webpack->combineJS()->output();
 */

class WebPHPack 
{
    private $inputHTML;
    private $outputHTML;
    private $baseURL;
    private $jsPath;
    private $cssPath;
    private $outputPath;
    private $outputURL;

    public function __construct($inputHTML)
    {
        $this->inputHTML = $inputHTML;
        $this->outputHTML = $inputHTML;
        $this->baseURL = BASE_URL;
        $this->jsPath = ROOT_DIR.'/public/assets/js';
        $this->cssPath = ROOT_DIR.'/public/assets/css';
        $this->outputPath = ROOT_DIR.'/public/assets/cache';
        $this->outputURL = BASE_URL.'/assets/cache';
    }

    public function output()
    {
        return $this->outputHTML;
    }

    public function combineJS ()
    {
		$pma = preg_match_all('/<script[^>]*src="([^"]*)\.js[^>]*"[^>]*><\/script>/', $this->inputHTML, $matches);
		if (($pma !== false && $pma > 0) && (!file_exists($this->outputPath.'/front.js'))) {
			$jscombined = "/* WebPHPack Auto-Generated JS File */\n";
			foreach ($matches[1] as $match) {
				if (strpos($match, $this->baseURL) !== false) {
					// read all javascript files and combine them
					$jscombined .= file_get_contents($this->jsPath.'/'.basename($match).'.js');
				}
			}
			file_put_contents($this->outputPath.'/front.js', $jscombined);
		}
		$newsrc = $this->inputHTML;
		foreach ($matches[0] as $match) {
			if (strpos($match, $this->baseURL) !== false) {
				// read all javascript and remove from html source
				$newsrc = str_replace($match, '', $newsrc);
			}
		}
		clearstatcache();
		$filectime = filectime($this->outputPath.'/front.js');
		$newsrc = str_replace('</head>', '<script src="'.$this->outputURL.'/front.js?'.$filectime.'" async></script></head>', $newsrc);
		$this->outputHTML = $newsrc;
        return $this;
    }

}