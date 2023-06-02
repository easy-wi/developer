<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit19680f709907ae8d9baf4303d2f59e41
{
    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Bramus' => 
            array (
                0 => __DIR__ . '/..' . '/bramus/router/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit19680f709907ae8d9baf4303d2f59e41::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit19680f709907ae8d9baf4303d2f59e41::$classMap;

        }, null, ClassLoader::class);
    }
}
