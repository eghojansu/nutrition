<?php

namespace Nutrition\Utils;

class GroupChecker
{
	/** @var array */
	private $groups;

	/** @var string */
	private $group;


	public function __construct(array $groups, string $group = null)
	{
		$this->groups = $groups;
		$this->group = $group ?? reset($groups);
	}

	/**
	 * Get current group
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * Get current groups
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups;
	}

    /**
     * Check if current group is invalid
     * @return boolean
     */
    public function isInvalid()
    {
        return !$this->isValid();
    }

    /**
     * Check if current group is valid
     * @return boolean
     */
    public function isValid()
    {
        return in_array($this->group, $this->groups);
    }

    /**
     * Check if current group is equal
     * @return boolean
     */
    public function isEqual($group)
    {
        return $group === $this->group;
    }

    /**
     * Check if current group is first group
     * @return boolean
     */
    public function isFirst()
    {
        return $this->group === reset($this->groups);
    }

    /**
     * Check if current group is last group
     * @return boolean
     */
    public function isLast()
    {
        return $this->group === end($this->groups);
    }

    /**
     * Get next group
     * @return string
     */
    public function getNext()
    {
        reset($this->groups);
        while ($key = key($this->groups)) {
            $nextGroup = next($this->groups);

            if ($this->groups[$key] === $this->group && $nextGroup) {
                return $nextGroup;
            }
        }

        return $this->group;
    }

    /**
     * Get prev group
     * @return string
     */
    public function getPrev()
    {
        end($this->groups);
        while ($key = key($this->groups)) {
            $prevGroup = prev($this->groups);

            if ($this->groups[$key] === $this->group && $prevGroup) {
                return $prevGroup;
            }
        }

        return $this->group;
    }
}
