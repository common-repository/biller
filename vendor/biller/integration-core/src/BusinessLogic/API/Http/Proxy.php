<?php

namespace Biller\BusinessLogic\API\Http;

use Biller\BusinessLogic\API\Http\Request\HttpRequest;
use Biller\BusinessLogic\API\Http\Exceptions\RequestNotSuccessfulException;
use Biller\BusinessLogic\API\Http\Exceptions\TooManyRequestsException;
use Biller\Infrastructure\Http\Exceptions\HttpCommunicationException;
use Biller\Infrastructure\Http\Exceptions\HttpRequestException;
use Biller\Infrastructure\Http\HttpClient;
use Biller\Infrastructure\Http\HttpResponse;
use Biller\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Exception;

/**
 * Class Proxy
 *
 * @package Biller\BusinessLogic\API\Http
 */
abstract class Proxy
{
	/**
	 * Default request retry request delay
	 */
	const DEFAULT_REQUEST_RETRY_DELAY = 5;
	/**
	 * Base Biller API URL PREFIX.
	 */
	const BASE_API_URL_PREFIX = 'api';
	/**
	 * Base Biller API URL SUFFIX.
	 */
	const BASE_API_URL_SUFFIX = 'biller.ai';
	/**
	 * Protocol.
	 */
	const PROTOCOL = 'https';
	/**
	 * Used API version.
	 */
	const API_VERSION = 'v1';
	/**
	 * Biller API URL SUFFIX.
	 */
	const API_URL_SUFFIX = 'api';
	/**
	 * Maximum number of request retries.
	 */
	const MAX_RETRIES = 5;
	const TO_MANY_REQUESTS = 429;
	const LIVE_MODE = 'live';
	/**
	 * Http client instance.
	 *
	 * @var HttpClient
	 */
	protected $httpClient;
	/**
	 * @var string
	 */
	protected $mode;

	/**
	 * Proxy constructor.
	 *
	 * @param HttpClient $httpClient
	 * @param string $mode
	 */
	public function __construct(HttpClient $httpClient, $mode = '')
	{
		$this->httpClient = $httpClient;
		$this->mode = $mode;
	}

	/**
	 * Performs GET HTTP request.
	 *
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse Get HTTP response.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function get(HttpRequest $request)
	{
		return $this->call(HttpClient::HTTP_METHOD_GET, $request);
	}

	/**
	 * Performs HTTP call.
	 *
	 * @param string $method Specifies which http method is utilized in call.
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse Response instance.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 * @throws Exception
	 */
	protected function call($method, HttpRequest $request)
	{
		$this->prepareRequest($request);

		$url = $this->getRequestUrl($request);

		$response = $this->httpClient->request(
			$method,
			$url,
			$request->getHeaders(),
			json_encode($request->getBody())
		);

		try {
			$this->validateResponse($response);
		} catch (TooManyRequestsException $e) {
			if ($request->getRetries() < self::MAX_RETRIES) {
				$now = time();
				$delay = array_key_exists('X-Rate-Limit-Reset', $response->getHeaders()) ?
					strtotime($response->getHeaders()['X-Rate-Limit-Reset']) - $now :
					self::DEFAULT_REQUEST_RETRY_DELAY;

				$response = $this->retryWithDelay($delay, $request, $method);
			} else {
				throw new RequestNotSuccessfulException(
					'Too many retries, request failed with the following message: ' . $e->getMessage(),
					$e->getCode()
				);
			}
		}

		return $response;
	}

	/**
	 * Prepares request for execution.
	 *
	 * @param HttpRequest $request
	 */
	protected function prepareRequest(HttpRequest $request)
	{
		$request->setHeaders(array_merge($request->getHeaders(), $this->getHeaders()));
	}

	/**
	 * Retrieves request headers.
	 *
	 * @return array Complete list of request headers.
	 *
	 */
	protected function getHeaders()
	{
		return array(
			'accept' => 'Accept: application/json',
			'content' => 'Content-Type: application/json',
		);
	}

