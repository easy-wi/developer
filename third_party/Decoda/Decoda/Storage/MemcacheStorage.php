<?php
/**
 * @copyright   2006-2014, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace Decoda\Storage;

use Decoda\Exception\MissingItemException;
use \Memcached;

/**
 * Cache data using Memcache.
 */
class MemcacheStorage extends AbstractStorage {

    /**
     * The third-party class instance.
     *
     * @var \Memcached
     */
    protected $_memcache;

    /**
     * Set the Memcached instance.
     *
     * @param \Memcached $memcache
     */
    public function __construct(Memcached $memcache) {
        $this->_memcache = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        $value = $this->getMemcache()->get($key);

        if ($value === false && $this->getMemcache()->getResultCode() === Memcached::RES_NOTFOUND) {
            throw new MissingItemException(sprintf('Item with key %s does not exist', $key));
        }

        return $value;
    }

    /**
     * Return the Memcached instance.
     *
     * @return \Memcached
     */
    public function getMemcache() {
        return $this->_memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        return (
            $this->getMemcache()->get($key) &&
            $this->getMemcache()->getResultCode() === Memcached::RES_SUCCESS
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        return $this->getMemcache()->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expires) {
        return $this->getMemcache()->set($key, $value, (int) $expires);
    }

}
