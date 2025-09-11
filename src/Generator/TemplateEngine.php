<?php
declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TemplateEngine
{
    private Environment $twig;

    public function __construct(string $templatesPath = null)
    {
        $templatesPath ??= __DIR__ . '/../../templates/php82';
        $this->setupTwig($templatesPath);
    }

    private function setupTwig(string $templatesPath): void
    {
        $loader = new FilesystemLoader([
            $templatesPath
        ]);

        $this->twig = new Environment($loader, [
            'debug' => true,
            'strict_variables' => true,
            'autoescape' => false,
        ]);

        $this->addCustomFunctions();
    }

    private function addCustomFunctions(): void
    {
        $this->twig->addFunction(new TwigFunction('pascal_case', function (string $text): string {
            return str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $text)));
        }));

        $this->twig->addFunction(new TwigFunction('camel_case', function (string $text): string {
            return lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $text))));
        }));

        $this->twig->addFunction(new TwigFunction('format_type', function (?string $type): string {
            if ($type === null) {
                return 'mixed';
            }

            return match($type) {
                'int' => 'int',
                'float' => 'float',
                'string' => 'string',
                'bool' => 'bool',
                'array' => 'array',
                'object' => 'object',
                'mixed' => 'mixed',
                'null' => 'null',
                default => $type
            };
        }));

        $this->twig->addFunction(new TwigFunction('php_export', function (mixed $value): string {
            if ($value === null) {
                return 'null';
            }

            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }

            if (is_string($value)) {
                return "'" . addslashes($value) . "'";
            }

            if (is_array($value)) {
                if (empty($value)) {
                    return '[]';
                }

                $isSimple = true;
                foreach ($value as $item) {
                    if (!is_scalar($item) && $item !== null) {
                        $isSimple = false;
                        break;
                    }
                }

                if ($isSimple) {
                    $elements = array_map(fn($item) => var_export($item, true), $value);
                    $inline = '[' . implode(', ', $elements) . ']';
                    if (strlen($inline) < 80) {
                        return $inline;
                    }
                }
            }

            $exported = var_export($value, true);

            if ($exported === "array (\n)") {
                return '[]';
            }

            return $exported;
        }));
    }

    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}
