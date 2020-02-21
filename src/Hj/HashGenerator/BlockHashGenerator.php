<?php

namespace Hj\HashGenerator;

class BlockHashGenerator extends HashGenerator
{
    /**
     * @return int
     */
    protected function getLength()
    {
        return 32;
    }
}