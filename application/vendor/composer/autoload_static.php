<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitace917fdfafef1b7fa14b07b4b7a4887
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Picqer\\Barcode\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Picqer\\Barcode\\' => 
        array (
            0 => __DIR__ . '/..' . '/picqer/php-barcode-generator/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitace917fdfafef1b7fa14b07b4b7a4887::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitace917fdfafef1b7fa14b07b4b7a4887::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
