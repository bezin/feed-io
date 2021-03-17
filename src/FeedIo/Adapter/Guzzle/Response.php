<?php declare(strict_types=1);
/*
 * This file is part of the feed-io package.
 *
 * (c) Alexandre Debril <alex.debril@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FeedIo\Adapter\Guzzle;

use DateTime;
use FeedIo\Adapter\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Guzzle dependent HTTP Response
 */
class Response implements ResponseInterface
{
    const HTTP_LAST_MODIFIED = 'Last-Modified';

    protected ?string $body = null;

    public function __construct(
        protected PsrResponseInterface $psrResponse,
        protected int $duration
    ) {
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getStatusCode(): int
    {
        return (int) $this->psrResponse->getStatusCode();
    }

    public function isModified() : bool
    {
        return $this->psrResponse->getStatusCode() != 304 && strlen($this->getBody()) > 0;
    }

    public function getBody() : ? string
    {
        if (is_null($this->body)) {
            $this->body = $this->psrResponse->getBody()->getContents();
        }

        return $this->body;
    }

    public function getLastModified() : ?DateTime
    {
        if ($this->psrResponse->hasHeader(static::HTTP_LAST_MODIFIED)) {
            $lastModified = DateTime::createFromFormat(DateTime::RFC2822, $this->getHeader(static::HTTP_LAST_MODIFIED)[0]);

            return false === $lastModified ? null : $lastModified;
        }

        return null;
    }

    public function getHeaders()  : iterable
    {
        return $this->psrResponse->getHeaders();
    }

    public function getHeader(string $name) : iterable
    {
        return $this->psrResponse->getHeader($name);
    }
}
