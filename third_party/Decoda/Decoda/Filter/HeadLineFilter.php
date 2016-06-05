<?php
/**
 * @copyright   2006-2014, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\AbstractFilter;

class HeadLineFilter extends AbstractFilter {
    protected $_tags = array(
        'h1' => array(
            'htmlTag' => 'h3',
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_NONE
        )
    );
}