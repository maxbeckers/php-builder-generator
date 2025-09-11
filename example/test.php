<?php

require_once 'vendor/autoload.php';

use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;

echo "Testing PHP Builder Generator\n";
echo "============================\n\n";

echo "1. Generating builders...\n";
$service = new BuilderService();
$config = [];

$generated = $service->generateBuilders($config);
echo "Generated: {$generated} files\n\n";

echo "2. Checking generated files...\n";
$builderFiles = [
    'vendor/generated/php-builder-generator/Test/CompanyBuilder.php',
    'vendor/generated/php-builder-generator/Test/TestUserPublicAttributesBuilder.php',
    'vendor/generated/php-builder-generator/Test/TestUserWithConstructorAndSetterBuilder.php',
    'vendor/generated/php-builder-generator/Test/TestUserWithConstructorBuilder.php',
    'vendor/generated/php-builder-generator/Test/UserWithReferenceBuilder.php',
];
foreach ($builderFiles as $builderFile) {
    if (file_exists($builderFile)) {
        echo "✓ Builder file created: {$builderFile}\n";
        echo "File size: " . filesize($builderFile) . " bytes\n\n";
        require_once $builderFile;
    } else {
        echo "✗ Builder file not found!\n\n";
        exit(1);
    }
}

echo "3. Testing generated builder...\n";
require_once $builderFile;

use Test\TestUserPublicAttributesBuilder;

try {
    $user = TestUserPublicAttributesBuilder::builder()
        ->name('John Doe')
        ->email('john@example.com')
        ->age(30)
        ->roles(['admin', 'user'])
        ->active(false)
        ->build();

    echo "✓ Builder created successfully!\n";
    echo "User name: {$user->name}\n";
    echo "User email: {$user->email}\n";
    echo "User age: {$user->age}\n";
    echo "User roles: " . implode(', ', $user->roles) . "\n";
    echo "User active: " . ($user->active ? 'true' : 'false') . "\n\n";

} catch (Throwable $e) {
    echo "✗ Error testing builder: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "4. Testing fluent interface...\n";
try {
    $builder = TestUserPublicAttributesBuilder::builder()
        ->name('Jane Doe')
        ->email('jane@example.com');

    echo "✓ Fluent interface working\n\n";
} catch (Throwable $e) {
    echo "✗ Fluent interface error: " . $e->getMessage() . "\n\n";
}

echo "All tests passed! 🎉\n";
