<?php

declare(strict_types=1);

return [
    'preset' => 'laravel',
    'ide'    => null,

    'exclude' => [],

    'add' => [
        \NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments::class => [
            \SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff::class,
        ],
    ],

    'remove' => [
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff::class,
        \ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff::class,
        \ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff::class,
        \ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff::class,
    ],

    'config' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff::class => [
            'exclude' => [
            ],
        ],
        \SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class => [
            'exclude' => [
                'src/ServiceProvider.php',
            ],
        ],
        \SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff::class => [
            'exclude' => [
            ],
        ],
        \SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff::class => [
            'minLineLength' => '0',
        ],
    ],

    'requirements' => [
        'min-quality'            => 100,
        'min-complexity'         => 50,
        'min-architecture'       => 100,
        'min-style'              => 100,
        'disable-security-check' => false,
    ],
];
