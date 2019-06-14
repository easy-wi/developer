<?php
/**
 * @copyright   2006-2014, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace Decoda\Storage;

use Decoda\Exception\MissingItemException;

/**
 * Cache data using in-memory.
 */
class MemoryStorage extends AbstractStorage {

    /**
     * Internal cache.
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * {@inheritdoc}
     */
    public function get($key) {
        if (!$this->has($key)) {
            throw new MissingItemException(sprintf('Item with key %s does not exist', $key));
        }

        return $this->_cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function has($key) {
        return isset($this->_cache[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        unset($this->_cache[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expires) {
        $this->_cache[$key] = $value;

        return true;
    }

}
