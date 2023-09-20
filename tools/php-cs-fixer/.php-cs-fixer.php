<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'class',
        'src/Azure',
        'src/Delivery',
        'src/Integration',
        'src/Order',
    ])
    ->in(realpath(__DIR__ . '/../../'));

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                  => true,
        'array_indentation'       => true,
        'array_syntax'            => ['syntax' => 'short'],
        'single_quote'            => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'binary_operator_spaces'  => [
            'operators' => [
                '=>'  => 'align_single_space',
                '='   => 'single_space',
                '+='  => 'single_space',
                '-='  => 'single_space',
                '|'   => 'single_space',
                '===' => 'single_space',
            ],
        ],
        'blank_line_after_namespace'  => true,
        'blank_line_before_statement' => [
            'statements' => [
                'if',
                'switch',
                'try',
                'foreach',
                'for',
                'while',
                'do',
                'require',
                'require_once',
            ],
        ],
        'cast_spaces'                 => ['space' => 'none'],
        'class_attributes_separation' => [
            'elements' => [
                'const'    => 'only_if_meta',
                'method'   => 'one',
                'property' => 'only_if_meta', ],
        ],
        'concat_space'                            => ['spacing' => 'one'],
        'constant_case'                           => [ 'case' => 'lower'],
        'control_structure_continuation_position' => ['position' => 'same_line'],
        'declare_equal_normalize'                 => ['space' => 'single'],
        'global_namespace_import'                 => [
            'import_classes'   => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'increment_style'       => ['style' => 'pre'],
        'list_syntax'           => ['syntax' => 'short'],
        'method_argument_space' => [
            'keep_multiple_spaces_after_comma' => false,
        ],
        'new_with_braces'       => ['anonymous_class' => true, 'named_class' => true],
        'no_alternative_syntax' => ['fix_non_monolithic_code' => true],
        'no_extra_blank_lines'  => [
            'tokens' => ['extra'],
        ],
        'no_trailing_comma_in_singleline' => ['elements' => [
            'arguments',
            'array',
            'array_destructuring',
            'group_import',
        ]],
        'no_unneeded_control_parentheses' => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'return',
                'switch_case',
                'yield',
                'others',
            ],
        ],
        'no_unneeded_curly_braces'                         => ['namespaces' => true],
        'no_useless_concat_operator'                       => ['juggle_simple_strings' => true],
        'no_whitespace_before_comma_in_array'              => ['after_heredoc' => true],
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
        'operator_linebreak'                               => ['only_booleans' => false, 'position' => 'beginning'],
        'ordered_class_elements'                           => ['order' => [
            'use_trait',
            'case',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public_readonly',
            'property_protected_readonly',
            'property_private_readonly',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public_abstract',
            'method_protected_abstract',
            'method_private_abstract',
            'method_public_abstract_static',
            'method_protected_abstract_static',
            'method_private_abstract_static',
            'method_public_static',
            'method_protected_static',
            'method_private_static',
            'method_public',
            'method_protected',
            'method_private',
        ],
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order'  => [
                'const',
                'class',
                'function',
            ],

        ],
        'ordered_interfaces' => [
            'direction' => 'ascend',
            'order'     => 'alpha',
        ],
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false,
        ],
        'phpdoc_align' => ['align' => 'vertical', 'tags'
        => [
            'method',
            'param',
            'property',
            'return',
            'throws',
            'type',
            'var',
        ]],
        'phpdoc_line_span' => [
            'const'    => 'single',
            'method'   => 'multi',
            'property' => 'single',
        ],
        'phpdoc_order' => [
            'order' => [
                'param',
                'return',
                'throws',
            ],
        ],
        'phpdoc_return_self_reference' => ['replacements' => ['this' => 'self']],
        'phpdoc_scalar'                => ['types' => ['double', 'boolean', 'integer', 'str', 'callback',
        ]],
        'phpdoc_types_order' => ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last',

        ],
        'return_type_declaration'            => ['space_before' => 'none'],
        'single_class_element_per_statement' => ['elements' => [
            'const',
            'property', ]],
        'single_import_per_statement' => true,
        'space_after_semicolon'       => ['remove_in_empty_for_expressions' => true],
        'trailing_comma_in_multiline' => ['after_heredoc' => true],
        'types_spaces'                => ['space' => 'single'],
        'visibility_required'         => ['elements' => [
            'property',
            'method',
            'const',
        ]],
        'whitespace_after_comma_in_array' => true,
        'yoda_style'                      => [
            'always_move_variable' => false,
            'equal'                => false,
            'identical'            => false,
            'less_and_greater'     => false],
        'no_unused_imports' => true,
    ])
    ->setLineEnding("\n")
    ->setFinder($finder)
;
