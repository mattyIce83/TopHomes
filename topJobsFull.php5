//<?php
class cURL {
  var $headers;
  var $user_agent;
  var $compression;
  var $cookie_file;
  var $proxy;

  // Create a new object
  function cURL($cookies=FALSE,$cookie='cookies.txt',$compression='gzip',$proxy='') {
    $this->headers[] = 'Accept: text/xml';
    $this->headers[] = 'Connection: Keep-Alive';
    $this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
    $this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
    $this->compression=$compression;
    $this->proxy=$proxy;
    $this->cookies=$cookies;
    if ($this->cookies == TRUE) $this->cookie($cookie);
  }

  // if using cookies
  function cookie($cookie_file) {
    if (file_exists($cookie_file)) {
      $this->cookie_file=$cookie_file;
    } else {
      fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
      $this->cookie_file=$cookie_file;
      fclose($this->cookie_file);
    }
  }

  // get content from a URL
  function get($url) {
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
    curl_setopt($process,CURLOPT_ENCODING , $this->compression);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);

    curl_close($process);
    return $return;
  }

  // do http post to a URL
  function post($url,$data) {
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
    curl_setopt($process, CURLOPT_ENCODING , $this->compression);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
    curl_setopt($process, CURLOPT_POSTFIELDS, $data);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($process, CURLOPT_POST, 1);
    $return = curl_exec($process);
    curl_close($process);
    return $return;
  }
  
  function error($error) {
    echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
    die;
  }
}
$url = "http://jobs.tampabay.com/rss/topjobs.xml";
$topJobcURL = new cURL();
$topJobcRSSString = $topJobcURL->get($url);
$maxCount = $SOSE->GetVar("count");
if ($maxCount == "")
  $maxCount = 4;
unset($topJobcURL);
$returnValue = "<div class=\"content topJobsContent\"><ul class=\"linkListLite\" id=\"topjobs\"><ul>";
$counter = 0;
if ($topJobcRSSString) {
  $topJobcRSS = simplexml_load_string($topJobcRSSString);
}
foreach ( $topJobcRSS->channel->item as $jobs ) {
  $returnValue .= "<li><span class=\"jobTitle\">".$jobs->title."</span><br><a class=\"jobLink\" href=\"".$jobs->link."\">".$jobs->description."</a></li>";
  $counter++;
}
unset($topJobcRSSString);
$returnValue .= "</ul></ul></div>";
$SOSE->Echo ($returnValue);