<?php

namespace Biller\BusinessLogic\API\DTO\Response;


use Biller\Infrastructure\Data\DataTransferObject;

class SuccessResponse extends DataTransferObject
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'description' => $this->description
        ];
    }

    /**
     * @param array $data
     * @return SuccessResponse
     */
    public static function fromArray(array $data)
    {
        $response = new self();
        $response->id = static::getDataValue($data, 'id', '');
        $response->description = static::getDataValue($data, 'description', '');

        return $response;
    }
}