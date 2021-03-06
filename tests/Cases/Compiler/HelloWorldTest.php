<?php
declare(strict_types=1);
namespace PHPJava\Tests\Cases\Compiler;

use PHPJava\Compiler\Builder\Attributes\Code;
use PHPJava\Compiler\Builder\Collection\Attributes;
use PHPJava\Compiler\Builder\Collection\ConstantPool;
use PHPJava\Compiler\Builder\Collection\Methods;
use PHPJava\Compiler\Builder\Finder\ConstantPoolFinder;
use PHPJava\Compiler\Builder\Generator\Operation\Operand;
use PHPJava\Compiler\Builder\Method;
use PHPJava\Compiler\Builder\Signatures\ClassAccessFlag;
use PHPJava\Compiler\Builder\Signatures\Descriptor;
use PHPJava\Compiler\Builder\Signatures\MethodAccessFlag;
use PHPJava\Compiler\Builder\Structures\ClassFileStructure;
use PHPJava\Compiler\Builder\Types\Uint16;
use PHPJava\Compiler\Builder\Types\Uint8;
use PHPJava\Compiler\Compiler;
use PHPJava\Compiler\Lang\Assembler\Enhancer\ConstantPoolEnhancer;
use PHPJava\Core\JavaClass;
use PHPJava\Core\JavaCompiledClass;
use PHPJava\Core\JVM\Parameters\Runtime;
use PHPJava\Core\Stream\Reader\InlineReader;
use PHPJava\IO\Standard\Output;
use PHPJava\Kernel\Maps\OpCode;
use PHPJava\Kernel\Resolvers\SDKVersionResolver;
use PHPJava\Kernel\Types\Void_;
use PHPJava\Packages\java\io\PrintStream;
use PHPJava\Packages\java\lang\Object_;
use PHPJava\Packages\java\lang\String_;
use PHPJava\Packages\java\lang\System;
use PHPJava\Tests\Cases\Base;

class HelloWorldTest extends Base
{
    public function testHelloWorld(): void
    {
        // Generate class
        $source = fopen('php://memory', 'r+');

        $constantPool = new ConstantPool();
        $finder = new ConstantPoolFinder($constantPool);
        [$majorVersion, $minorVersion] = SDKVersionResolver::resolveByVersion(
            Runtime::PHP_COMPILER_JDK_VERSION
        );

        $enhancedConstantPool = ConstantPoolEnhancer::factory(
            $constantPool,
            $finder
        );

        $className = 'HelloWorld';

        $enhancedConstantPool
            ->addString('Hello PHPJava Compiler!')
            ->addClass(Object_::class)
            ->addClass($className)
            ->addClass(System::class)
            ->addClass(PrintStream::class)
            ->addFieldref(
                System::class,
                'out',
                (new Descriptor())
                    ->addArgument(PrintStream::class)
                    ->make()
            )
            ->addMethodref(
                PrintStream::class,
                'println',
                (new Descriptor())
                    ->addArgument(String_::class)
                    ->setReturn(Void_::class)
                    ->make()
            )
            ->addNameAndType(
                'main',
                (new Descriptor())
                    ->addArgument(String_::class, 1)
                    ->setReturn(Void_::class)
                    ->make()
            );

        $compiler = new Compiler(
            (new ClassFileStructure())
                ->setMinorVersion($minorVersion)
                ->setMajorVersion($majorVersion)
                ->setAccessFlags(
                    (new ClassAccessFlag())
                        ->enableSuper()
                        ->make()
                )
                ->setThisClass($enhancedConstantPool->findClass($className))
                ->setSuperClass($enhancedConstantPool->findClass(Object_::class))
                ->setMethods(
                    (new Methods())
                        ->add(
                            (new Method(
                                (new MethodAccessFlag())
                                    ->enablePublic()
                                    ->enableStatic()
                                    ->make(),
                                $className,
                                'main',
                                (new Descriptor())
                                    ->addArgument(String_::class, 1)
                                    ->setReturn(Void_::class)
                                    ->make()
                            ))
                                ->setConstantPool($constantPool)
                                ->setConstantPoolFinder($finder)
                                ->setAttributes(
                                    (new Attributes())
                                        ->add(
                                            (new Code($enhancedConstantPool->findUtf8('Code')))
                                                ->setConstantPool($constantPool)
                                                ->setConstantPoolFinder($finder)
                                                ->setCode(
                                                    [
                                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                                            OpCode::_getstatic,
                                                            Operand::factory(
                                                                Uint16::class,
                                                                $enhancedConstantPool->findField(
                                                                    System::class,
                                                                    'out',
                                                                    (new Descriptor())
                                                                        ->addArgument(PrintStream::class)
                                                                        ->make()
                                                                )
                                                            )
                                                        ),
                                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                                            OpCode::_ldc,
                                                            Operand::factory(
                                                                Uint8::class,
                                                                $enhancedConstantPool->findString('Hello PHPJava Compiler!')
                                                            )
                                                        ),
                                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                                            OpCode::_invokevirtual,
                                                            Operand::factory(
                                                                Uint16::class,
                                                                $enhancedConstantPool->findMethod(
                                                                    PrintStream::class,
                                                                    'println',
                                                                    (new Descriptor())
                                                                        ->addArgument(String_::class)
                                                                        ->setReturn(Void_::class)
                                                                        ->make()
                                                                )
                                                            )
                                                        ),
                                                        \PHPJava\Compiler\Builder\Generator\Operation\Operation::create(
                                                            OpCode::_return
                                                        ),
                                                    ]
                                                )
                                                ->beginPreparation()
                                        )
                                        ->toArray()
                                )
                        )
                        ->toArray()
                )
                ->setConstantPool($constantPool->toArray())
        );

        $compiler->compile($source);
        rewind($source);

        $javaClass = new JavaClass(
            new JavaCompiledClass(
                new InlineReader(
                    $className,
                    stream_get_contents($source)
                )
            )
        );

        $javaClass
            ->getInvoker()
            ->getStatic()
            ->getMethods()
            ->call(
                'main',
                []
            );

        $result = trim(Output::getHeapspace());

        $this->assertSame('Hello PHPJava Compiler!', $result);
    }
}
