<?php
/**
 * @copyright   2006-2014, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace Decoda\Storage;

use Decoda\Exception\MissingItemException;
use \Redis;

/**
 * Cache data using Redis.
 */
class RedisStorage extends AbstractStorage {

    /**
     * The third-party class instance.
     *
     * @var \Redis
     */
    protected $_redis;

    /**
     * Set the Redis instance.
     *
     * @param \Redis $redis
     */
    public function __construct(Redis $redis) {
        $this->_redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        $value = $this->getRedis()->get($key);

        if ($value === false) {
            throw new MissingItemException(sprintf('Item with key %s does not exist', $key));
        }

        return $value;
    }

    /**
     * Return the Redis instance.
     *
     * @return \Redis
     */
    public function getRedis() {
        return $this->_redis;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        return $this->getRedis()->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        return (bool) $this->getRedis()->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expires) {
        return $this->getRedis()->setex($key, (int) $expires - time(), $value); // Redis is TTL
    }

}
