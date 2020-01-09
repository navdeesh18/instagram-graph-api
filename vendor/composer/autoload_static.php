<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit66fa82d580c8933034faecb19b17c3f5
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'Nkcx\\InstagramGraphApi\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Nkcx\\InstagramGraphApi\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit66fa82d580c8933034faecb19b17c3f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit66fa82d580c8933034faecb19b17c3f5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}