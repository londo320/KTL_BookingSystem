<?php

namespace App\Foundation;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Get the path to the PHP binary.
     *
     * Overridden to use wrapper script instead of PHP_BINARY
     * to avoid issues with spaces in the path.
     *
     * @return string
     */
    public function phpBinary()
    {
        $wrapperPath = $this->basePath('php-wrapper');

        if (file_exists($wrapperPath)) {
            return $wrapperPath;
        }

        return parent::phpBinary();
    }
}
