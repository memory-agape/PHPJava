<?php
declare(strict_types=1);
namespace PHPJava\Compiler\Lang\Assembler\Traits;

use PHPJava\Compiler\Builder\Attributes\Code;
use PHPJava\Compiler\Builder\Collection\Attributes;
use PHPJava\Compiler\Builder\Collection\Fields;
use PHPJava\Compiler\Builder\Collection\Methods;
use PHPJava\Compiler\Builder\Field;
use PHPJava\Compiler\Builder\Method;
use PHPJava\Compiler\Builder\Signatures\Descriptor;
use PHPJava\Compiler\Builder\Signatures\MethodAccessFlag;
use PHPJava\Compiler\Lang\Assembler\Enhancer\ConstantPoolEnhancer;
use PHPJava\Kernel\Maps\OpCode;
use PHPJava\Kernel\Types\Void_;
use PHPJava\Utilities\ArrayTool;

/**
 * @method ConstantPoolEnhancer getEnhancedConstantPool()
 * @property Fields $fields
 * @property Methods $methods
 */
trait StaticInitializerAssignable
{
    public function assignStaticInitializer(string $className): self
    {
        if ($this->fields->length() === 0) {
            return $this;
        }
        $descriptor = (new Descriptor())
            ->setReturn(Void_::class)
            ->make();

        // The <clinit> is not required methodref, it is not possible to invoke in the class.
        // But it need a method name and descriptor info in UTF-8 format.
        // See: https://docs.oracle.com/javase/specs/jvms/se7/html/jvms-2.html#jvms-2.9
        $this->getEnhancedConstantPool()
            ->addUtf8('<clinit>')
            ->addUtf8($descriptor);

        $staticInitializerOperations = [];
        foreach ($this->fields as $field) {
            /**
             * @var Field $field
             */
            ArrayTool::concat(
                $staticInitializerOperations,
                ...$this->assembleAssignStaticField(
                    $field->getClassName(),
                    $field->getName(),
                    $field->getValue(),
                    $field->getDescriptor()
                )
            );
        }

        $staticInitializerOperations[] = \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
            OpCode::_return
        );

        // Define <clinit> for initialize static fields.
        $this->methods
            ->add(
                (new Method(
                    (new MethodAccessFlag())
                        ->enableStatic()
                        ->make(),
                    $className,
                    '<clinit>',
                    $descriptor
                ))
                    ->setConstantPool($this->getConstantPool())
                    ->setConstantPoolFinder($this->getConstantPoolFinder())
                    ->setAttributes(
                        (new Attributes())
                            ->add(
                                (new Code())
                                    ->setConstantPool($this->getConstantPool())
                                    ->setConstantPoolFinder($this->getConstantPoolFinder())
                                    ->setCode($staticInitializerOperations)
                                    ->beginPreparation()
                            )
                            ->toArray()
                    )
            );
        return $this;
    }
}
