<?php

class LocalizationTest extends TestCase
{
    public function testLocalization():void
    {
        App::setLocale('en');
        $testTranslate = trans('auth.test_entry');
        $this->assertEquals($testTranslate, 'These is a test entry.');

        $testTranslate = trans('this_has_no_translation');
        $this->assertEquals($testTranslate, 'this_has_no_translation');

        App::setLocale('pt');
        $testTranslate = trans('auth.test_entry');
        $this->assertEquals($testTranslate, 'Esta é uma entrada de teste.');

        $testTranslate = trans('this_has_no_translation');
        $this->assertEquals($testTranslate, 'this_has_no_translation');
    }
}