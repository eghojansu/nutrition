<?php

namespace Nutrition\Utils;

use Base;
use Prefab;

/**
 * Flash message utility
 */
class FlashMessage extends Prefab
{
    const SESSION_NAME = 'SESSION.FLASH';

    /**
     * Add message to flash message
     * @param string $key
     * @param mixed $message
     */
    public function add($key, $message)
    {
        $fullKey = self::SESSION_NAME.'.'.$key;
        $base = Base::instance();
        if ($base->exists($fullKey)) {
            $base->push($fullKey, $message);
        } else {
            $base->set($fullKey, [$message]);
        }

        return $this;
    }

    /**
     * Get message from flash message
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        $fullKey = self::SESSION_NAME.'.'.$key;
        $base = Base::instance();
        $message = $base->get($fullKey);
        $base->clear($fullKey);

        return $message;
    }

    /**
     * Get all message
     * @return array
     */
    public function all()
    {
        $base = Base::instance();
        $messages = $base->get(self::SESSION_NAME);
        $base->clear(self::SESSION_NAME);

        return $messages;
    }
}
