<?php

namespace Biller\Infrastructure\Http;

use Biller\Infrastructure\Configuration\Configuration;
use Biller\Infrastructure\Http\DTO\OptionsDTO;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Logger\Logger;
use Biller\Infrastructure\ServiceRegister;

/**
 * Class HttpClient.
 *
 * @package Biller\Infrastructure\Http
 */
abstract class HttpClient
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Unauthorized HTTP status code.
     */
    const HTTP_STATUS_CODE_UNAUTHORIZED = 401;
    /**
     * Forbidden HTTP status code.
     */
    const HTTP_STATUS_CODE_FORBIDDEN = 403;
    /**
     * Not found HTTP status code.
     */
    const HTTP_STATUS_CODE_NOT_FOUND = 404;
    /**
     * HTTP GET method.
     */
    const HTTP_METHOD_GET = 'GET';
    /**
     * HTTP POST method.
     */
    const HTTP_METHOD_POST = 'POST';
    /**
     * HTTP PUT method.
     */
    const HTTP_METHOD_PUT = 'PUT';
    /**
     * HTTP DELETE method.
     */
    const HTTP_METHOD_DELETE = 'DELETE';
    /**
     * HTTP PATCH method.
     */
    const HTTP_METHOD_PATCH = 'PATCH';
    /**
     * Configuration service.
     *
     * @var Configuration
     */
    private $configService;

    /**
     * Create, log and send request.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional.
     *
     * @return HttpResponse Response from making HTTP request.
     *
     * @throws HttpCommunicationException
     */
    public function request($method, $url, $headers = array(), $body = '')
    {
        Logger::logDebug(
            "Sending http request to $url",
            'Core',
            array(
                'Type' => $method,
                'Endpoint' => $url,
                'Headers' => json_encode($headers),
                'Content' => $body,
            )
        );

        $response = $this->sendHttpRequest($method, $url, $headers, $body);

        Logger::logDebug(
            "Http response from $url",
            'Core',
            array(
                'ResponseFor' => "$method at $url",
                'Status' => $response->getStatus(),
                'Headers' => json_encode($response->getHeaders()),
                'Content' => $response->getBody(),
            )
        );

        return $response;
    }

    /**
     * Create, log and send request asynchronously.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional. Default value for
     * request body is '1' to ensure minimal request data in case of POST, PUT, PATCH methods.
     *
     */
    public function requestAsync($method, $url, $headers = array(), $body = '1')
    {
        Logger::logDebug(
            "Sending async http request to $url",
            'Core',
            array(
                'Type' => $method,
                'Endpoint' => $url,
                'Headers' => json_encode($headers),
                'Content' => $body,
            )
        );

        $this->sendHttpRequestAsync($method, $url, $headers, $body);
    }

    /**
     * Auto configures http call options. Tries to make a request to the provided URL with all configured
     * configurations of HTTP options. When first succeeds, stored options should be used.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional.
     *
     * @return bool TRUE if configuration went successfully; otherwise, FALSE.
     */
    public function autoConfigure($method, $url, $headers = array(), $body = '')
    {
        if ($this->isRequestSuccessful($method, $url, $headers, $body)) {
            return true;
        }

        $combinations = $this->getAdditionalOptionsCombinations();
        foreach ($combinations as $combination) {
            $this->setAdditionalOptions($combination);
            if ($this->isRequestSuccessful($method, $url, $headers, $body)) {
                return true;
            }

            // if request is not successful, reset options combination.
            $this->resetAdditionalOptions();
        }

        return false;
    }

    /**
     * Create and send request.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional.
     *
     * @return HttpResponse Response object.
     *
     * @throws HttpCommunicationException
     *      Only in situation when there is no connection or no response.
     */
    abstract protected function sendHttpRequest($method, $url, $headers = array(), $body = '');

    /**
     * Create and send request asynchronously.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional.  Default value for
     * request body is '1' to ensure minimal request data in case of POST, PUT, PATCH methods.
     */
    abstract protected function sendHttpRequestAsync($method, $url, $headers = array(), $body = '1');

    /**
     * Get additional options combinations for request.
     *
     * @return array
     *  Array of additional options combinations. Each array item should be an array of OptionsDTO instance.
     */
    protected function getAdditionalOptionsCombinations()
    {
        // Left blank intentionally so integrations can override this method,
        // in order to return all possible combinations for additional curl options
        return array();
    }

    /**
     * Save additional options for request.
     *
     * @param OptionsDTO[] $options Additional option to add to HTTP request.
     */
    protected function setAdditionalOptions($options)
    {
        // Left blank intentionally so integrations can override this method,
        // in order to save combination to some persisted array which HttpClient can use it later while creating request
    }

    /**
     * Reset additional options for request to default value.
     */
    protected function resetAdditionalOptions()
    {
        // Left blank intentionally so integrations can override this method,
        // in order to reset to its default values persisted array which `HttpClient` uses later while creating request
    }

    /**
     * Verifies the response and returns TRUE if valid, FALSE otherwise
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE etc.)
     * @param string $url Request URL. Full URL where request should be sent.
     * @param array|null $headers Request headers to send. Key as header name and value as header content. Optional.
     * @param string $body Request payload. String data to send as HTTP request payload. Optional.
     *
     * @return bool TRUE if request was successful; otherwise, FALSE.
     */
    private function isRequestSuccessful($method, $url, $headers = array(), $body = '')
    {
        try {
            $response = $this->request($method, $url, $headers, $body);
        } catch (HttpCommunicationException $ex) {
            $response = null;
        }

        return $response !== null && $response->isSuccessful();
    }

    /**
     * Gets the configuration service.
     *
     * @return Configuration Configuration service instance.
     */
    protected function getConfigService()
    {
        if (empty($this->configService)) {
            $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        }

        return $this->configService;
    }
}
