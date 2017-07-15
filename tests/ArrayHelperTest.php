<?php

use App\API\V1\Entities\User;
use TempestTools\Common\Helper\ArrayHelper;

class ArrayHelperTest extends TestCase
{

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testEnforceValues()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->testEnforceValues([['foo'=>'bar']], [['foo'=>'bar']]);
        $this->assertTrue($result);
        $result = $arrayHelper->testEnforceValues([['foo'=>'bar']], [['foo'=>'baz']]);
        $this->assertFalse($result);

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testWrapArrayAndNumeric()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->wrapArray(['foo'=>'bar']);
        $this->assertTrue($arrayHelper->isNumeric($result));

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testFindSetting()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->findSetting([['foo'=>'bar'], NULL], 'foo');
        $this->assertSame($result, 'bar');

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testAutoParse()
    {
        $testArray = $this->getTestArray();
        $arrayHelper = new ArrayHelper($testArray);
        $result1 = $arrayHelper->parse($testArray['base']['goGetIt']);
        $result2 = $arrayHelper->parse($testArray['base']['templateIt']);
        $result3 = $arrayHelper->parse($testArray['base']['closure']);
        $result4 = $arrayHelper->parse($testArray['three']);

        $this->assertSame($result1, 'foo');
        $this->assertSame($result2, 'im mary poppins yall!');
        $this->assertTrue($result3);
        $this->assertSame($result4['goGetIt'], '?:one:retrieve');
        $this->assertSame($result4['retrieve'], 'foo');
        $this->assertSame($result4['extended'], [':two', ':one', ':base', ':four']);
    }

    /**
     * @group arrayHelper
     * Test extraction works
     */
    public function testExtraction() {
        $arrayHelper = new ArrayHelper();
        $user = new User();
        $user->setEmail('bobs@youruncle.com');
        $arrayHelper->extract([$user]);
        $array = $arrayHelper->getArray();

        $this->assertSame($array['userEntity']['email'], 'bobs@youruncle.com');
    }

    /**
     * Get a test array object to work with
     * @return ArrayObject
     */
    protected function getTestArray():ArrayObject {
        return new ArrayObject([
           'base'=> [
               'extends'=>[],
               'closure'=>function ($extra, ArrayHelper $arrayHelper) {
                    return ($arrayHelper instanceof ArrayHelper && is_array($extra));
               },
               'goGetIt'=>'?:one:retrieve',
               'templateIt'=>'?im {{:one:retrieve2}} {{:one:retrieve3}}!'
           ],
           'one'=> [
               'extends'=>[':base'],
               'retrieve'=>'foo',
               'retrieve2'=>'mary poppins',
               'retrieve3'=>'yall'
           ],
           'two'=> [
               'extends'=>[':one']
           ],
           'three'=> [
               'extends'=>[':two', ':four']
           ],
           'four'=> [
               'extends'=>[]
           ]
         ]);
    }
}
