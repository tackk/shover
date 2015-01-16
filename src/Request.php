<?php
namespace Tackk\Shover;

class Request
{
    /**
     * The HTTP Method
     * @var string
     */
    protected $method = 'GET';

    /**
     * The URI
     * @var string
     */
    protected $uri = null;

    /**
     * The Query Parameters
     * @var array
     */
    protected $query = [];

    /**
     * The Request Body
     * @var string
     */
    protected $body = null;

    /**
     * Whether the request has been prepared or not.
     * @var bool
     */
    protected $signed = false;

    /**
     * @param string $method The HTTP Method
     * @param string $uri    The URI Path
     * @param array  $query  The Query to send.
     * @param string $body   The Request Body
     */
    public function __construct($method, $uri, $query = [], $body = null)
    {
        $this->setMethod($method);
        $this->setUri($uri);
        $this->setQuery($query);
        $this->setBody($body);
    }

    /**
     * Gets the HTTP method.
     *
     * @return string The HTTP Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set's the HTTP method.
     *
     * @param string $method The HTTP Method
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Gets the URI.
     *
     * @return string The URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set's the Uri.
     *
     * @param string $uri The URI
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get the Query
     *
     * @return array
     */
    public function getQuery($asString = false)
    {
        return $asString ? http_build_query($this->query) : $this->query;
    }

    /**
     * Set the Query
     *
     * @param array $query The Query
     */
    public function setQuery($query)
    {
        $this->signed = false;
        $this->query  = $query;

        return $this;
    }

    /**
     * Merge in the given Query Parameters.
     *
     * @param  array $query The Query Parameters to merge in.
     */
    public function mergeQuery($query)
    {
        $this->signed = false;
        $this->query  = array_merge($this->query, $query);

        return $this;
    }

    /**
     * Returns if this request has been signed.
     *
     * @return boolean
     */
    public function isSigned()
    {
        return $this->signed;
    }

    /**
     * Sets if this Request is signed.
     *
     * @param bool $signed Whether it is signed.
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
    }

    /**
     * Get the Request Body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the Request Body
     *
     * @param string $body The Request Body
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

}
