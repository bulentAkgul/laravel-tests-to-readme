<?php

return [
    /**
     * You can change these folder names as you wish.
     */

    'output_folder' => 'readme',

    'test_root' => 'tests',

    /**
     * We will use the following values to break the
     * readme files down and rebuilt them.
     */

    'class_identifier' => '## ',

    'method_identifier' => '### ',

    'code_block_identifier' => '```php',

    /**
     * Line length and number of parameters tell
     * how many lines will take displaying a
     * method declaration. If any one of these
     * values are excited, we will break the
     * method to multiple lines. After the limit
     * in force_break_for_multiline_parameters,
     * the multiple lines will be the case regarless
     * of the length of the line.
     */

    'line_length' => 80,

    'method_declaration_single_line_parameters' => 3,

    'force_break_for_multiline_parameters' => 5,

    /**
     * If you choose to display only the class names
     * instead of fully qualified namespaces, make
     * this false.
     */

    'show_full_class_name_when_type_hint' => true,

    /**
     * When this is true, we will try moving parameters and
     * return types from phpdoc to the method declarations.
     * We will keep rendering other parts of phpdoc and
     * explanations of parameters.
     * 
     * If the method has typehint and return types, we won't
     * make any changes on them regardless of this setting.
     * If method has these things, we will place a warning
     * message to the readme file if an inconsistency is
     * found between phpdoc and the method declaration.
     * 
     */

    'merge_phpdoc_and_method_declaration' => true,

    /**
     * In the case of an inconsistency between method and
     * and its phpdoc on the parameters' or return
     * types, we will output a warning that will tell
     * the issue. This is the list and messages of
     * the warnings.
     */

    'warnings' => [
        'mismatched_parameter_count' => 'mismatched parameter count',
        'misplaced_parameter' => 'misplaced parameter',
        'mismatched_variadic' => 'mismatched variadic parameter',
        'mismatched_reference' => 'mismatched reference parameter',
        'mismatched_type_count' => 'mismatched type count',
        'mismatched_types' => 'mismatched types',
        'missmatched_nullable_types' => 'missmatched nullable types'
    ]
];
