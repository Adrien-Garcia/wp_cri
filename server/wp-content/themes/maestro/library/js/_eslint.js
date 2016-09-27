// eslint-disable-next-line no-undef
module.exports = {
    parserOptions: {
        ecmaVersion: 5,
        sourceType: 'module',
    },
    env: {
        browser: true,
        jquery: true,
        amd: true,
    },
    globals: { App: true },
    rules: {
        /** PROJECT SPECIFIC **/


        /** BEST PRACTICE **/

        strict: ['off', 'global'],

        // require trailing commas in multiline object literals
        'comma-dangle': ['error', 'always-multiline'],


        // enforces return statements in callbacks of array's methods
        // http://eslint.org/docs/rules/array-callback-return
        'array-callback-return': 'error',

        // treat var statements as if they were block scoped
        'block-scoped-var': 'error',

        // specify the maximum cyclomatic complexity allowed in a program
        complexity: ['off', 11],

        // require return statements to either always or never specify values
        'consistent-return': 'error',

        // specify curly brace conventions for all control statements
        curly: ['error', 'all'],

        // encourages use of dot notation whenever possible
        'dot-notation': ['error', { allowKeywords: true }],

        // enforces consistent newlines before or after dots
        'dot-location': 'off',

        // require the use of === and !==
        // http://eslint.org/docs/rules/eqeqeq
        eqeqeq: ['error', 'allow-null'],

        // make sure for-in loops have an if statement
        'guard-for-in': 'error',

        // disallow the use of alert, confirm, and prompt
        'no-alert': 'warn',

        // disallow use of arguments.caller or arguments.callee
        'no-caller': 'error',


        // disallow lexical declarations in case/default clauses
        // http://eslint.org/docs/rules/no-case-declarations.html
        'no-case-declarations': 'error',

        // disallow division operators explicitly at beginning of regular expression
        'no-div-regex': 'off',

        // disallow else after a return in an if
        // http://eslint.org/docs/rules/no-else-return
        'no-else-return': 'error',

        // disallow use of eval()
        'no-eval': 'error',

        // disallow adding to native types
        'no-extend-native': 'error',

        // disallow unnecessary function binding
        'no-extra-bind': 'error',

        // disallow the use of leading or trailing decimal points in numeric literals
        'no-floating-decimal': 'error',

        // disallow use of eval()-like methods
        'no-implied-eval': 'error',

        // disallow usage of __iterator__ property // Deprecated
        'no-iterator': 'error',

        // disallow use of multiline strings
        'no-multi-str': 'error',

        // disallow reassignments of native objects
        'no-native-reassign': 'error',

        // // disallow use of new operator when not part of the assignment or comparison
        // 'no-new': 'error,

        // disallow use of new operator for Function object
        'no-new-func': 'error',

        // disallows creating new instances of String, Number, and Boolean
        'no-new-wrappers': 'error',

        // disallow use of (old style) octal literals
        'no-octal': 'error',

        // disallow reassignment of function parameters
        // disallow parameter object manipulation
        // rule: http://eslint.org/docs/rules/no-param-reassign.html
        'no-param-reassign': 'error',

        // disallow usage of __proto__ property // Deprecated
        'no-proto': 'error',

        // disallow declaring the same variable more then once
        'no-redeclare': 'error',

        // disallow self assignment
        // http://eslint.org/docs/rules/no-self-assign
        'no-self-assign': 'error',

        // disallow comparisons where both sides are exactly the same
        'no-self-compare': 'error',

        // disallow useless string concatenation
        // http://eslint.org/docs/rules/no-useless-concat
        'no-useless-concat': 'error',

        // require use of the second argument for parseInt()
        radix: 'error',

        // requires to declare all vars on top of their containing scope
        'vars-on-top': 'warn',

        yoda: 'error',

        /** ERRORS **/


        // disallow assignment in conditional expressions
        'no-cond-assign': ['error', 'always'],

        // disallow use of console
        'no-console': 'warn',

        // disallow use of constant expressions in conditions
        'no-constant-condition': 'warn',

        // disallow duplicate arguments in functions
        'no-dupe-args': 2,

        // disallow duplicate keys when creating object literals
        'no-dupe-keys': 2,

        // disallow a duplicate case label.
        'no-duplicate-case': 2,

        // disallow empty statements
        'no-empty': 2,

        // disallow the use of empty character classes in regular expressions
        'no-empty-character-class': 2,

        // disallow double-negation boolean casts in a boolean context
        'no-extra-boolean-cast': 'off',

        // disallow unnecessary parentheses
        // http://eslint.org/docs/rules/no-extra-parens
        'no-extra-parens': ['off', 'all', {
            conditionalAssign: true,
            nestedBinaryExpressions: false,
            returnAssign: false,
        }],

        // disallow unnecessary semicolons
        'no-extra-semi': 2,

        // disallow function or variable declarations in nested blocks
        'no-inner-declarations': 2,

        // disallow invalid regular expression strings in the RegExp constructor
        'no-invalid-regexp': 2,

        // disallow irregular whitespace outside of strings and comments
        'no-irregular-whitespace': 2,

        // disallow negation of the left operand of an in expression
        'no-negated-in-lhs': 2,

        // disallow the use of object properties of the global object (Math and JSON) as functions
        'no-obj-calls': 2,

        // Avoid code that looks like two expressions but is actually one
        // http://eslint.org/docs/rules/no-unexpected-multiline
        'no-unexpected-multiline': 2,

        // disallow unreachable statements after a return, throw, continue, or break statement
        'no-unreachable': 2,

        // disallow return/throw/break/continue inside finally blocks
        // http://eslint.org/docs/rules/no-unsafe-finally
        'no-unsafe-finally': 2,

        // ensure JSDoc comments are valid
        // http://eslint.org/docs/rules/valid-jsdoc
        'valid-jsdoc': 0,

        // ensure that the results of typeof are compared against a valid string
        'valid-typeof': 2,


        /** Style **/


        // enforce spacing inside array brackets
        'array-bracket-spacing': [2, 'never'],

        // enforce spacing inside single-line blocks
        // http://eslint.org/docs/rules/block-spacing
        'block-spacing': [2, 'always'],

        // enforce one true brace style
        'brace-style': [2, '1tbs', { allowSingleLine: true }],

        // require camel case names
        camelcase: [2, { properties: 'never' }],

        // enforce spacing before and after comma
        'comma-spacing': [2, { before: false, after: true }],

        // enforce one true comma style
        'comma-style': [2, 'last'],

        // disallow padding inside computed properties
        'computed-property-spacing': [2, 'never'],

        // enforces consistent naming when capturing the current execution context
        'consistent-this': 0,

        // enforce newline at the end of file, with no multiple empty lines
        'eol-last': 2,

        // require function expressions to have a name
        'func-names': 0,

        // enforces use of function declarations or expressions
        'func-style': 0,

        // Blacklist certain identifiers to prevent them being used
        // http://eslint.org/docs/rules/id-blacklist
        'id-blacklist': 0,

        // this option enforces minimum and maximum identifier lengths
        // (variable names, property names etc.)
        'id-length': 0,

        // require identifiers to match the provided regular expression
        'id-match': 0,

        // this option sets a specific tab width for your code
        // http://eslint.org/docs/rules/indent
        indent: [2, 4, { SwitchCase: 1, VariableDeclarator: 1, outerIIFEBody: 1 }],

        // specify whether double or single quotes should be used in JSX attributes
        // http://eslint.org/docs/rules/jsx-quotes
        'jsx-quotes': [0, 'prefer-double'],

        // enforces spacing between keys and values in object literal properties
        'key-spacing': [2, { beforeColon: false, afterColon: true }],

        // require a space before & after certain keywords
        'keyword-spacing': [2, {
            before: true,
            after: true,
            overrides: {
                return: { after: true },
                throw: { after: true },
                case: { after: true },
            },
        }],

        // disallow mixed 'LF' and 'CRLF' as linebreaks
        // http://eslint.org/docs/rules/linebreak-style
        'linebreak-style': [2, 'unix'],

        // enforces empty lines around comments
        'lines-around-comment': 0,

        // specify the maximum depth that blocks can be nested
        'max-depth': [0, 4],

        // specify the maximum length of a line in your program
        // http://eslint.org/docs/rules/max-len
        'max-len': [0, 100, 2, {
            ignoreUrls: true,
            ignoreComments: false,
        }],

        // specify the max number of lines in a file
        // http://eslint.org/docs/rules/max-lines
        'max-lines': [0, {
            max: 300,
            skipBlankLines: true,
            skipComments: true,
        }],

        // specify the maximum depth callbacks can be nested
        'max-nested-callbacks': 0,

        // limits the number of parameters that can be used in the function declaration.
        'max-params': [0, 3],

        // specify the maximum number of statement allowed in a function
        'max-statements': [0, 10],

        // restrict the number of statements per line
        // http://eslint.org/docs/rules/max-statements-per-line
        'max-statements-per-line': [0, { max: 1 }],

        // require multiline ternary
        // http://eslint.org/docs/rules/multiline-ternary
        'multiline-ternary': 0,

        // require a capital letter for constructors
        'new-cap': [2, { newIsCap: true }],

        // disallow the omission of parentheses when invoking a constructor with no arguments
        // http://eslint.org/docs/rules/new-parens
        'new-parens': 2,

        // allow/disallow an empty newline after var statement
        'newline-after-var': 0,

        // http://eslint.org/docs/rules/newline-before-return
        'newline-before-return': 0,

        // enforces new line after each method call in the chain to make it
        // more readable and easy to maintain
        // http://eslint.org/docs/rules/newline-per-chained-call
        'newline-per-chained-call': [2, { ignoreChainWithDepth: 4 }],

        // disallow use of the Array constructor
        'no-array-constructor': 2,

        // disallow use of the continue statement
        // http://eslint.org/docs/rules/no-continue
        'no-continue': 2,

        // disallow comments inline after code
        'no-inline-comments': 0,

        // disallow if as the only statement in an else block
        // http://eslint.org/docs/rules/no-lonely-if
        'no-lonely-if': 2,

        // disallow un-paren'd mixes of different operators
        // http://eslint.org/docs/rules/no-mixed-operators
        'no-mixed-operators': [2, {
            groups: [
                ['+', '-', '*', '/', '%', '**'],
                ['&', '|', '^', '~', '<<', '>>', '>>>'],
                ['==', '!=', '===', '!==', '>', '>=', '<', '<='],
                ['&&', '||'],
                ['in', 'instanceof'],
            ],
            allowSamePrecedence: false,
        }],

        // disallow mixed spaces and tabs for indentation
        'no-mixed-spaces-and-tabs': 2,

        // disallow multiple empty lines and only one newline at the end
        'no-multiple-empty-lines': [2, { max: 2, maxEOF: 1 }],

        // disallow negated conditions
        // http://eslint.org/docs/rules/no-negated-condition
        'no-negated-condition': 0,

        // disallow nested ternary expressions
        'no-nested-ternary': 2,

        // disallow use of the Object constructor
        'no-new-object': 2,

        // disallow use of unary operators, ++ and --
        'no-plusplus': 0,

        // disallow space between function identifier and application
        'no-spaced-func': 2,

        // disallow the use of ternary operators
        'no-ternary': 0,

        // disallow trailing whitespace at the end of lines
        'no-trailing-spaces': 2,

        // disallow whitespace before properties
        // http://eslint.org/docs/rules/no-whitespace-before-property
        'no-whitespace-before-property': 2,

        // require padding inside curly braces
        'object-curly-spacing': [2, 'always'],

        // enforce "same line" or "multiple line" on object properties.
        // http://eslint.org/docs/rules/object-property-newline
        'object-property-newline': [2, {
            allowMultiplePropertiesPerLine: true,
        }],

        // require a newline around variable declaration
        // http://eslint.org/docs/rules/one-var-declaration-per-line
        'one-var-declaration-per-line': [2, 'always'],

        // require assignment operator shorthand where possible or prohibit it entirely
        // http://eslint.org/docs/rules/operator-assignment
        'operator-assignment': [2, 'always'],

        // enforce operators to be placed before or after line breaks
        'operator-linebreak': 0,

        // enforce padding within blocks
        'padded-blocks': [2, 'never'],

        // A voir si conserver
        // require quotes around object literal property names
        // http://eslint.org/docs/rules/quote-props.html
        'quote-props': [2, 'as-needed', { keywords: false, unnecessary: true, numbers: false }],

        // specify whether double or single quotes should be used
        quotes: [2, 'single', { avoidEscape: true }],

        // do not require jsdoc
        // http://eslint.org/docs/rules/require-jsdoc
        'require-jsdoc': 0,

        // require or disallow use of semicolons instead of ASI
        semi: [2, 'always'],

        // enforce spacing before and after semicolons
        'semi-spacing': [2, { before: false, after: true }],

        // sort variables within the same declaration block
        'sort-vars': 0,

        // require or disallow space before blocks
        'space-before-blocks': 2,

        // require or disallow space before function opening parenthesis
        // http://eslint.org/docs/rules/space-before-function-paren
        'space-before-function-paren': [2, { anonymous: 'always', named: 'never' }],

        // require or disallow spaces inside parentheses
        'space-in-parens': [2, 'never'],

        // require spaces around operators
        'space-infix-ops': 2,

        // Require or disallow spaces before/after unary operators
        // http://eslint.org/docs/rules/space-unary-ops
        'space-unary-ops': [2, {
            words: true,
            nonwords: false,
            overrides: {
            },
        }],

        // require or disallow a space immediately following the // or /* in a comment
        'spaced-comment': [2, 'always', {
            exceptions: ['-', '+'],
            markers: ['=', '!'],          // space here to support sprockets directives
        }],

        // require or disallow the Unicode Byte Order Mark
        // http://eslint.org/docs/rules/unicode-bom
        'unicode-bom': [2, 'never'],

        // require regex literals to be wrapped in parentheses
        'wrap-regex': 0,


        /** Variables **/


        // enforce or disallow variable initializations at definition
        'init-declarations': 0,

        // disallow the catch clause parameter name being the same as a variable in the outer scope
        'no-catch-shadow': 0,

        // disallow deletion of variables
        'no-delete-var': 2,

        // disallow labels that share a name with a variable
        // http://eslint.org/docs/rules/no-label-var
        'no-label-var': 2,

        // disallow specific globals
        'no-restricted-globals': 0,

        // disallow declaration of variables already declared in the outer scope
        'no-shadow': 2,

        // disallow shadowing of names such as arguments
        'no-shadow-restricted-names': 2,

        // disallow use of undeclared variables unless mentioned in a /*global */ block
        'no-undef': 2,

        // disallow use of undefined when initializing variables
        'no-undef-init': 2,

        // disallow use of undefined variable
        'no-undefined': 2,

        // disallow declaration of variables that are not used in the code
        'no-unused-vars': [2, { varsIgnorePattern: '^self$', vars: 'local', args: 'after-used' }],


        // disallow use of variables before they are defined
        'no-use-before-define': 2,
    },
};
