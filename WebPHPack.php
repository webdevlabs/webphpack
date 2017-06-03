<?php
/**
 * WebPHPack v1.1
 * webpack PHP alternative
 * 
 * @package phreak 
 * @author Simeon Lyubenov <lyubenov@gmail.com> www.webdevlabs.com
 *
 */
define('BASE_URL', 'http://localhost');
define('ROOT_DIR', dirname(__DIR__));

class WebPHPack
{
    private $outputHTML;
    private $baseURL;
    private $outputURL;
    private $outputPath;
    private $jsPath, $cssPath;
    private $excludeCSS, $excludeJS;    
    private $outputJSfilename, $outputCSSfilename;    
    private $caching;

    public function __construct($inputHTML)
    {
        $this->outputHTML = $inputHTML;
        $this->baseURL = BASE_URL;
        $this->jsPath = ROOT_DIR.'/public/assets/js';
        $this->cssPath = ROOT_DIR.'/public/assets/css';
        $this->outputPath = ROOT_DIR.'/public/assets/cache';
        $this->outputURL = BASE_URL.'/assets/cache';
        $this->outputJSfilename = 'front.js';
        $this->outputCSSfilename = 'front.css';
        $this->excludeJS = [];
        $this->excludeCSS = ['theme.min'];
        $this->caching = false;
    }

    public function output()
    {
        return $this->outputHTML;
    }

    public function combineJS ()
    {
		$pma = preg_match_all('/<script[^>]*src="([^"]*)\.js[^>]*"[^>]*><\/script>/', $this->outputHTML, $matches);
		if (($pma !== false && $pma > 0) && (!file_exists($this->outputPath.'/'.$this->outputJSfilename) || $this->caching===false)) {
			$jscombined = "/* WebPHPack Auto-Generated JS File */\n";
			foreach ($matches[1] as $match) {
				if (strpos($match, $this->baseURL) !== false) {
                    if (in_array(basename($match), $this->excludeJS)) {continue;}					
					// read all javascript files and combine them
					$jscombined .= file_get_contents($this->jsPath.'/'.basename($match).'.js');
				}
			}
			file_put_contents($this->outputPath.'/'.$this->outputJSfilename, $jscombined);
		}
		$newsrc = $this->outputHTML;
		foreach ($matches[0] as $match) {
			if (strpos($match, $this->baseURL) !== false) {
                foreach ($this->excludeJS as $mtc) {
                    if (strpos($match, $mtc)!==false) {continue 2;}    
                }
				// read all javascript and remove from html source
				$newsrc = str_replace($match, '', $newsrc);
			}
		}
		clearstatcache();
		$filetime = filemtime($this->outputPath.'/'.$this->outputJSfilename);
		$newsrc = str_replace('</head>', '<script async src="'.$this->outputURL.'/'.$this->outputJSfilename.'?'.$filetime.'"></script></head>', $newsrc);
        $this->outputHTML = $newsrc;
        return $this;
    }

	public function combineCSS()
    {
		$pma = preg_match_all('/<link[^>]*href="([^"]*)\.css[^>]*"[^>]*>/', $this->outputHTML, $matches);
		if (($pma !== false && $pma > 0) && (!file_exists($this->outputPath.'/'.$this->outputCSSfilename) || $this->caching===false)) {
			$csscombined = "/* bgCMS Auto-Generated CSS File */\n";
			foreach ($matches[1] as $match) {
				if (strpos($match, $this->baseURL) !== false) {
				if (in_array(basename($match), $this->excludeCSS)) {continue;}					
					// read all css files and combine them
					$csscombined .= file_get_contents($this->cssPath.'/'.basename($match).'.css');					
				}
			}
			file_put_contents($this->outputPath.'/'.$this->outputCSSfilename, $csscombined);
		}
		$newsrc = $this->outputHTML;
		foreach ($matches[0] as $match) {
			if (strpos($match, $this->baseURL) !== false) {
                foreach ($this->excludeCSS as $mtc) {
                    if (strpos($match, $mtc)!==false) {continue 2;}    
                }
                // read all css and remove from html source
				$newsrc = str_replace($match, '', $newsrc);
			}
		}
		clearstatcache();
		$filetime = filemtime($this->outputPath.'/'.$this->outputCSSfilename);
		$newsrc = str_replace('</head>', '<link href="'.$this->outputURL.'/'.$this->outputCSSfilename.'?'.$filetime.'" rel="preload" as="style" onload="this.rel=\'stylesheet\'">'.'</head>', $newsrc);
		$this->outputHTML = $newsrc;
        return $this;
	}

}
