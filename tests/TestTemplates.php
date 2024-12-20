<?php

declare(strict_types=1);

namespace Oct8pus\Tests;

use Latte\Engine;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TestTemplates
{
    private static bool $useCache = false;

    private static function viewsDir() : string
    {
        return __DIR__ . '/../views';
    }

    private static function params() : array
    {
        return [
            'title' => 'test',
            'name' => 'world',
            'favicon' => 'favicon.ico',
            'list' => [
                'first',
                'second',
                'third',
            ],
        ];
    }

    public static function testTwig() : void
    {
        $namespaces = [
            '__main__' => '',
        ];

        $loader = new FilesystemLoader($namespaces, self::viewsDir());

        $environment = new Environment($loader, [
            //'auto_reload' => true,
            'cache' => self::$useCache ? sys_get_temp_dir() . '/twig' : false,
            'debug' => false,
            //'strict_variables' => true,
        ]);

        $output = $environment->render('Index.twig', self::params());
        //file_put_contents('twig.html', $output);
    }

    public static function testLatte() : void
    {
        $latte = new Engine();

        if (self::$useCache) {
            $latte->setTempDirectory(sys_get_temp_dir() . '/latte');
        }

        // even when the cache directory is not set, there is still class caching
        $output = $latte->renderToString(self::viewsDir() . '/Index.latte', self::params());
        //file_put_contents('latte.html', $output);
    }
}
