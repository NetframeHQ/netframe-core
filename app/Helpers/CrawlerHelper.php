<?php

namespace App\Helpers;

class CrawlerHelper
{
    protected $userAgent = null;

    protected $httpHeaders = array();

    protected $matches = array();


    /**
     * Class constructor.
     */
    public function __construct(array $headers = null, $userAgent = null)
    {
        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    public function setHttpHeaders($httpHeaders = null)
    {
        // use global _SERVER if $httpHeaders aren't defined
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }
        // clear existing headers
        $this->httpHeaders = array();
        // Only save HTTP headers. In PHP land, that means only _SERVER vars that
        // start with HTTP_.
        foreach ($httpHeaders as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $this->httpHeaders[$key] = $value;
            }
        }
    }

    public function getUaHttpHeaders()
    {
        return config('crawlers.http-headers');
    }

    public function setUserAgent($userAgent = null)
    {
        if (false === empty($userAgent)) {
            return $this->userAgent = $userAgent;
        } else {
            $this->userAgent = null;
            foreach ($this->getUaHttpHeaders() as $altHeader) {
                // @todo: should use getHttpHeader(), but it would be slow. (Serban)
                if (false === empty($this->httpHeaders[$altHeader])) {
                    $this->userAgent .= $this->httpHeaders[$altHeader].' ';
                }
            }
            return $this->userAgent = (!empty($this->userAgent) ? trim($this->userAgent) : null);
        }
    }

    public function getRegex()
    {
        return '('.implode('|', config('crawlers.user-agent')).')';
    }

    public function isCrawler($userAgent = null)
    {
        $agent = is_null($userAgent) ? $this->userAgent : $userAgent;

        $result = preg_match('/'.$this->getRegex().'/i', $agent, $matches);

        if ($matches) {
            $this->matches = $matches;
        }

        return (bool) $result;
    }

    public function getMatches()
    {
        return $this->matches[0];
    }
}
