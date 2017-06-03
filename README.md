# WebPHPack
#### WebPHPack is a simple php alternative to webpack for automatic concatenation of multiple JavaScript and CSS files into single files. 

This library replaces the style/script tags from the input html source code and returns plain html code ready for output.
Loading is done asynchronously for both javascript/css.


### Usage
```
$webphpack = new WebPHPack($htmlsource);
$webphpack->caching = true;
$webphpack->combineJS();
$webphpack->combineCSS();
$webphpack->output();
```
or nested
```
$webphpack = new WebPHPack($htmlsource);
$newHTMLsource = $webphpack->combineJS()->combineCSS()->output();
```
