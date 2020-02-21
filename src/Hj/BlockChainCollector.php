<?php

namespace Hj;

class BlockChainCollector implements \Iterator
{
    /**
     * @var BlockChain[]
     */
    private $blockChains = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @param BlockChain $blockChain
     */
    public function collect(BlockChain $blockChain)
    {
        if (!in_array($blockChain, $this->blockChains, true)) {
            array_push($this->blockChains, $blockChain);
            $this->index ++;
        }
    }

    /**
     * @return BlockChain[]
     */
    public function getBlockChains(): array
    {
        return $this->blockChains;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return BlockChain.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->blockChains[$this->index];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index ++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        if (isset($this->blockChains[$this->index]))
        {
            return true;
        }

        return false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }
}