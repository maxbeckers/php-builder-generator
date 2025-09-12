<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\TemplateEngine;
use PHPUnit\Framework\TestCase;

class TemplateEngineTest extends TestCase
{
    private TemplateEngine $templateEngine;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/php-builder-test-' . uniqid();
        mkdir($this->tempDir, 0777, true);

        $this->templateEngine = new TemplateEngine($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            unlink($this->tempDir . '/test.twig');
            rmdir($this->tempDir);
        }
    }

    public function testRenderTemplate(): void
    {
        file_put_contents($this->tempDir . '/test.twig', 'Hello {{ name }}!');

        $result = $this->templateEngine->render('test.twig', ['name' => 'World']);

        $this->assertEquals('Hello World!', $result);
    }

    public function testCamelCaseFunction(): void
    {
        file_put_contents($this->tempDir . '/test.twig', '{{ camel_case(text) }}');

        $result = $this->templateEngine->render('test.twig', ['text' => 'hello_world']);

        $this->assertEquals('helloWorld', $result);
    }

    public function testPhpExportFunction(): void
    {
        file_put_contents($this->tempDir . '/test.twig', '{{ php_export(value) }}');

        $tests = [
            [null, 'null'],
            [true, 'true'],
            [false, 'false'],
            ['test', "'test'"],
            [42, '42'],
            [[], '[]'],
            [['a', 'b'], "['a', 'b']"]
        ];

        foreach ($tests as [$input, $expected]) {
            $result = $this->templateEngine->render('test.twig', ['value' => $input]);
            $this->assertEquals($expected, $result);
        }
    }
}
