<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1ab8129f25e80a01c2f4db0939337682
{
    public static $files = array (
        'fa3df3013f51e030ec6f48c5e17462d5' => __DIR__ . '/..' . '/lindelius/php-jwt/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Lindelius\\JWT\\' => 14,
        ),
        'I' => 
        array (
            'Inc\\' => 4,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'Ahc\\Jwt\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Lindelius\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/lindelius/php-jwt/src',
        ),
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Ahc\\Jwt\\' => 
        array (
            0 => __DIR__ . '/..' . '/adhocore/jwt/src',
        ),
    );

    public static $classMap = array (
        'Inc\\Base\\Activate' => __DIR__ . '/../..' . '/inc/Base/activate.php',
        'Inc\\Base\\Deactivate' => __DIR__ . '/../..' . '/inc/Base/deactivate.php',
        'Inc\\Base\\DomitaiApi' => __DIR__ . '/../..' . '/inc/Base/DomitaiApi.php',
        'Inc\\Init' => __DIR__ . '/../..' . '/inc/init.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1ab8129f25e80a01c2f4db0939337682::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1ab8129f25e80a01c2f4db0939337682::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1ab8129f25e80a01c2f4db0939337682::$classMap;

        }, null, ClassLoader::class);
    }
}