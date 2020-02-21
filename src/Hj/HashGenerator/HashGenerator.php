<?php

namespace Hj\HashGenerator;

abstract class HashGenerator
{
    /**
     * @var string
     */
    private $alphabet;

    /**
     * @var int
     */
    private $alphabetLength;

    /**
     * @var string
     */
    private $lastHashGenerated = '';

    public function __construct()
    {
        $this->alphabet = implode(range('a', 'z'))
            . implode(range('A', 'Z'))
            . implode(range(0, 9));

        $this->alphabetLength = strlen($this->alphabet);
    }

    /**
     * @return string
     */
    public function generate()
    {
        $length = $this->getLength();

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        $this->lastHashGenerated = $token;

        return $token;
    }

    /**
     * @return string
     */
    public function getLastHashGenerated(): string
    {
        return $this->lastHashGenerated;
    }

    /**
     * @return int
     */
    protected abstract function getLength();

    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    private function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;

        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}