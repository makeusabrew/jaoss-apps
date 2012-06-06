<?php
class ImageResizeControllerTest extends PHPUnitTestController {
    protected $originalSettings;

    public function setUp() {
        $baseDir = realpath(dirname(__FILE__)."/../fixtures");
        $outputDir = $baseDir."/output/";
        $inputDir = $baseDir."/input/";

        $this->originalSettings = $settings = Settings::getSettings();
        $settings["uploads"]["files"]  = $inputDir;
        $settings["images"]["scaled"] = $outputDir;
        Settings::setFromArray($settings);

        $files = array(
            $outputDir."100x100/image.png",
            $outputDir."100/image.png",
        );

        foreach ($files as $file) {
            $dir = dirname($file);

            if (file_exists($file)) {
                unlink($file);
            }

            if (is_dir($dir)) {
                rmdir($dir);
            }
        }

        return parent::setUp();
    }

    public function tearDown() {
        Settings::setFromArray($this->originalSettings);
        return parent::tearDown();
    }

    public function testNonExistentFileReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/100x100/invalid.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Image not found");
    }

    public function testValidFileWithWidthOnlyReturnsImageData() {
        $this->request->dispatch("/uploads/scaled/100/image.png");

        $this->assertResponseCode(200);
        $this->assertHeader("Content-Type", "image/png");
        $this->assertTrue(strlen($this->request->getResponse()->getBody()) > 0);
    }

    public function testValidFileWithWidthAndHeightReturnsImageData() {
        $this->request->dispatch("/uploads/scaled/100x100/image.png");

        $this->assertResponseCode(200);
        $this->assertHeader("Content-Type", "image/png");
        $this->assertTrue(strlen($this->request->getResponse()->getBody()) > 0);
    }

    public function testZeroWidthDimensionReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/0x1/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testZeroWidthOnlyReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/0/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testZeroHeightDimensionReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/1x0/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testZeroBothDimensionReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/0x0/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testTooLargeWidthReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/5000/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testTooLargeHeightReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/10x5000/image.png");
        
        $this->assertResponseCode(404);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Invalid dimensions");
    }

    public function testZeroComputedHeightReturnsCorrectError() {
        $this->request->dispatch("/uploads/scaled/1/image.png");
        
        $this->assertResponseCode(500);
        $this->assertHeader("Content-Type", "text/plain");
        $this->assertBodyHasContents("Resize failed");
    }
}
