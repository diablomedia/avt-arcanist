<?php

/**
 * Stops other linters from running on generated code.
 *
 * @group linter
 */
final class AvtGeneratedLinter extends ArcanistLinter {

  public function willLintPaths(array $paths) {
    return;
  }

  public function getLinterName() {
    return 'AVTGENERATED';
  }

  public function getLintSeverityMap() {
    return array();
  }

  public function getLintNameMap() {
    return array(
    );
  }

  public function lintPath($path) {
    if (preg_match('/[gG]enerated/', $path)) {
      $this->stopAllLinters();
    }
  }

}