	/**
	 * Retrieves full request url.
	 *
	 * @param HttpRequest $request
	 *
	 * @return string Full request url.
	 */
	protected function getRequestUrl(HttpRequest $request)
	{
		if ($this->isUrlAbsolute($request)) {
			return $request->getEndpoint();
		}

		$url = self::PROTOCOL . '://' . self::BASE_API_URL_PREFIX . '.';

		if (!empty($this->mode) && $this->mode !== self::LIVE_MODE ) {
			$url .= $this->mode . '.';
		}

		$url .= self::BASE_API_URL_SUFFIX . '/' .
		        self::API_VERSION . '/' .
		        self::API_URL_SUFFIX . '/' .
		        $request->getEndpoint();

        // Ensure ending slash in the API URL path
        $url = rtrim($url, "/") . '/';

		if (!empty($request->getQueries())) {
			$url .= '?' . $this->getQueryString($request);
		}

		return $url;
	}

	/**
	 * Prepares request's queries.
	 *
	 * @param HttpRequest $request
	 *
	 * @return string
	 */
	protected function getQueryString(HttpRequest $request)
	{
		$queryString = '';
		$queries = $request->getQueries();

		foreach ($queries as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $item) {
					$queryString .= http_build_query([$key => $item]) . '&';
				}

				unset($queries[$key]);
			}
		}

		return rtrim($queryString . http_build_query($queries), '&');
	}

	/**
	 * Validates HTTP response.
	 *
	 * @param HttpResponse $response Response object to be validated.
	 *
	 * @throws HttpRequestException
	 * @throws TooManyRequestsException
	 */
	protected function validateResponse(HttpResponse $response)
	{
		if ($response->isSuccessful()) {
			return;
		}

		if ($response->getStatus() === self::TO_MANY_REQUESTS) {
			throw new TooManyRequestsException();
		}

		$message = $body = $response->getBody();

		$httpCode = $response->getStatus();

		$responseBody = json_decode($body, true);
		if (is_array($responseBody) && isset($responseBody['details']) && is_string($responseBody['details'])) {
			$message = $responseBody['details'];
		}

        if (is_array($responseBody) && array_key_exists('non_field_errors', $responseBody)) {
            $message = implode("\n", (array)$responseBody['non_field_errors']);
        }

		throw new HttpRequestException($message, $httpCode);
	}

	/**
	 * Performs HTTP calls after $delay seconds.
	 *
	 * @param float $delay
	 * @param HttpRequest $request
	 * @param string $method
	 *
	 * @return HttpResponse
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function retryWithDelay($delay, HttpRequest $request, $method)
	{
		if ($delay > 0) {
			sleep($delay);
		}

		$request->setRetries($request->getRetries() + 1);

		return $this->call($method, $request);
	}

	/**
	 * Performs DELETE HTTP request.
	 *
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse DELETE HTTP response.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function delete(HttpRequest $request)
	{
		return $this->call(HttpClient::HTTP_METHOD_DELETE, $request);
	}

	/**
	 * Performs POST HTTP request.
	 *
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse Response instance.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function post(HttpRequest $request)
	{
		return $this->call(HttpClient::HTTP_METHOD_POST, $request);
	}

	/**
	 * Performs PUT HTTP request.
	 *
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse Response instance.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function put(HttpRequest $request)
	{
		return $this->call(HttpClient::HTTP_METHOD_PUT, $request);
	}

	/**
	 * Performs PATCH HTTP request.
	 *
	 * @param HttpRequest $request
	 *
	 * @return HttpResponse Response instance.
	 *
	 * @throws HttpCommunicationException
	 * @throws QueryFilterInvalidParamException
	 * @throws RequestNotSuccessfulException
	 * @throws HttpRequestException
	 */
	protected function patch(HttpRequest $request)
	{
		return $this->call(HttpClient::HTTP_METHOD_PATCH, $request);
	}

	/**
	 * @param HttpRequest $request
	 * @return bool
	 */
	protected function isUrlAbsolute(HttpRequest $request)
	{
		$host = self::BASE_API_URL_PREFIX . '.';

		if (!empty($this->mode) && $this->mode !== self::LIVE_MODE ) {
			$host .= $this->mode . '.';
		}

		$host .= self::BASE_API_URL_SUFFIX;

		return parse_url($request->getEndpoint(), PHP_URL_SCHEME) === self::PROTOCOL &&
		       parse_url($request->getEndpoint(), PHP_URL_HOST) === $host;
	}
}
