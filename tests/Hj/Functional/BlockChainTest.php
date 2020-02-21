<?php

namespace Hj\Tests\Functional;

use Hj\Block;
use Hj\BlockChain;
use Hj\BlockChainCollector;
use Hj\Data;
use Hj\HashGenerator\BlockChainHashGenerator;
use Hj\HashGenerator\BlockHashGenerator;
use PHPUnit\Framework\TestCase;

class BlockChainTest extends TestCase
{
    public function testAddBlock()
    {
        $blockChainHashGenerator = new BlockChainHashGenerator();
        $blockHashGenerator = new BlockHashGenerator();
        $blockChainCollector = new BlockChainCollector();

        $blockChain1 = new BlockChain($blockChainHashGenerator, $blockChainCollector);
        $this->assertExpectedFriends($blockChain1, []);

        $blockChain2 = new BlockChain($blockChainHashGenerator, $blockChainCollector);
        $this->assertExpectedFriends($blockChain1, [$blockChain2]);
        $this->assertExpectedFriends($blockChain2, [$blockChain1]);

        $expected = [];
        $this->assertExpectedBlocks($blockChain1, $expected);
        $this->assertExpectedBlocks($blockChain2, $expected);

        $b1 = new Block(new Data(), $blockHashGenerator);
        $b2 = new Block(new Data(), $blockHashGenerator);

        // add block 1 to block chain 1
        $blockChain1->addBlock($b1);

        // block chain 1 and block chain 2 must contain the same block b1
        $expected = [
            $b1,
        ];
        $this->assertExpectedBlocks($blockChain1, $expected);
        $this->assertExpectedBlocks($blockChain2, $expected);

        // add block 2 to block chain 2
        $blockChain2->addBlock($b2);

        // block chain 1 and block chain 2 must contain the blocks b1,b2
        $expected = [
            $b1,
            $b2,
        ];
        $this->assertExpectedBlocks($blockChain1, $expected);
        $this->assertExpectedBlocks($blockChain2, $expected);

        $blockChain3 = new BlockChain($blockChainHashGenerator, $blockChainCollector);
        $this->assertExpectedFriends($blockChain1, [$blockChain2, $blockChain3]);
        $this->assertExpectedFriends($blockChain2, [$blockChain1, $blockChain3]);
        $this->assertExpectedFriends($blockChain3, [$blockChain1, $blockChain2]);

    }

    /**
     * @param BlockChain $blockChain
     * @param array $expectedBlocks
     */
    private function assertExpectedBlocks(BlockChain $blockChain, array $expectedBlocks)
    {
        self::assertSame($expectedBlocks, $blockChain->getBlocks());
    }

    /**
     * @param BlockChain $blockChain
     * @param array $expected
     */
    private function assertExpectedFriends(BlockChain $blockChain, array $expected)
    {
        self::assertSame($expected, $blockChain->getFriends());
    }
}