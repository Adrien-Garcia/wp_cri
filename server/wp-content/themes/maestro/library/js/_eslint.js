module.exports = {
    parserOptions: {
        ecmaVersion: 5,
        sourceType: 'module',
    },
    env: {
        browser: true,
        jquery: true,
    },
    rules: {
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

        'yoda': 'error',


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


    },
};