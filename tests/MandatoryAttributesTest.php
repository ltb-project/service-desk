<?php
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../lib/attributes.inc.php");

class MandatoryAttributesTest extends TestCase
{
    private function getAttributesMap()
    {
        return array(
            'firstname' => array('attribute' => 'givenname', 'mandatory' => array('all')),
            'lastname' => array('attribute' => 'sn', 'mandatory' => array('create')),
            'fullname' => array('attribute' => 'cn', 'mandatory' => array('update')),
            'title' => array('attribute' => 'title'),
        );
    }

    public function testCreateRequiresMandatoryValues()
    {
        $missing = find_missing_mandatory_attributes(
            'create',
            $this->getAttributesMap(),
            array(
                'givenname' => array('Alice'),
                'cn' => 'Alice Doe',
            )
        );

        $this->assertSame(array('lastname'), $missing);
    }

    public function testCreateAcceptsMandatoryMacroValue()
    {
        $missing = find_missing_mandatory_attributes(
            'create',
            $this->getAttributesMap(),
            array(
                'givenname' => array('Alice'),
                'sn' => 'Doe',
            )
        );

        $this->assertSame(array(), $missing);
    }

    public function testUpdateChecksOnlyProposedMandatoryAttributes()
    {
        $missing = find_missing_mandatory_attributes(
            'update',
            $this->getAttributesMap(),
            array(
                'givenname' => array(),
                'cn' => array(),
            ),
            array('fullname')
        );

        $this->assertSame(array('fullname'), $missing);
    }

    public function testUpdateIgnoresMandatoryAttributesNotProposedForChange()
    {
        $missing = find_missing_mandatory_attributes(
            'update',
            $this->getAttributesMap(),
            array(
                'givenname' => array(),
                'cn' => array(),
            ),
            array('title')
        );

        $this->assertSame(array(), $missing);
    }
}
