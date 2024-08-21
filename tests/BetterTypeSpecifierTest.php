<?php

use PHPStan\Testing\TypeInferenceTestCase;

class BetterTypeSpecifierTest extends TypeInferenceTestCase
{
  public static function getAdditionalConfigFiles(): array
  {
    return [
      __DIR__ . '/data/BetterTypeSpecifier.neon',
    ];
  }

  private function _gatherAssertCases($file): array
  {
    $cases = $this->gatherAssertTypes($file);
    $data = array();
    foreach ($cases as $key => $case) {
      $key = preg_replace('/.*:(\d+)$/', '\\1', $key);
      $data[$key] = $case;
    }
    return $data;
  }

  /**
   * ...
   */
  public function dataCase1(): array
  {
    return $this->_gatherAssertCases(
        __DIR__ . "/data/BetterTypeSpecifier.case1.php");
  }

  /**
   * ...
   *
   * @dataProvider dataCase1
   */
  public function testCase1($type, $file, ...$args): void
  {
    $this->assertFileAsserts($type, $file, ...$args);
  }
}
