<?php
/**
 * InflectionTest.php
 *
 * - use this for crate correct tests
 * @url http://prirucka.ujc.cas.cz/
 *
 * @author Jan Navratil <jan.navratil@heureka.cz>
 */

require_once __DIR__ . './../src/Inflection.php';

class InflectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @author Jan Navratil <jan.navratil@heureka.cz>
     * @var Inflection
     */
    private $inflection = null;

    protected function setUp()
    {
        parent::setUp();
        $this->inflection = new Inflection();
    }

    public function providerNames()
    {
        return array(
            array(
                "Jan", //name to inflection
                true, //environment ? - životné
                null, //preferred genus
                array( //expected result
                    1 => "Jan",
                    2 => "Jana",
                    3 => "Janovi",
                    4 => "Jana",
                    5 => "Jane",
                    6 => "Janovi",
                    7 => "Janem",
                    8 => "Janové",
                    9 => "Janů",
                    10 => "Janům",
                    11 => "Jany",
                    12 => "Janové",
                    13 => "Janech",
                    14 =>"Jany"
                )
            ),
            array(
                "Josef", //name to inflection
                true, //environment ? - životné
                null, //preferred genus
                array( //expected result
                    1 => "Josef",
                    2 => "Josefa",
                    3 => "Josefovi",
                    4 => "Josefa",
                    5 => "Josefe",
                    6 => "Josefovi",
                    7 => "Josefem",
                    8 => "Josefové",
                    9 => "Josefů",
                    10 => "Josefům",
                    11 => "Josefy",
                    12 => "Josefové",
                    13 => "Josefech",
                    14 => "Josefy"
                )
            ),
            array(
                "Zeus", //name to inflection
                true, //environment ? - životné
                null, //preferred genus
                array( //expected result
                    1 => "Zeus",
                    2 => "Dia",
                    3 => "Diovi",
                    4 => "Dia",
                    5 => "Die",
                    6 => "Diovi",
                    7 => "Diem",
                    8 => "Diové",
                    9 => "Diů",
                    10 => "Diům",
                    11 => null,
                    12 => "Diové",
                    13 => null,
                    14 => null
                )
            ),
            array(
                "Monika", //name to inflection
                true, //environment ? - životné
                null, //preferred genus
                array( //expected result
                    1 => "Monika",
                    2 => "Moniky",
                    3 => "Monice",
                    4 => "Moniku",
                    5 => "Moniko",
                    6 => "Monice",
                    7 => "Monikou",
                    8 => "Moniky",
                    9 => "Monik",
                    10 => "Monikám",
                    11 => "Moniky",
                    12 => "Moniky",
                    13 => "Monikách",
                    14 => "Monikami"
                )
            )

        );
    }

    public function providerSurnames()
    {
        return array(
            array(
                "Nováková", Inflection::GENUS_FEMININE
            ),
            array(
                "Novák", null
            ),
            array(
                "Brožová", Inflection::GENUS_FEMININE
            ),
            array(
                "Záhlavová-Strýcová", Inflection::GENUS_FEMININE
            )
        );
    }

    /**
     * @dataProvider providerNames
     */
    public function testInflectionNames($name, $env, $preferredGenus, $expected)
    {
        $inflected = $this->inflection->inflect($name, $env, $preferredGenus);
        $this->assertSame($expected, $inflected);
    }

    /**
     * @dataProvider providerSurnames
     */
    public function testIsFeminineGenusSurname($surname, $expected)
    {
        $this->assertSame($this->inflection->isFeminineGenusSurname($surname), $expected);
    }
}