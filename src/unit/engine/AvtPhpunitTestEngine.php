<?php

/**
 * PHPUnit wrapper.
 */
final class AvtPhpunitTestEngine extends ArcanistUnitTestEngine {

  private $configFile;
  private $phpunitBinary = 'phpunit --stop-on-failure';
  private $affectedTests;
  private $projectRoot;

  public function supportsRunAllTests()
  {
    return true;
  }

  public function run() {
    $this->projectRoot = $this->getWorkingCopy()->getProjectRoot();
    $this->prepareConfigFile();

    $json_tmp = new TempFile();
    $clover_tmp = null;
    $clover = null;
    if ($this->getEnableCoverage() !== false) {
      $clover_tmp = new TempFile();
      $clover = csprintf('--coverage-clover %s', $clover_tmp);
    }

    $config = $this->configFile ? csprintf('-c %s', $this->configFile) : null;

    $stderr = '-d display_errors=stderr';

    $future = new ExecFuture('%C %C %C --log-json %s %C',
      $this->phpunitBinary, $config, $stderr, $json_tmp, $clover);

    $results = array();
    list($err, $stdout, $stderr) = $future->resolve();
    $results[] = $this->parseTestResults(
      'everything',
      $json_tmp,
      $clover_tmp,
      $stderr);

    return array_mergev($results);
  }

  /**
   * Parse test results from phpunit json report.
   *
   * @param string $path Path to test
   * @param string $json_tmp Path to phpunit json report
   * @param string $clover_tmp Path to phpunit clover report
   * @param string $stderr Data written to stderr
   *
   * @return array
   */
  private function parseTestResults($path, $json_tmp, $clover_tmp, $stderr) {
    $test_results = Filesystem::readFile($json_tmp);
    return id(new AvtPhpunitTestResultParser())
      ->setEnableCoverage($this->getEnableCoverage())
      ->setProjectRoot($this->projectRoot)
      ->setCoverageFile($clover_tmp)
      ->setStderr($stderr)
      ->parseTestResults($path, $test_results);
  }

  /**
   * Tries to find and update phpunit configuration file based on
   * `phpunit_config` option in `.arcconfig`.
   */
  private function prepareConfigFile() {
    $project_root = $this->projectRoot.DIRECTORY_SEPARATOR;
    $config = $this->getConfigurationManager()->getConfigFromAnySource(
      'phpunit_config');

    if ($config) {
      if (Filesystem::pathExists($project_root.$config)) {
        $this->configFile = $project_root.$config;
      } else {
        throw new Exception('PHPUnit configuration file was not '.
          'found in '.$project_root.$config);
      }
    }
    $bin = $this->getConfigurationManager()->getConfigFromAnySource(
      'unit.phpunit.binary');
    if ($bin) {
      if (Filesystem::binaryExists($bin)) {
        $this->phpunitBinary = $bin;
      } else {
        $this->phpunitBinary = Filesystem::resolvePath($bin, $project_root);
      }
    }
  }

}

