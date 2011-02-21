<?php
/* ----------------------------------------------------------------------------
* Mark Schmale <ma.schmale@googlemail.com>
* ----------------------------------------------------------------------------
* Solange Sie diesen Vermerk nicht entfernen, können Sie mit der Datei machen,
* was Sie möchten. Wenn wir uns eines Tages treffen und Sie denken, die Datei
* ist es wert, können Sie mir dafür ein Bier ausgeben.
* ----------------------------------------------------------------------------
*/

class Connector
{
    /**
* curl handle used in this class
* @var curl handle
*/
    private $_handle;

    /**
* configuration for this connector
* @var ConnectorConfig
*/
    private $_config;

    public function __construct(ConnectorConfig $cfg)
    {
        $this->_config = $cfg;
        $this->_handle = $this->setupCurl();
        $this->refreshSession();
    }

    /**
* initiates the curl connection
* @return curl
*/
    protected function setupCurl()
    {
        $c = curl_init();
        if(!file_exists($this->_config->cookieJar)) {
            if(!file_put_contents($this->_config->cookieJar, '')) {
                echo 'kann cookiejar nicht öffnen';
            }
        }
        $opts = array(CURLOPT_AUTOREFERER => false,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_VERBOSE => $this->_config->debug === true,
                      CURLOPT_COOKIEFILE => $this->_config->cookieJar,
                      CURLOPT_COOKIEJAR => $this->_config->cookieJar);

        curl_setopt_array($c, $opts);
        return $c;
    }

    /**
* Executes a GET Request
* @param String $url
* @return String
*/
    protected function requestGet($url)
    {
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_HTTPGET, true);
        return curl_exec($this->_handle);
    }

    /**
* Executes a PUT Request
* @param String $url
* @param array $data
* @return String
*/
    protected function requestPost($url, array $data)
    {
        $qry = '';
        $c = '';
        foreach($data as $k => $v) {
            $qry .= $c.$k.'='.urlencode($v);
            $c = '&';
        }
        curl_setopt($this->_handle, CURLOPT_URL, $url);
        curl_setopt($this->_handle, CURLOPT_POST, true);
        curl_setopt($this->_handle, CURLOPT_POSTFIELDS, $qry);
        return curl_exec($this->_handle);
    }

    /**
* refreshs the session
*/
    protected function refreshSession()
    {
        // check login
        $page = $this->requestGet($this->_config->padHost);
        if($this->isLoginPage($page)) {
            $url = $this->getLoginUrl($page);
            $url = $this->_config->padHost.$url;
            $new_page = $this->requestPost($url, array('email' => $this->_config->padUser,
                                                       'password' => $this->_config->padPass));
        }
    }

    /**
* checks if the $content is a login page
* @param string $content
* @return boolean
*/
    protected function isLoginPage($content)
    {
        return (strpos($content, 'form id="signin-form"') !== false);
    }

    /**
* gets the login url from the $content
* @param string $content
* @return string url
*/
    protected function getLoginUrl($content)
    {
        $regexp = '(form id="signin-form" action="([^"]*)" method="post")U';
        preg_match($regexp, $content, $matches);
        return $matches[1];
    }

    /**
* returns the content from a pad
* @param string $pagename
* @param string $format html or plain
* @return string
*/
    public function getPage($pagename, $format='html')
    {
        $url = $this->_config->padHost.'/ep/pad/export/'.$pagename.'/latest/?format='.$format;
        $content = $this->requestGet($url);
        if($format == 'html') {
            preg_match('(<body>(.*)</body>)sU', $content, $matches);
            $content = str_replace("\n", "", $matches[1]);
        }
        return $content;
    }
}