<?php

namespace Biller\Infrastructure\Http\DTO;

use Biller\Infrastructure\Data\DataTransferObject;

/**
 * Class OptionsDTO. Represents HTTP options set for Request by HttpClient.
 *
 * @package Biller\Infrastructure\Http\DTO
 */
class OptionsDTO extends DataTransferObject
{
    /**
     * Name of the option.
     *
     * @var string
     */
    private $name;
    /**
     * Value of the option.
     *
     * @var string
     */
    private $value;

    /**
     * OptionsDTO constructor.
     *
     * @param string $name Name of the option.
     * @param string $value Value of the option.
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Gets name of the option.
     *
     * @return string Name of the option.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets value of the option.
     *
     * @return string Value of the option.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Transforms DTO to an array representation.
     *
     * @return array DTO in array format.
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'value' => $this->getValue(),
        );
    }

    /**
     * Transforms raw array data to Options.
     *
     * @param array $data Raw array data.
     *
     * @return OptionsDTO Transformed object.
     */
    public static function fromArray(array $data)
    {
        return new static($data['name'], $data['value']);
    }
}
