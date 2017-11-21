<?php

namespace Nutrition\Utils;

class Pagination
{
    /** @var array */
    private $properties = [
        // total record in tables
        'allRecordCount' => 0,
        // current page record count
        'recordCount' => 0,
        // current page
        'currentPage' => 0,
        // last page number
        'lastPage' => 0,
        // first page number
        'firstPage' => 0,
        // first number of record
        'first' => 0,
        // last number of record
        'last' => 0,
        // record counter
        'counter' => 0,
        // is on first page
        'onFirstPage' => false,
        // is on last page
        'onLastPage' => false,
        // has previous gap
        'prevGap' => false,
        // has next gap
        'nextGap' => false,
        // previous page
        'prevPage' => 0,
        // next page
        'nextPage' => 0,
        // has content
        'hasContent' => false,
        // range start
        'rangeStart' => 0,
        // range end
        'rangeEnd' => 0,
        // data subset
        'subset' => [],
    ];


    /**
     * Construct pagination
     * @param array $subset result from DB\SQL\Mapper::paginate
     */
    public function __construct(array $subset)
    {
        $this->properties['allRecordCount'] = $subset['total'];
        $limit  = $subset['limit'];
        $offset = $subset['pos'];
        $recordCount = count($subset['subset']);
        $this->properties['recordCount']  = $recordCount;
        $this->properties['currentPage'] = ($offset ? $offset / $limit : 0) + 1;
        $this->properties['lastPage'] = $subset['count'];
        $this->properties['firstPage'] = 1;

        $this->properties['onFirstPage'] = $this->properties['currentPage'] <= 1;
        $this->properties['onLastPage'] = $this->properties['currentPage'] >= $this->properties['lastPage'];
        $this->properties['prevPage'] = $this->properties['currentPage'] - 1 < 1 ? 1 : $this->properties['currentPage'] - 1;
        $this->properties['nextPage'] = $this->properties['currentPage'] + 1 <= $this->properties['lastPage'] ?
                $this->properties['currentPage'] + 1 : $this->properties['currentPage'];

        $adjacent = PaginationSetup::instance()->getAdjacent();
        $this->properties['rangeStart'] = $this->properties['currentPage'] <= $adjacent ? 1 :
            $this->properties['currentPage'] - $adjacent;
        $this->properties['rangeEnd'] = $this->properties['currentPage'] > $this->properties['lastPage'] - $adjacent ?
            $this->properties['lastPage'] : $this->properties['currentPage'] + $adjacent;
        $this->properties['first'] = $recordCount ? $$offset + 1 : 0;
        $this->properties['last'] = $offset + $recordCount;
        $this->properties['counter'] = $this->properties['first'];

        $this->properties['prevGap'] = $this->rangeStart > 1;
        $this->properties['nextGap'] = $this->rangeEnd < $this->properties['lastPage'];

        $this->properties['hasContent'] = $this->properties['rangeStart'] < $this->properties['rangeEnd'];

        $this->properties['subset'] = $subset['subset'];
    }

    /**
     * Row counter
     * @return int
     */
    public function index()
    {
        return $this->properties['counter']++;
    }

    /**
     * Get range
     * @return array
     */
    public function getRange()
    {
        return $this->properties['hasContent'] ?
            range($this->properties['rangeStart'], $this->properties['rangeEnd']) :
            [] ;
    }

    /**
     * Check if pagination is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->properties['subset']) === 0;
    }

    /**
     * Proxy to PaginationSetup::path
     * @return string
     */
    public function path()
    {
        return call_user_func_array([PaginationSetup::instance(), 'path'], func_get_args());
    }

    public function __get($name)
    {
        return array_key_exists($name, $this->properties) ? $this->properties[$name] : null;
    }
}
