<?php

namespace Nutrition\Test\Utils;

use MyTestCase;
use Nutrition\Utils\Inflector;

class InflectorTest extends MyTestCase
{
    /**
     * @dataProvider pluralProvider
     */
    public function testPluralize($source, $expected)
    {
        $this->assertEquals($expected, Inflector::pluralize($source));
    }

    /**
     * @dataProvider pluralProvider
     */
    public function testSingularize($expected, $source)
    {
        $this->assertEquals($expected, Inflector::singularize($source));
    }

    /**
     * @dataProvider pluralProvider
     */
    public function testCountable($singular, $plural)
    {
        $this->assertEquals("1 $singular", Inflector::countable(1, $singular));
        $this->assertEquals("2 $plural", Inflector::countable(2, $singular));
    }

    public function pluralProvider()
    {
        return [
            ['boat' , 'boats'],
            ['house' , 'houses'],
            ['cat' , 'cats'],
            ['river' , 'rivers'],
            ['bus', 'buses'],
            ['wish', 'wishes'],
            ['pitch', 'pitches'],
            ['box', 'boxes'],
            ['penny', 'pennies'],
            ['spy', 'spies'],
            ['baby', 'babies'],
            ['city', 'cities'],
            ['daisy', 'daisies'],
            ['woman', 'women'],
            ['man', 'men'],
            ['child', 'children'],
            ['tooth', 'teeth'],
            ['foot', 'feet'],
            ['person', 'people'],
            ['leaf', 'leaves'],
            ['mouse', 'mice'],
            ['goose', 'geese'],
            ['half', 'halves'],
            ['knife', 'knives'],
            ['wife', 'wives'],
            ['life', 'lives'],
            ['elf', 'elves'],
            ['loaf', 'loaves'],
            ['potato', 'potatoes'],
            ['tomato', 'tomatoes'],
            ['cactus', 'cacti'],
            ['focus', 'foci'],
            ['fungus', 'fungi'],
            ['nucleus', 'nuclei'],
            ['syllabus', 'syllabuses'],
            ['syllabus', 'syllabuses'],
            ['analysis', 'analyses'],
            ['diagnosis', 'diagnoses'],
            ['oasis', 'oases'],
            ['thesis', 'theses'],
            ['crisis', 'crises'],
            ['phenomenon', 'phenomena'],
            ['criterion', 'criteria'],
            ['datum', 'data'],
            ['sheep', 'sheep'],
            ['fish', 'fish'],
            ['deer', 'deer'],
            ['species', 'species'],
            ['aircraft', 'aircraft'],
        ];
    }
}
