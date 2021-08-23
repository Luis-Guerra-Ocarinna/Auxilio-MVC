<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd45cfd4a1d44d60eb27d9578c64f9f86
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WilliamCosta\\DotEnv\\' => 20,
            'WilliamCosta\\DatabaseManager\\' => 29,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WilliamCosta\\DotEnv\\' => 
        array (
            0 => __DIR__ . '/..' . '/william-costa/dot-env/src',
        ),
        'WilliamCosta\\DatabaseManager\\' => 
        array (
            0 => __DIR__ . '/..' . '/william-costa/database-manager/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd45cfd4a1d44d60eb27d9578c64f9f86::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd45cfd4a1d44d60eb27d9578c64f9f86::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd45cfd4a1d44d60eb27d9578c64f9f86::$classMap;

        }, null, ClassLoader::class);
    }
}
