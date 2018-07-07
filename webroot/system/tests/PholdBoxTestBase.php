<?php
include_once('../PholdBoxBaseObj.php');
include_once('../PhORM.php');
include_once('../PholdBoxSessionManager.php');
include_once('../Event.php');
class PholdBoxTestBase extends PHPUnit_Framework_TestCase
{
    /**
     * calls protected method of a class for testing.
     *
     * @param [type] $obj
     * @param [type] $name
     * @param array $args
     * @return void
     */
    public static function callPrivateMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}