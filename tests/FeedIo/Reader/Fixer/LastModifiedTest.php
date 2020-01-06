<?php
/*
 * This file is part of the feed-io package.
 *
 * (c) Alexandre Debril <alex.debril@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FeedIo\Reader\Fixer;

use FeedIo\Adapter\NullResponse;
use FeedIo\Adapter\ResponseInterface;
use FeedIo\Feed;
use FeedIo\Feed\Item;
use FeedIo\Reader\Document;
use FeedIo\Reader\Result;
use Psr\Log\NullLogger;

use \PHPUnit\Framework\TestCase;

class LastModifiedTest extends TestCase
{

    /**
     * @var LastModified
     */
    protected $object;

    /**
     * @var \DateTime
     */
    protected $newest;

    protected function setUp()
    {
        $this->newest = new \DateTime('2014-01-01');

        $this->object = new LastModified();
        $this->object->setLogger(new NullLogger());
    }

    public function testSearchLastModified()
    {
        $feed = $this->getFeed();

        $this->assertEquals(
            $this->newest,
            $this->object->searchLastModified($feed)
        );
    }

    public function testCorrect()
    {
        $result = $this->getResultMock();
        $feed = $result->getFeed();

        $this->assertNull($feed->getLastModified());
        $this->object->correct($result);

        $this->assertEquals($this->newest, $feed->getLastModified());
    }

    protected function getFeed()
    {
        $item1 = new Item();
        $item1->setLastModified($this->newest);

        $item2 = new Item();
        $item2->setLastModified(new \DateTime('2013-01-01'));

        $feed = new Feed();
        $feed->add($item1)->add($item2);

        return $feed;
    }

    protected function getResultMock(): Result
    {
        /** @var Document $document */
        $document = $this->createMock(Document::class);
        /** @var Feed $feed */
        $feed = $this->getFeed();
        /** @var ResponseInterface $response */
        $response = new NullResponse();

        return new Result($document, $feed, new \DateTime('@0'), $response, 'http://localhost/test.rss');
    }
}
