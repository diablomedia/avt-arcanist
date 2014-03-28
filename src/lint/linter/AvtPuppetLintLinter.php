<?php

/**
 * Uses "puppet-lint" to detect style errors in Puppet manifests.
 * To use this linter, you must install puppet-lint
 * http://puppet-lint.com
 *
 * Optional configurations in .arcconfig:
 *
 *   lint.puppetlint.prefix
 *   lint.puppetlint.bin
 */
final class AvtPuppetLintLinter extends ArcanistExternalLinter {

  public function getLinterName() {
    return 'PuppetLint';
  }

  public function getInstallInstructions() {
    return pht('Install puppet-lint with `gem install puppet-lint`.');
  }

  public function getLinterConfigurationName() {
    return 'puppetlint';
  }

  public function shouldExpectCommandErrors() {
    return true;
  }

  public function getMandatoryFlags() {
    return '--log-format %{linenumber}:%{column}:%{kind}:%{message}';
  }

  public function getDefaultBinary() {
    $config = $this->getEngine()->getConfigurationManager();
    $prefix = $config->getConfigFromAnySource('lint.puppetlint.prefix');
    $bin = $config->getConfigFromAnySource('lint.puppetlint.bin');

    if ($bin === null) {
      $bin = 'puppet-lint';
    }

    if ($prefix !== null) {
      $bin = $prefix.'/'.$bin;

      if (!Filesystem::pathExists($bin)) {
        throw new ArcanistUsageException(
        "Unable to find puppet-lint binary in a specified directory. Make sure".
        "that 'lint.puppetlint.prefix' and 'lint.puppetlint.bin' keys are set".
        "correctly.");
      }
    }

    if (!Filesystem::binaryExists($bin)) {
      throw new ArcanistUsageException(
        "Puppet-lint does not appear to be installed on this system. Install".
        "it (e.g., with 'gem install puppet-lint') or configure".
        "'lint.puppetlint.prefix' in your .arcconfig to point to the directory".
        "where it resides.");
    }

    return $bin;
  }

  protected function getDefaultMessageSeverity($code) {
    switch ($code) {
      case 'warning':
        return ArcanistLintSeverity::SEVERITY_WARNING;
        break;
      case 'fixed':
        return ArcanistLintSeverity::SEVERITY_AUTOFIX;
        break;
      case 'error':
        // Default to error
      default:
        return ArcanistLintSeverity::SEVERITY_ERROR;
        break;
    }
  }

  protected function parseLinterOutput($path, $err, $stdout, $stderr) {
    if (!$stdout) {
      return array();
    }

    $lines = phutil_split_lines($stdout, $retain_endings = false);

    $messages = array();
    foreach ($lines as $line) {
      list($line, $column, $code, $desc) = explode(':', $line);
      $message = new ArcanistLintMessage();
      $message->setPath($path);
      $message->setLine($line);
      $message->setChar($column);
      $message->setCode($code);
      $message->setDescription($desc);
      $message->setSeverity($this->getLintMessageSeverity($code));

      $messages[] = $message;
    }

    if ($err && !$messages) {
      return false;
    }

    return $messages;

  }

}
