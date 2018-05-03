<?php

/**
 * This file is automatically generated. Use 'arc liberate' to rebuild it.
 *
 * @generated
 * @phutil-library-version 2
 */
phutil_register_library_map(array(
  '__library_version__' => 2,
  'class' => array(
    'AvtComposerLinter' => 'lint/linter/AvtComposerLinter.php',
    'AvtGeneratedLinter' => 'lint/linter/AvtGeneratedLinter.php',
    'AvtLintEngine' => 'lint/engine/AvtLintEngine.php',
    'AvtPhpunitTestEngine' => 'unit/engine/AvtPhpunitTestEngine.php',
    'AvtPhpunitTestResultParser' => 'unit/parser/AvtPhpunitTestResultParser.php',
    'AvtPuppetLintLinter' => 'lint/linter/AvtPuppetLintLinter.php',
  ),
  'function' => array(),
  'xmap' => array(
    'AvtComposerLinter' => 'ArcanistLinter',
    'AvtGeneratedLinter' => 'ArcanistLinter',
    'AvtLintEngine' => 'ArcanistLintEngine',
    'AvtPhpunitTestEngine' => 'ArcanistUnitTestEngine',
    'AvtPhpunitTestResultParser' => 'ArcanistTestResultParser',
    'AvtPuppetLintLinter' => 'ArcanistExternalLinter',
  ),
));
