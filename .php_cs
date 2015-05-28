<?php

use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Config\Config;

$finder = DefaultFinder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__)
;

return Config::create()
    ->fixers(array(
        'psr0',
        'encoding',
        'short_tag',
        'braces',
        'elseif',
        'eof_ending',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'multiple_use',
        'php_closing_tag',
        'trailing_spaces',
        'visibility',
        'duplicate_semicolon',
        'extra_empty_lines',
        'include',
        'namespace_no_leading_whitespace',
        'object_operator',
        'operators_spaces',
        'phpdoc_params',
        'return',
        'single_array_no_trailing_comma',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'unused_use',
        'whitespacy_lines',
    ))
    ->finder($finder)
;