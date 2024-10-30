<?php

namespace Biller\BusinessLogic\API\Http\Request;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class HttpRequest
 *
 * @package Biller\BusinessLogic\API\Http\DTO
 */
class HttpRequest extends DataTransferObject
{
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var array
     */
    protected $body;
    /**
     * @var array
     */
    protected $queries;
    /**
     * @var array
     */
    protected $headers;
    /**
     * @var int
     */
    protected $retries;

    /**
     * HttpRequest constructor.
     *
     * @param string $endpoint
     * @param array $body
     * @param array $queries
     * @param array $headers
     */
    public function __construct(
        $endpoint,
        array $body = [],
        array $queries = [],
        array $headers = []
    )
    {
        $this->endpoint = $endpoint;
        $this->body = $body;
        $this->queries = $queries;
        $this->headers = $headers;
        $this->retries = 0;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param array $queries
     */
    public function setQueries(array $queries)
    {
        $this->queries = $queries;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $httpRequest = new static(
            static::getDataValue($data, 'endpoint'),
            static::getDataValue($data, 'body', []),
            static::getDataValue($data, 'queries', []),
            static::getDataValue($data, 'headers', [])
        );
        $httpRequest->setRetries(static::getDataValue($data, 'retries', 0));

        return $httpRequest;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'endpoint' => $this->getEndpoint(),
            'body' => $this->getBody(),
            'queries' => $this->getQueries(),
            'headers' => $this->getHeaders(),
            'retires' => $this->getRetries()
        ];
    }
}
