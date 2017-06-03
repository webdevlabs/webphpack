# WebPHPack
WebPHPack is a simple php alternative to webpack for auto combining multiple JS and CSS files into single files. 

### Usage
```
$webphpack = new WebPHPack($htmlsource);
$webphpack->combineJS();
$webphpack->combineCSS();
$webphpack->output();
```
or nested
```
$webphpack = new WebPHPack($htmlsource);
$newHTMLsource = $webphpack->combineJS()->combineCSS()->output();
```
