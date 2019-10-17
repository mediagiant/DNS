<?php

declare(strict_types=1);

/*
 * This file is part of Badcow DNS Library.
 *
 * (c) Samuel Williams <sam@badcow.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badcow\DNS\Rdata;

/**
 * {@link https://tools.ietf.org/html/rfc2535#section-3.1}.
 */
class KEY implements RdataInterface
{
    use RdataTrait;

    const TYPE = 'KEY';
    const TYPE_CODE = 25;
    
    /**
     * {@link https://tools.ietf.org/html/rfc4034#section-2.1.1}.
     *
     * @var int
     */
    protected $flags;
    
    /**
     * The Protocol Field MUST have value 3, and the DNSKEY RR MUST be
     * treated as invalid during signature verification if it is found to be
     * some value other than 3.
     * {@link https://tools.ietf.org/html/rfc4034#section-2.1.2}.
     *
     * @var int
     */
    protected $protocol = 3;
    
    /**
     * The Algorithm field identifies the public key's cryptographic
     * algorithm and determines the format of the Public Key field.
     * {@link https://tools.ietf.org/html/rfc4034#section-2.1.3}.
     *
     * @var int
     */
    protected $algorithm;
    
    /**
     * The Public Key field is a Base64 encoding of the Public Key.
     * Whitespace is allowed within the Base64 text.
     * {@link https://tools.ietf.org/html/rfc4034#section-2.1.4}.
     *
     * @var string
     */
    protected $publicKey;
    
    /**
     * @param int $flags
     */
    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    /**
     * @param int $protocol
     */
    public function setProtocol(int $protocol): void
    {
        $this->protocol = $protocol;
    }

    /**
     * @param int $algorithm
     */
    public function setAlgorithm(int $algorithm): void
    {
        $this->algorithm = $algorithm;
    }
    
    /**
     * @param string $publicKey
     */
    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }
    
    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return int
     */
    public function getProtocol(): int
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getAlgorithm(): int
    {
        return $this->algorithm;
    }
    
    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * {@inheritdoc}
     */
    public function toText(): string
    {
        return sprintf('%d %d %d %s', $this->flags, $this->protocol, $this->algorithm, $this->publicKey);
    }

    /**
     * {@inheritdoc}
     */
    public function toWire(): string
    {
        $encoded = pack('nCC', $this->flags, $this->protocol, $this->algorithm);
        $encoded .= $this->publicKey;
        
        return $encoded;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromText(string $text): RdataInterface
    {
        $rdata = explode(Tokens::SPACE, $text);
        $key = new static();
        $key->setFlags((int) array_shift($rdata));
        $key->setProtocol((int) array_shift($rdata));
        $key->setAlgorithm((int) array_shift($rdata));
        $key->setPublicKey(implode('', $rdata));
        
        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromWire(string $rdata): RdataInterface
    {
        $integers = unpack('nflags/Cprotocol/Calgorithm', $rdata);
        $key = new static();
        $key->setFlags((int) $integers['flags']);
        $key->setProtocol((int) $integers['protocol']);
        $key->setAlgorithm((int) $integers['algorithm']);
        $key->setPublicKey(substr($rdata, 4));
        
        return $key;
    }
}
