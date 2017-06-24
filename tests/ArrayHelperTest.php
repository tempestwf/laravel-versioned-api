<?php

use App\API\V1\Entities\User;
use TempestTools\Common\Helper\ArrayHelper;

class ArrayHelperTest extends TestCase
{

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
               'closure'=>function (ArrayHelper $arrayHelper) {
                    return ($arrayHelper instanceof ArrayHelper);
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
