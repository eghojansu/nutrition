<?php

namespace Nutrition\Console;

class StringParser
{
    /** @var array console color map */
    protected static $colorMaps = [
        'danger' => '0;31',
        'info' => '0;32',
        'warning' => '1;33',
    ];

    /** @var string */
    private $pattern;

    /** @var string */
    private $originalString;

    /** @var array parsed string */
    private $parsed;


    /**
     * Class constructor, normalize string line
     * @param string $str
     */
    public function __construct($str)
    {
        $this->originalString = str_replace("\r\n", PHP_EOL, $str);
        $this->pattern = '/(?<prefix>[^<]*)?<(?<color>'.
            implode('|', array_keys(static::$colorMaps)).
            ')>(?<str>[^<]+)<\/(?:\\1)?>(?<suffix>[^<]*)?/';
    }

    /**
     * String parser
     * @param  string $str
     * @return static
     */
    public static function create($str)
    {
        return new static($str);
    }

    /**
     * Parsed to string
     * @return string
     */
    public function __toString()
    {
        if (null == $this->parsed) {
            $this->parse();
        }

        $newStr = '';
        foreach ($this->parsed as $part) {
            $newStr .= $part['colored'] . ($part['newline'] ? PHP_EOL : '');
        }

        return $newStr;
    }

    /**
     * Parse line as colored text
     * @param  string $str
     * @return array
     */
    public function parse()
    {
        if (preg_match_all($this->pattern, $this->originalString, $matches, PREG_SET_ORDER)) {
            $this->parsed = [];
            foreach ($matches as $match) {
                $code = explode(';', static::$colorMaps[$match['color']]);
                $parsed = [];
                if ($match['prefix']) {
                    $parsed = array_merge($parsed, $this->parseLine($match['prefix']));
                }
                $parsed = array_merge($parsed, $this->parseLine($match['str'], $code[1]));
                if ($match['suffix']) {
                    $parsed = array_merge($parsed, $this->parseLine($match['suffix']));
                }
                /*
                $this->parsed = array_merge(
                    $this->parsed,
                    // $this->parseLine($match['prefix']),
                    $this->parseLine($match['str'], $code[1])
                    // $this->parseLine($match['suffix'])
                );
                */
               $this->parsed = array_merge($this->parsed, $parsed);
            }
        } else {
            $this->parsed = $this->parseLine($this->originalString, null);
        }

        return $this;
    }

    /**
     * Get parsed string
     * @return array
     */
    public function getParsed()
    {
        if (null === $this->parsed) {
            $this->parse();
        }

        return $this->parsed;
    }

    /**
     * Parse new line
     * @param  strng $str
     * @param  string $color
     * @return array
     */
    private function parseLine($str, $color = null)
    {
        $parsed = [];
        $lines = explode(PHP_EOL, $str);
        $last = count($lines) - 1;
        foreach ($lines as $key => $line) {
            $parsed[] = [
                'colored' => $color ? sprintf("\033[%sm%s\033[0m", $color, $line) : $line,
                'original' => $line,
                'newline' => $key < $last
            ];
        }

        return $parsed;
    }
}
