<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        'array_indentation' => true,
        'array_push' => true,
        'array_syntax' => ['syntax' => 'short'],
        'assign_null_coalescing_to_coalesce_equal' => true,
        'backtick_to_shell_exec' => true,
        'binary_operator_spaces' => true,
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => true,
        'blank_line_between_import_groups' => true,
        'braces' => true,
        'cast_spaces' => true,
        'class_attributes_separation' => ['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'one', 'case' => 'one']],
        'class_definition' => ['multi_line_extends_each_single_line' => true, 'single_line' => true, 'single_item_single_line' => true, 'space_before_parenthesis' => false, 'inline_constructor_arguments' => false],
        'class_reference_name_casing' => true,
        'clean_namespace' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'combine_nested_dirname' => true,
        'comment_to_phpdoc' => ['ignored_tags' => ['codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'phpstan-ignore-next-line']],
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'constant_case' => ['case' => 'lower'],
        'curly_braces_position' => ['control_structures_opening_brace' => 'same_line', 'functions_opening_brace' => 'next_line_unless_newline_at_signature_end', 'anonymous_functions_opening_brace' => 'same_line', 'classes_opening_brace' => 'next_line_unless_newline_at_signature_end', 'anonymous_classes_opening_brace' => 'same_line', 'allow_single_line_empty_anonymous_classes' => true, 'allow_single_line_anonymous_functions' => true],
        'date_time_create_from_format_call' => true,
        'date_time_immutable' => true,
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_parentheses' => true,
        'declare_strict_types' => true,
        'dir_constant' => true,
        'doctrine_annotation_array_assignment' => true,
        'doctrine_annotation_braces' => true,
        'doctrine_annotation_indentation' => true,
        'doctrine_annotation_spaces' => true,
        'echo_tag_syntax' => ['format' => 'long'],
        'elseif' => true,
        'empty_loop_body' => true,
        'empty_loop_condition' => true,
        'encoding' => true,
        'ereg_to_preg' => true,
        'error_suppression' => true,
        'escape_implicit_backslashes' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'final_class' => true,
        'final_internal_class' => true,
        'final_public_method_for_abstract_class' => true,
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'full_opening_tag' => true,
        'fully_qualified_strict_types' => true,
        'function_declaration' => true,
        'function_to_constant' => true,
        'function_typehint_space' => true,
        'general_phpdoc_annotation_remove' => true,
        'general_phpdoc_tag_rename' => true,
        'get_class_to_class_keyword' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'group_import' => true,
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'implode_call' => true,
        'include' => true,
        'increment_style' => ['style' => 'pre'],
        'indentation_type' => true,
        'integer_literal_case' => true,
        'is_null' => true,
        'lambda_not_used_import' => true,
        'line_ending' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'logical_operators' => true,
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'mb_str_functions' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline', 'after_heredoc' => true],
        'method_chaining_indentation' => true,
        'modernize_strpos' => true,
        'modernize_types_casting' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => true,
        'native_function_casing' => true,
        'native_function_invocation' => true,
        'native_function_type_declaration_casing' => true,
        'new_with_braces' => true,
        'no_alias_functions' => true,
        'no_alias_language_construct_call' => true,
        'no_alternative_syntax' => true,
        'no_binary_string' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_break_comment' => true,
        'no_closing_tag' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_homoglyph_names' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiple_statements_per_line' => true,
        'no_php4_constructor' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_space_around_double_colon' => true,
        'no_spaces_after_function_name' => true,
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_trailing_whitespace_in_string' => true,
        'no_trailing_whitespace' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unneeded_final_method' => true,
        'no_unneeded_import_alias' => true,
        'no_unreachable_default_argument_value' => true,
        'no_unset_cast' => true,
        'no_unused_imports' => true,
        'no_useless_concat_operator' => true,
        'no_useless_else' => true,
        'no_useless_nullsafe_operator' => true,
        'no_useless_return' => true,
        'no_useless_sprintf' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'non_printable_character' => true,
        'normalize_index_brace' => true,
        'not_operator_with_successor_space' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'object_operator_without_whitespace' => true,
        'octal_notation' => true,
        'operator_linebreak' => true,
        'ordered_class_elements' => ['sort_algorithm' => 'alpha', 'order' => ['use_trait', 'case', 'constant', 'constant_private', 'constant_protected', 'constant_public', 'property_private', 'property_private_readonly', 'property_private_static', 'property_protected', 'property_protected_readonly', 'property_protected_static', 'property_public', 'property_public_readonly', 'property_public_static', 'property_static', 'protected', 'construct', 'destruct', 'magic', 'method', 'public', 'method_public', 'method_abstract', 'method_public_abstract', 'method_public_abstract_static', 'method_public_static', 'method_static', 'method_private', 'method_private_abstract', 'method_private_abstract_static', 'method_private_static', 'method_protected', 'method_protected_abstract', 'method_protected_abstract_static', 'method_protected_static', 'phpunit', 'private', 'property']],
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['const', 'class', 'function']],
        'ordered_interfaces' => true,
        'ordered_traits' => true,
        'php_unit_fqcn_annotation' => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_line_span' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order_by_value' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => ['replacements' => ['this' => 'self']],
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_tag_type' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['var']],
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_trim' => true,
        'phpdoc_types_order' => true,
        'phpdoc_types' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'pow_to_exponentiation' => true,
        'protected_to_private' => true,
        'psr_autoloading' => true,
        'random_api_migration' => true,
        'regular_callable_call' => true,
        'return_assignment' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'return_type_declaration' => true,
        'self_accessor' => true,
        'self_static_accessor' => true,
        'semicolon_after_instruction' => true,
        'set_type_to_cast' => true,
        'short_scalar_cast' => true,
        'simple_to_complex_string_variable' => true,
        'simplified_if_return' => true,
        'single_blank_line_at_eof' => true,
        'single_blank_line_before_namespace' => true,
        'single_class_element_per_statement' => true,
        'single_line_after_imports' => true,
        'single_line_comment_spacing' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'single_line_throw' => true,
        'single_quote' => true,
        'single_space_after_construct' => true,
        'single_space_around_construct' => true,
        'single_trait_insert_per_statement' => true,
        'space_after_semicolon' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'statement_indentation' => true,
        'static_lambda' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'string_length_to_empty' => true,
        'string_line_ending' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'switch_continue_to_break' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_elvis_operator' => true,
        'ternary_to_null_coalescing' => true,
        'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arguments', 'arrays', 'match', 'parameters']],
        'trim_array_spaces' => true,
        'types_spaces' => ['space' => 'single', 'space_multiple_catch' => 'single'],
        'unary_operator_spaces' => true,
        'use_arrow_functions' => true,
        'visibility_required' => true,
        'void_return' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in([__DIR__ . '/src/']),
    );
