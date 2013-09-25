<?php

namespace attitude\Elements;

class Request_Element
{
    private $accept = null;
    private $accept_language = null;
    private $accept_encoding = null;

    private $search_query = array();

    private $request_uri = null;
    private $request_method = null;
    private $request_body = null;

    public function __construct($request_method, $request_uri, $search_query = array(), $accept = '*/*', $request_body = null, $accept_language = '*', $accept_encoding = 'utf8')
    {
        $this->setRequestMethod($request_method);
        $this->setRequestURI($request_uri);
        $this->setAccept($accept);

        $this->search_query = (array) $search_query;

        // @todo: Dependency setters
        $this->request_body = $request_body;
        $this->accept_language = 'en';
        $this->accept_encoding = 'utf8';
    }

    private function setRequestMethod($dependency)
    {
        if (!is_string($dependency)) {
            throw new HTTPException('Request method must be a string.');
        }

        $valid_methods = array('GET', 'POST', 'PUT', 'DELETE');

        if (!in_array($dependency, $valid_methods)) {
            throw new HTTPException('Method must be one of the '.implode(', ', $valid_methods).' methods.');
        }

        $this->request_method = $dependency;

        return $this;
    }

    private function setRequestURI($dependency)
    {
        if (!is_string($dependency) && !is_array($dependency)) {
            throw new HTTPException('Request URI must be a string or array.');
        }

        if (is_string($dependency)) {
            $dependency = explode('/', trim('/', $dependency));
        }

        $this->request_uri = $dependency;

        return $this;
    }

    private function setAccept($dependency)
    {
        if (!is_string($dependency)) {
            throw new HTTPException('Accept header must be a string.');
        }

        $this->accept = explode(',', str_replace(' ', '', $dependency));

        foreach ($this->accept as &$accept) {
            $accept = explode('/', $accept);
            $accept = array_pop($accept);
        }

        return $this;
    }

    public function getAccept()
    {
        return empty($this->accept) ? array() : $this->accept;
    }

    public function getRequestURIArray()
    {
        return empty($this->request_uri) ? array() : $this->request_uri;
    }

    public function getRequestURI()
    {
        return '/'.implode('/', $this->getRequestURIArray());
    }

    public function getLocation()
    {
        return 'http'. ($_SERVER['SCHEME']==='HTTPS' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$this->getRequestURI();
    }

    public function getSearchQuery($key=null)
    {
        if ($key===null) {
            return $this->search_query;
        }

        if (settype($key, 'string') && isset($this->search_query[$key])) {
            return $this->search_query[$key];
        }

        throw new HTTPException(404);
    }

    public function getUUID()
    {
        return isset($this->request_uri[1]) ? $this->request_uri[1] : null;
    }

    public function getRequestMethod()
    {
        return $this->request_method;
    }

    public function getRequestBody()
    {
        return $this->request_body;
    }
}
