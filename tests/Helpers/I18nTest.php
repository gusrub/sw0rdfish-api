<?php

namespace Test\Helpers;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Helpers\I18n;

/**
 * Contains tests for the Sw0rdfish\Helpers\I18n helper.
 */
class I18nTest extends TestCase
{

    /**
     * Test that a string is properly translated.
     *
     * @return void
     * @test
     */
    function translateStringByHeader()
    {
        $_SERVER['HTTP_X_LANGUAGE'] = 'es_MX.utf8';
        $translation = I18n::translate('TEST: this string should be translated!');
        $spanish = 'PRUEBA: esta cadena de texto debería estar traducida!';
        $this->assertEquals($translation, $spanish);
    }

    /**
     * Test that a string is properly translated by overriding language.
     *
     * @return void
     * @test
     */
    function translateStringByOverriding()
    {
        $translation = I18n::translate(
            'TEST: this string should be translated!',
            null,
            'es_MX.utf8'
        );
        $spanish = 'PRUEBA: esta cadena de texto debería estar traducida!';
        $this->assertEquals($translation, $spanish);
    }

    /**
     * Test that a string is properly translated with arguments.
     *
     * @return void
     * @test
     */
    function translateStringWithArgs()
    {
        $translation = I18n::translate(
            'TEST: Hello {name}, you are {years} years old!',
            [
                'name' => 'Gus',
                'years' => '33'
            ],
            'es_MX.utf8'
        );
        $spanish = 'PRUEBA: Hola Gus, tienes 33 años!';
        $this->assertEquals($translation, $spanish);
    }
}
