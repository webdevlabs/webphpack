# webphpack
WebPHPack is a php alternative to webpack for combining JS and CSS files. 

### Usage
```
$webphpack = new WebPHPack($htmlsource);
$webphpack->combineJS();
$webphpack->combineCSS();
$newHTMLsource = $webphpack->output();
```
