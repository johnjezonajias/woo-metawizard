<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc9aa894b77ea49be707f8b87dc6f4ecf
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitc9aa894b77ea49be707f8b87dc6f4ecf', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc9aa894b77ea49be707f8b87dc6f4ecf', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitc9aa894b77ea49be707f8b87dc6f4ecf::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}