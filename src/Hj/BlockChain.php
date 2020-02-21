<?php

namespace Hj;

use Hj\Exception\BlockChainLockedException;
use Hj\HashGenerator\BlockChainHashGenerator;

class BlockChain
{
    /**
     * @var Block[]
     */
    private $blocks = [];

    /**
     * @var array
     */
    private $history = [];

    /**
     * @var BlockChain[]
     */
    private $friends = [];

    /**
     * @var string
     */
    private $uniqId = '';

    /**
     * @var BlockChainHashGenerator
     */
    private $hashGenerator;

    /**
     * @var BlockChainCollector
     */
    private $blockChainCollector;

    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * BlockChain constructor.
     * @param BlockChainHashGenerator $hashGenerator
     * @param BlockChainCollector $blockChainCollector
     */
    public function __construct(
        BlockChainHashGenerator $hashGenerator,
        BlockChainCollector $blockChainCollector
    )
    {
        $this->hashGenerator = $hashGenerator;
        $this->blockChainCollector = $blockChainCollector;

        $this->blockChainCollector->collect($this);
        $this->uniqId = $this->hashGenerator->generate();
        $this->initialize();
    }


    /**
     * @return string
     */
    public function getUniqId(): string
    {
        return $this->uniqId;
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @return array
     */
    public function getFriends(): array
    {
        return $this->friends;
    }

    /**
     * @return Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    public function lock()
    {
        $this->isLocked = true;
    }

    public function unLock()
    {
        $this->isLocked = false;
    }

    public function isLocked()
    {
        return $this->isLocked;
    }

    private function initialize()
    {
        $this->lock();
        $this->initializeFriends();
        $this->initializeBlocks();
        $this->unLock();
    }

    /**
     * @param Block $block
     * @throws BlockChainLockedException
     */
    public function addBlock(Block $block)
    {
        if ($this->isLocked) {
           throw new BlockChainLockedException("The current blockchain is locked. Please try again in few minutes.");
        }

        // add block to block chain
        array_push($this->blocks, $block);
        // update the history
        $this->updateHistory($block);
        // update other block chain except this
        $this->addBlockToFriends($block);
    }

    /**
     * @param BlockChain $friend
     */
    private function addFriend(BlockChain $friend)
    {
        if (!in_array($friend, $this->friends, true)) {
            // add a friend to the current block chain
            array_push($this->friends, $friend);
        }
    }

    private function initializeFriends()
    {
        // initialize friends
        foreach ($this->blockChainCollector->getBlockChains() as $blockChain) {
            // add friends from collector
            if (!in_array($blockChain, $this->friends, true) && $blockChain->getUniqId() !== $this->getUniqId()) {
                $blockChain->lock();
                // add a friend to the current block chain
                array_push($this->friends, $blockChain);
                // add the current block chain to the other block chain
                $blockChain->addFriend($this);
                $blockChain->unLock();
            }
        }

    }

    private function initializeBlocks()
    {
        $this->blockChainCollector->rewind();
        // get the first block chain
        $currentBlockChain = $this->blockChainCollector->current();
        // if the current block chain is not the first we add all blocks
        if ($this !== $currentBlockChain) {
            $currentBlockChain->lock();
            foreach ($currentBlockChain->getBlocks() as $block) {
                $this->addBlockForInitializing($block);
            }
            $currentBlockChain->unLock();
        }
    }

    /**
     * @param Block $block
     */
    private function addBlockForInitializing(Block $block)
    {
        // add block to block chain
        array_push($this->blocks, $block);
    }

    /**
     * @param Block $block
     * @throws BlockChainLockedException
     */
    private function addBlockToFriends(Block $block)
    {
        foreach ($this->friends as $friend) {
            // add block to other blockChain except the current
            if (!in_array($block, $friend->getBlocks(), true)) {
                $friend->addBlock($block);
            }
        }
    }

    /**
     * @param Block $block
     */
    private function updateHistory(Block $block)
    {
        $chainHistory = [
            'block' => [
                'chainId' => $this->getUniqId(),
                'createdDate' => $block->getCreated()->format('Y-m-d H:i:s.u'),
                'previousHash' => $block->getPreviousHash(),
                'hash' => $block->getHash(),
            ],
        ];
        array_push($this->history, $chainHistory);
    }
}