<?php
/**
 * WebPHPack v1.2.3
 * webpack PHP alternative
 * 
 * @package phreak 
 * @author Simeon Lyubenov <lyubenov@gmail.com> www.webdevlabs.com
 *
 */

namespace Phreak;

class WebPHPack
{
    private $outputHTML;
    public $matchString;
    public $outputURL;
    public $outputPath;
    public $jsPath;
    public $cssPath;
    public $excludeCSS;
    public $excludeJS;    
    public $outputJSfilename;
    public $outputCSSfilename;    
    public $caching;
    public $httpush;
    private $headerlinks;

    public function __construct($inputHTML)
    {
        $this->outputHTML = $inputHTML;
        $this->matchString = BASE_URL;
        $this->jsPath = ROOT_DIR.'/public/assets/js';
        $this->cssPath = ROOT_DIR.'/public/assets/css';
        $this->outputPath = ROOT_DIR.'/public/assets/cache';
        $this->outputURL = BASE_URL.'/assets/cache';
        $this->outputJSfilename = 'bundle.js';
        $this->outputCSSfilename = 'style.css';
        $this->excludeJS = [];
        $this->excludeCSS = [];
        $this->caching = false;
        $this->httpush = false;
    }

    public function output()
    {
        if ($this->httpush) {
            $this->pushHeaders();
        }
        return $this->outputHTML;
    }

    public function combineJS ()
    {
        $pma = preg_match_all('/<script[^>]*src="([^"]*)\.js[^>]*"[^>]*><\/script>/', $this->outputHTML, $matches);
        if (($pma !== false && $pma > 0) && (!file_exists($this->outputPath.'/'.$this->outputJSfilename) || $this->caching===false)) {
            $jscombined = "/* WebPHPack Auto-Generated JS File */\n";
            foreach ($matches[1] as $match) {
                if (strpos($match, $this->matchString) !== false) {
                    if (in_array(basename($match), $this->excludeJS)) {continue;}					
                    // read all javascript files and combine them
                    $jscombined .= file_get_contents($this->jsPath.'/'.basename($match).'.js');
                }
            }    
            file_put_contents($this->outputPath.'/'.$this->outputJSfilename, $jscombined);
        }
        $newsrc = $this->outputHTML;
        foreach ($matches[0] as $match) {
            if (strpos($match, $this->matchString) !== false) {
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
        $this->headerlinks[] = [
            'link'=>$this->outputURL.'/'.$this->outputJSfilename.'?'.$filetime,
            'type'=>'script'
        ];        
        $this->outputHTML = $newsrc;
        return $this;
    }

    public function combineCSS()
    {
        $pma = preg_match_all('/<link[^>]*href="([^"]*)\.css[^>]*"[^>]*>/', $this->outputHTML, $matches);
        if (($pma !== false && $pma > 0) && (!file_exists($this->outputPath.'/'.$this->outputCSSfilename) || $this->caching===false)) {
            $csscombined = "/* WebPHPack Auto-Generated CSS File */\n";
            foreach ($matches[1] as $match) {
                if (strpos($match, $this->matchString) !== false) {
                if (in_array(basename($match), $this->excludeCSS)) {continue;}					
                    // read all css files and combine them
                    $csscombined .= file_get_contents($this->cssPath.'/'.basename($match).'.css');					
                }
            }
            file_put_contents($this->outputPath.'/'.$this->outputCSSfilename, $csscombined);
        }
        $newsrc = $this->outputHTML;
        foreach ($matches[0] as $match) {
            if (strpos($match, $this->matchString) !== false) {
                foreach ($this->excludeCSS as $mtc) {
                    if (strpos($match, $mtc)!==false) {continue 2;}    
                }
                // read all css and remove from html source
                $newsrc = str_replace($match, '', $newsrc);
            }
        }
        clearstatcache();
        $filetime = filemtime($this->outputPath.'/'.$this->outputCSSfilename);
        $newsrc = str_replace('</head>', '<link href="'.$this->outputURL.'/'.$this->outputCSSfilename.'?'.$filetime.'" rel="stylesheet" media="none" onload="if(media!=\'all\')media=\'all\'">'.'</head>', $newsrc);
        $this->headerlinks[] = [
            'link'=>$this->outputURL.'/'.$this->outputCSSfilename.'?'.$filetime,
            'type'=>'style'
        ];
        $this->outputHTML = $newsrc;
        return $this;
    }

    public function pushHeaders()
    {
        $pushlinks='';
        foreach ($this->headerlinks as $resource => $src) {
            $pushlinks .= '<'.$src['link'].'>; rel=preload; as='.$src['type'].', ';             
        }
        header("Link: ".$pushlinks);
    }

}
