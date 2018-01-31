<?php

/**
 * PHPUnit Result Parsing utility
 *
 * For an example on how to integrate with your test engine, see
 * @{class:PhpunitTestEngine}.
 */
final class AvtPhpunitTestResultParser extends ArcanistTestResultParser {

  /**
   * Parse test results from phpunit junit report
   *
   * @param string $path Path to test
   * @param string $test_results String containing phpunit junit report
   *
   * @return array
   */
  public function parseTestResults($path, $test_results) {
    if (!$test_results) {
      $result = id(new ArcanistUnitTestResult())
        ->setName($path)
        ->setUserData($this->stderr)
        ->setResult(ArcanistUnitTestResult::RESULT_BROKEN);
      return array($result);
    }

    $report = $this->getJunitReport($test_results);

    // coverage is for all testcases in the executed $path
    $coverage = array();
    if ($this->enableCoverage !== false) {
      $coverage = $this->readCoverage();
    }

    $results = array();
    foreach ($report as $result) {
      $result->setCoverage($coverage);
      $results[] = $result;
      $last_test_finished = true;
    }

    if (!$last_test_finished) {
      $results[] = id(new ArcanistUnitTestResult())
        ->setName(idx($event, 'test')) // use last event
        ->setUserData($this->stderr)
        ->setResult(ArcanistUnitTestResult::RESULT_BROKEN);
    }
    return $results;
  }

  /**
   * Read the coverage from phpunit generated clover report
   *
   * @return array
   */
  private function readCoverage() {
    $test_results = Filesystem::readFile($this->coverageFile);
    if (empty($test_results)) {
      return array();
    }

    $coverage_dom = new DOMDocument();
    $coverage_dom->loadXML($test_results);

    $reports = array();
    $files = $coverage_dom->getElementsByTagName('file');

    foreach ($files as $file) {
      $class_path = $file->getAttribute('name');
      // get total line count in file
      $line_count = count(file($class_path));

      $coverage = '';
      $start_line = 1;
      $lines = $file->getElementsByTagName('line');
      for ($ii = 0; $ii < $lines->length; $ii++) {
        $line = $lines->item($ii);
        for (; $start_line < $line->getAttribute('num'); $start_line++) {
          $coverage .= 'N';
        }

        if ($line->getAttribute('type') != 'stmt') {
          $coverage .= 'N';
        } else {
          if ((int)$line->getAttribute('count') == 0) {
            $coverage .= 'U';
          } else if ((int)$line->getAttribute('count') > 0) {
            $coverage .= 'C';
          }
        }

        $start_line++;
      }

      for (; $start_line <= $line_count; $start_line++) {
        $coverage .= 'N';
      }

      $len = strlen($this->projectRoot.DIRECTORY_SEPARATOR);
      $class_path = substr($class_path, $len);
      $reports[$class_path] = $coverage;
    }

    return $reports;
  }

  /**
   * @param string $junit String containing junit report
   * @return array of ArcanistUnitTestResult objects
   */
  private function getJunitReport($junit) {
    if (empty($junit)) {
      throw new Exception(
        pht(
          'junit report file is empty, it probably means that phpunit '.
          'failed to run tests. Try running %s with %s option and then run '.
          'generated phpunit command yourself, you might get the answer.',
          'arc unit',
          '--trace'));
    }

    $parser = new ArcanistXUnitTestResultParser();
    return $parser->parseTestResults($junit);
  }
}
