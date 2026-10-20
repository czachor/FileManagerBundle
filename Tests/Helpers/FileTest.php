<?php

namespace Artgris\Bundle\FileManagerBundle\Tests\Helpers;

use Artgris\Bundle\FileManagerBundle\Helpers\File;
use Artgris\Bundle\FileManagerBundle\Helpers\FileManager;
use Artgris\Bundle\FileManagerBundle\Service\FileTypeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/artgris-file-test-'.uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir.'/*'));
        rmdir($this->dir);
    }

    public function testDimensionOfImage()
    {
        // 1x1 transparent PNG
        $file = $this->createFile('pixel.PNG', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));

        $this->assertSame([1, 1], \array_slice($file->getDimension(), 0, 2));
    }

    public function testDimensionOfCorruptImageIsFalse()
    {
        $file = $this->createFile('broken.png', 'not an image');

        $this->assertFalse($file->getDimension());
    }

    public function testDimensionOfNonImageIsEmpty()
    {
        $file = $this->createFile('text.txt', 'text');

        $this->assertSame('', $file->getDimension());
    }

    private function createFile(string $name, string $content): File
    {
        file_put_contents($this->dir.'/'.$name, $content);

        return new File(
            new SplFileInfo($this->dir.'/'.$name, '', $name),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(FileTypeService::class),
            $this->createMock(FileManager::class),
        );
    }
}
