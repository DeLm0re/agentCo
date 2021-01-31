<?php

namespace App\Helper;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * Class PropertyExtractor.
 */
class PropertyExtractor extends PropertyInfoExtractor
{
    /**
     * PropertyExtractor constructor.
     */
    public function __construct()
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propInitExtractors = [$reflectionExtractor];

        /*
         * This class acts like a wrapper to simplify code of controllers
         */
        parent::__construct(
            $listExtractors,
            $typeExtractors,
            $descExtractors,
            $accessExtractors,
            $propInitExtractors
        );
    }

    /**
     * @param string $class
     * @param array  $context
     *
     * @return null
     */
    public function getPropertiesForForm(string $class, array $context = []): ?array
    {
        $properties = parent::getProperties($class, $context);
        $propertiesNotForForm = $this->getPropertiesNotForForm();

        return $properties ? \array_diff($properties, $propertiesNotForForm) : null;
    }

    /**
     * @return string[]
     */
    private function getPropertiesNotForForm(): array
    {
        return [
            'id',
            'createdAt',
            'updatedAt',
        ];
    }
}
