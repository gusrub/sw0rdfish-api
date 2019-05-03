<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

/**
 * Contains generic security functions.
 */
trait SecurityHelpersTrait {

    /**
     * Generated a randomized, secure token with the given size in bytes.
     *
     * @param int $size The bits or size of the random bytes to generate.
     * @return String A hashed secure token.
     */
    protected function generateSecureToken($size)
    {
        return hash('sha256', bin2hex(random_bytes($size)));
    }

    /**
     * Generates a security code used for reset tokens with the given bits size.
     *
     * @param int $size The bits or size of the random bytes to generate.
     * @return String a base64 encoded security code.
     */
    protected function generateSecurityCode($size)
    {
        return base64_encode(random_bytes($size));
    }

}
