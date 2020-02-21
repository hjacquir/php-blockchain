<?php

namespace Hj;

use DateTime;
use Exception;
use Hj\HashGenerator\BlockHashGenerator;

class Block
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $previousHash;

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var BlockHashGenerator
     */
    private $hashGenerator;

    /**
     * Block constructor.
     * @param Data $data
     * @param BlockHashGenerator $hashGenerator
     * @throws Exception
     */
    public function __construct(Data $data, BlockHashGenerator $hashGenerator)
    {
        $this->hashGenerator = $hashGenerator;
        $this->data = $data;
        $this->created = new DateTime();
        $this->previousHash = $this->hashGenerator->getLastHashGenerated();

        $this->hash = $this->hashGenerator->generate();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getPreviousHash()
    {
        return $this->previousHash;
    }
}