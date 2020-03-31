<?php
use PHPUnit\Framework\TestCase;

class DefaultTest extends TestCase
{

  private function assertXML($xmlPath, $expectedExit, $expectedOutput = null, $options = '')
  {
      exec('cat '.$xmlPath .' | php '. __DIR__ .'/../pmd2pr '.$options.' 2>&1', $output, $exit);
      $output = implode("\n", $output);

      $this->assertEquals($expectedExit, $exit, "Invalid exit code returned");
      $this->assertEquals($expectedOutput, $output, "Invalid console output returned");
  }

  public function testFailOnEmpty() {
    $this->assertXML(__DIR__.'/fail/empty.xml', 2, "Error: Expecting xml stream starting with a xml opening tag.\n");
  }

  public function testFailOnInvalid() {
    $this->assertXML(__DIR__.'/fail/invalid.xml', 2, "Error: Start tag expected, '<' not found on line 1, column 1\n\n" .trim(file_get_contents(__DIR__.'/fail/invalid.xml')));
  }

  public function testFileWithMinimalWarnings() {
    $this->assertXML(__DIR__.'/errors/minimal.xml', 1, trim(file_get_contents(__DIR__.'/errors/minimal.expect')));
  }

  public function testFileWithMixedWarnings() {
    $this->assertXML(__DIR__.'/errors/mixed.xml', 1, trim(file_get_contents(__DIR__.'/errors/mixed.expect')));
  }

  public function testFileWithMinimalWarningsExitsGracefully() {
    $this->assertXML(__DIR__.'/errors/minimal.xml', 0, trim(file_get_contents(__DIR__.'/errors/minimal.expect')), '--graceful-warnings');
  }

  public function testFileWithMixedWarningsExitsGracefully() {
    $this->assertXML(__DIR__.'/errors/mixed.xml', 0, trim(file_get_contents(__DIR__.'/errors/mixed.expect')), '--graceful-warnings');
  }

  public function testFileWithNoErrorsAndOnlyHeader() {
    $this->assertXML(__DIR__.'/noerrors/only-header.xml', 0, trim(file_get_contents(__DIR__.'/noerrors/only-header.expect')));
  }
}
