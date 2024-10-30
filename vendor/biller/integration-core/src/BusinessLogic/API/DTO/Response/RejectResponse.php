<?php

namespace Biller\BusinessLogic\API\DTO\Response;

use Biller\Infrastructure\Data\DataTransferObject;

class RejectResponse extends DataTransferObject
{
    /**
     * @var string
     */
    private $details;

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    public function toArray()
    {
        return [
            'details' => $this->details
        ];
    }

    /**
     * @param array $data
     * @return RejectResponse
     */
    public static function fromArray(array $data)
    {
        $response = new self();
        $response->details = static::getDataValue($data, 'details', static::getDataValue($data, 'message', ''));

        return $response;
    }
}