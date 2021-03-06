<?php
declare(strict_types=1);
namespace PHPJava\Core\JVM\Field;

use PHPJava\Packages\java\lang\NoSuchFieldException;

trait FieldGettable
{
    /**
     * @throws NoSuchFieldException
     */
    public function get(string $name)
    {
        $this->javaClassInvoker
            ->getStatic()
            ->getMethods()
            ->callStaticInitializerIfNotInstantiated();

        if (!array_key_exists($name, $this->fields)) {
            throw new NoSuchFieldException('Tried to get undefined field ' . $name);
        }

        return $this->fields[$name];
    }
}
