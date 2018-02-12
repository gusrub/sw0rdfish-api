<?php

namespace Sw0rdfish\Models;

trait SecurityHelpersTrait {

    protected function generateSecureToken($size)
    {
        return hash('sha256', bin2hex(random_bytes($size)));
    }

}
