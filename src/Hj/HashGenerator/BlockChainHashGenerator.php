<?php

namespace Hj\HashGenerator;

class BlockChainHashGenerator extends HashGenerator
{
    /**
     * @return int
     */
    protected function getLength()
    {
        return 16;
    }
}