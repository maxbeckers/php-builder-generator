<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator;

class ImportManager
{
    private array $imports = [];
    private string $currentNamespace;

    public function __construct(string $currentNamespace)
    {
        $this->currentNamespace = $currentNamespace;
    }

    public function addType(string $fullTypeName): string
    {
        $fullTypeName = ltrim($fullTypeName, '\\');

        if ($this->isBuiltinType($fullTypeName)) {
            return $fullTypeName;
        }

        $lastSlash = strrpos($fullTypeName, '\\');
        if ($lastSlash === false) {
            return $fullTypeName;
        }

        $namespace = substr($fullTypeName, 0, $lastSlash);
        $className = substr($fullTypeName, $lastSlash + 1);

        if ($namespace === $this->currentNamespace) {
            return $className;
        }

        $this->imports[$fullTypeName] = $className;

        return $className;
    }

    public function getImports(): array
    {
        ksort($this->imports);
        return array_keys($this->imports);
    }

    private function isBuiltinType(string $type): bool
    {
        $builtinTypes = [
            'int', 'float', 'string', 'bool', 'array', 'object',
            'mixed', 'null', 'callable', 'iterable', 'void', 'never'
        ];

        return in_array($type, $builtinTypes);
    }

    public function formatType(?string $type): string
    {
        if ($type === null || $type === '') {
            return 'mixed';
        }

        if (str_starts_with($type, '?')) {
            $baseType = substr($type, 1);
            return '?' . $this->addType($baseType);
        }

        if (str_contains($type, '|')) {
            $types = explode('|', $type);
            $formattedTypes = array_map(fn($t) => $this->addType(trim($t)), $types);
            return implode('|', $formattedTypes);
        }

        return $this->addType($type);
    }
}