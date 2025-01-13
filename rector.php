<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app/Filament/Customer/Resources/OrderResource/Pages/*',
        // __DIR__.'/bootstrap',
        // __DIR__.'/public',
        // __DIR__.'/resources',
        // __DIR__.'/routes',
        // __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        carbon: false,
        rectorPreset: false,
        phpunitCodeQuality: false,
        doctrineCodeQuality: false,
        symfonyCodeQuality: false,
        symfonyConfigs: false,
    );
// uncomment to reach your current PHP version
// ->withTypeCoverageLevel(0)
// ->withDeadCodeLevel(0)
// ->withCodeQualityLevel(0);
