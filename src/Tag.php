<?php

namespace Salla\ZATCA;

use LengthException;

class Tag
{
    /**
     * ZATCA stores the length in one byte, so a value can not exceed 255 bytes
     * once it is UTF-8 encoded.
     *
     * @see E-Invoice Security Features Implementation Standards, section 4.1:
     *      "The length shall be stored in one byte."
     */
    const MAX_BYTE = 255;

    protected $tag;

    protected $value;

    public function __construct($tag, $value)
    {
        $this->tag = $tag;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * its important to get the number of bytes of a string instated of number of characters
     *
     * @return false|int
     */
    public function getLength()
    {
        return strlen($this->value);
    }

    /**
     * @return string Returns a string representing the encoded TLV data structure.
     *
     * @throws LengthException If the value is too long for the single length byte
     *         ZATCA allows, which would otherwise emit a malformed TLV.
     */
    public function __toString()
    {
        $value = (string) $this->getValue();

        return $this->toByte($this->getTag()).$this->toByte($this->getLength()).($value);
    }

    /**
     * To convert the tag or the length to a single unsigned byte.
     *
     * @param $value
     *
     * @return string
     *
     * @throws LengthException If the value does not fit in one byte.
     */
    protected function toByte($value)
    {
        if ($value < 0 || $value > self::MAX_BYTE) {
            throw new LengthException(sprintf(
                'Tag %d: the value is %d bytes once UTF-8 encoded, but ZATCA stores the length in a single byte (max %d).',
                $this->getTag(),
                $value,
                self::MAX_BYTE
            ));
        }

        return chr($value);
    }
}
