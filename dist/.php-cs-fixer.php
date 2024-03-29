<?php

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

$config = new PhpCsFixer\Config();

$config
    ->setIndent("    ")
    ->setRiskyAllowed(true)
    ->setRules([
    // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
    'modernize_types_casting' => true,
    // Use `&&` and `||` logical operators instead of `and` and `or`.
    'logical_operators' => true,
    // Converts simple usages of `array_push($x, $y);` to `$x[] = $y;`.
    'array_push' => true,
    // PHP arrays should be declared using the configured syntax.
    'array_syntax' => ['syntax'=>'short'],
    // Replaces `is_null($var)` expression with `null === $var`.
    'is_null' => true,
    // Convert double quotes to single quotes for simple strings.
    'single_quote' => true,
     // Unused `use` statements must be removed.
     'no_unused_imports' => true,
     // Short cast `bool` using double exclamation mark should not be used.
    'no_short_bool_cast' => true,
    // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
    'list_syntax' => ['syntax'=>'short'],    
    // In array declaration, there MUST be a whitespace after each comma.
    'whitespace_after_comma_in_array' => true,
    // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
    'no_spaces_after_function_name' => true,
    // Logical NOT operators (`!`) should have one trailing whitespace.
    'not_operator_with_successor_space' => true,
    // Cast shall be used, not `settype`.
    'set_type_to_cast' => true,
     // Cast should be written in lower case.
     'lowercase_cast' => true,
     // PHP keywords MUST be in lower case.
     'lowercase_keywords' => true,
     // Class static references `self`, `static` and `parent` MUST be in lower case.
     'lowercase_static_reference' => true,
    // Converts backtick operators to `shell_exec` calls.
    'backtick_to_shell_exec' => true,
    // Binary operators should be surrounded by space as configured.
    'binary_operator_spaces' => true,
    // Using `isset($var) &&` multiple times should be done in one call.
    'combine_consecutive_issets' => true,    
    // Master language constructs shall be used instead of aliases.
    'no_alias_functions' => true,
    // Master functions shall be used instead of aliases.
    'no_alias_language_construct_call' => true,
    // There must be no `sprintf` calls with only the first argument.
    'no_useless_sprintf' => true,
    // Unused `use` statements must be removed.
    'no_useless_else' => true,
    // Remove trailing whitespace at the end of blank lines.
    'no_whitespace_in_blank_line' => true,
    // There should not be an empty `return` statement at the end of a function.
    'no_useless_return' => true,
    // Ordering `use` statements.
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
    // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
    'trim_array_spaces' => true,
    // There should not be space before or after object operators `->` and `?->`.
    'object_operator_without_whitespace' => true,
    // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
    'non_printable_character' => ['use_escape_sequences_in_strings'=>true],
    // Ensure there is no code on the same line as the PHP open tag.
    'linebreak_after_opening_tag' => true,
    // Convert PHP4-style constructors to `__construct`.
    'no_php4_constructor' => true,
    // Short cast `bool` using double exclamation mark should not be used.
    'no_short_bool_cast' => true,
    // Remove trailing whitespace at the end of non-blank lines.
    'no_trailing_whitespace' => true,
    // There MUST be no trailing spaces inside comment or PHPDoc.
    'no_trailing_whitespace_in_comment' => true,
    // In array declaration, there MUST NOT be a whitespace before each comma.
    'no_whitespace_before_comma_in_array' => true,
    // Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
    'align_multiline_comment' => ['comment_type'=>'all_multiline'],
    // Each element of an array must be indented exactly once.
    'array_indentation' => true,
    // There MUST be one blank line after the namespace declaration.
    'blank_line_after_namespace' => true,
    // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
    'blank_line_after_opening_tag' => true,
    // Whitespace around the keywords of a class, trait or interfaces definition should be one space.
    'class_definition' => true,
    // Namespace must not contain spacing, comments or PHPDoc.
    'clean_namespace' => true,
    // Class, trait and interface elements must be separated with one or none blank line.
    'class_attributes_separation' => true,
    // The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.
    'constant_case' => true,
    // PHP code MUST use only UTF-8 without BOM (remove BOM).
    'encoding' => true,    
    // Standardize spaces around ternary operator.
    'ternary_operator_spaces' => true,
    // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
    'ternary_to_null_coalescing' => true,
    // Use the Elvis operator `?:` where possible.
    'ternary_to_elvis_operator' => true,
    // Switch case must not be ended with `continue` but with `break`.
    'switch_continue_to_break' => true,
    // The closing `? >` tag MUST be omitted from files containing only PHP.
    'no_closing_tag' => true,
    // Replace all `<>` with `!=`.
    'standardize_not_equals' => true,
    // A case should be followed by a colon and not a semicolon.
    'switch_case_semicolon_to_colon' => true,
    // Unary operators should be placed adjacent to their operands.
    'unary_operator_spaces' => true,
    // Removes extra spaces between colon and case value.
    'switch_case_space' => true,
    // There should not be any empty comments.
    'no_empty_comment' => true,
    // There should not be empty PHPDoc blocks.
    'no_empty_phpdoc' => true,
    // Remove useless (semicolon) statements.
    'no_empty_statement' => true,
    // A PHP file without end tag must always end with a single empty line feed.
    'single_blank_line_at_eof' => true,
    // There should be exactly one blank line before a namespace declaration.
    'single_blank_line_before_namespace' => true,
    // Classy that does not inherit must not have `@inheritdoc` tags.
    'phpdoc_no_useless_inheritdoc' => true,
    // Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.
    'phpdoc_order' => true,
    // The type of `@return` annotations of methods returning a reference to itself must the configured one.
    'phpdoc_return_self_reference' => true,
    // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
    'phpdoc_scalar' => true,
    // Fixes casing of PHPDoc tags.
    'phpdoc_tag_casing' => true,
    // Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.
    'short_scalar_cast' => true,
    // There MUST NOT be more than one property or constant declared per statement.
    'single_class_element_per_statement' => true,
    // There MUST be one use keyword per declaration.
    'single_import_per_statement' => true,
    // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
    'single_line_after_imports' => true,
    // Single-line comments and multi-line comments with only one line of actual content should use the `//` syntax.
    'single_line_comment_style' => true,
    // Convert double quotes to single quotes for simple strings.
    'single_trait_insert_per_statement' => true,
    // Magic constants should be referred to using the correct casing.
    'magic_constant_casing' => true,
    // Magic method definitions and calls must be using the correct casing.
    'magic_method_casing' => true,
    // Array index should always be written by using square braces.
    'normalize_index_brace' => true,
    // There should not be blank lines between docblock and the documented element.
    'no_blank_lines_after_phpdoc' => true,
    // Removes `@param`, `@return` and `@var` tags that don't provide any useful information.
    'no_superfluous_phpdoc_tags' => false,
    // Adds or removes `?` before type declarations for parameters with a default `null` value.
    'nullable_type_declaration_for_default_null_value' => true,
    // Trait `use` statements must be sorted alphabetically.
    'ordered_traits' => true,
    // In function arguments there must not be arguments with default values before non-default ones.
    'no_unreachable_default_argument_value' => true,
    // Removes unneeded parentheses around control statements.
    'no_unneeded_control_parentheses' => true,
    // Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
    'no_unneeded_curly_braces' => true,    
     // Remove leading slashes in `use` clauses.
     'no_leading_import_slash' => true,
     // The namespace declaration line shouldn't contain leading whitespace.
     'no_leading_namespace_whitespace' => true,
     // Replace accidental usage of homoglyphs (non ascii characters) in names.
    'no_homoglyph_names' => true,
    // All instances created with new keyword must be followed by braces.
    'new_with_braces' => true,
    // Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).
    'simple_to_complex_string_variable' => true,
    // Simplify `if` control structures that return the boolean result of their condition.
    'simplified_if_return' => true,
    // Instructions must be terminated with a semicolon.
    'semicolon_after_instruction' => true,
    // Native type hints for functions should use the correct case.
    'native_function_type_declaration_casing' => true,
    // Function defined by PHP should be called using the correct casing.
    'native_function_casing' => true,
    // Either language construct `print` or `echo` should be used.
    'no_mixed_echo_print' => true,
    // Operator `=>` should not be surrounded by multi-line whitespaces.
    'no_multiline_whitespace_around_double_arrow' => true,
    // Function `implode` must be called with 2 arguments in the documented order.
    'implode_call' => true,
    // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
    'full_opening_tag' => true,
    // Replace core functions calls returning constants with the constants.
    'function_to_constant' => true,
    // Order the flags in `fopen` calls, `b` and `t` must be last.
    'fopen_flag_order' => true,
    // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
    'no_spaces_inside_parenthesis' => true,
    // Remove extra spaces in a nullable typehint.
    'compact_nullable_typehint' => true,
    // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
    'multiline_whitespace_before_semicolons' => true,
     // Orders the elements of classes/interfaces/traits.
     'ordered_class_elements' => ['order'=>['use_trait','constant_public','constant_protected','constant_private','property_public','property_protected','property_private','construct','destruct','magic','phpunit','method_public','method_protected','method_private']],
     // Orders the interfaces in an `implements` or `interface extends` clause.
     'ordered_interfaces' => true,
    // Comments with annotation should be docblock when used on structural elements.
    'comment_to_phpdoc' => true,
    // Renames PHPDoc tags.
    'general_phpdoc_tag_rename' => true,
    // Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.
    'psr_autoloading' => false,
    // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
    'dir_constant' => false,
    // Replace multiple nested calls of `dirname` by only one call with second `$level` parameter. Requires PHP >= 7.0.
    'combine_nested_dirname' => true,
    // Replace deprecated `ereg` regular expression functions with `preg`.
    'ereg_to_preg' => true,
    // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
    'elseif' => true,
    // Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.
    'explicit_indirect_variable' => true,
    // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
    'explicit_string_variable' => true,
    // Calling `unset` on multiple items should be done in one call.
    'combine_consecutive_unsets' => true,
    // Include/Require and file path should be divided with a single space. File path should not be placed under brackets.
    'include' => true,
    // All PHP files must use same line ending.
    'line_ending' => true,
    // An empty line feed must precede any configured statement.
    'blank_line_before_statement' => ['statements'=>[]],
    // A single space or none should be between cast and variable.
    'cast_spaces' => ['space'=>'none'],
    // Variables must be set `null` instead of using `(unset)` casting.
    'no_unset_cast' => false,
    // Properties should be set to `null` instead of using `unset`.
    'no_unset_on_property' => false,
    // All multi-line strings must use correct line ending.
    'string_line_ending' => true,
    // A `final` class must not have `final` methods and `private` methods must not be `final`.
    'no_unneeded_final_method' => true,
    // Converts `protected` variables and methods to `private` where possible.
    'protected_to_private' => false,
    // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
    'method_argument_space' => false,
    // Ensure single space between function's argument and its typehint.
    'function_typehint_space' => true,
    // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
    'method_chaining_indentation' => true,
    // Concatenation should be spaced according configuration.
    'concat_space' => ['spacing'=>'one'],
    // Equal sign in declare statement should be surrounded by spaces or not following configuration.
    'declare_equal_normalize' => ['space'=>'none'],
    // Visibility MUST be declared on all properties and methods; `abstract` and `final` MUST be declared before the visibility; `static` MUST be declared after the visibility.
    'visibility_required' => true,
    // Add `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. Requires PHP >= 7.1.
    'void_return' => false,
    // There should be one or no space before colon, and one space after it in return type declarations, according to configuration.
    'return_type_declaration' => ['space_before'=>'none'],
    // Lambdas not (indirect) referencing `$this` must be declared `static`.
    'static_lambda' => true,
    // Replace control structure alternative syntax to use braces.
    'no_alternative_syntax' => true,
    // A return statement wishing to return `void` should not return `null`.
    'simplified_null_return' => false,
    // Spaces should be properly placed in a function declaration.
    'function_declaration' => ['closure_function_spacing'=>'none', 'closure_fn_spacing'=>'none'],
    // There must be a comment when fall-through is intentional in a non-empty case body.
    'no_break_comment' => ['comment_text'=>'Intentionally fall through'],
    // Write conditions in Yoda style (`true`), non-Yoda style (`['equal' => false, 'identical' => false, 'less_and_greater' => false]`) or ignore those conditions (`null`) based on configuration.
    'yoda_style' => false,
    // Add leading \ before function invocation to speed up resolving.
    'native_function_invocation' => false,
    // Replace non multibyte-safe functions with corresponding mb function.
    'mb_str_functions' => false,
    // Replace strpos() calls with str_starts_with() or str_contains() if possible.
    'modernize_strpos' => true,
    // Putting blank lines between use statement groups.
    'blank_line_between_import_groups' => true,
    // Code MUST use configured indentation type.
    'indentation_type'=> true,
    // Each statement must be indented.
    'statement_indentation' => true,
    // Curly braces must be placed as configured.
    'curly_braces_position' => [
        'control_structures_opening_brace' => 'same_line',
        'functions_opening_brace' => 'same_line',
        'anonymous_functions_opening_brace' => 'same_line',
        'classes_opening_brace' => 'same_line',
        'anonymous_classes_opening_brace' => 'same_line',
        'allow_single_line_empty_anonymous_classes' => true,
        'allow_single_line_anonymous_functions' => true
    ],
    // Removes extra blank lines and/or blank lines following configuration.
    'no_extra_blank_lines' => ['tokens' => ['extra']]
    ]);

return $config->setFinder(
    PhpCsFixer\Finder::create()
    ->in(__DIR__)
        ->exclude(__DIR__ .'/admin/fm')
        ->exclude(__DIR__ .'/vendor')
);
