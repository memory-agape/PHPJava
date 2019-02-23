<?php
namespace PHPJava\Kernel\Mnemonics;

use \PHPJava\Exceptions\NotImplementedException;
use \PHPJava\Kernel\Utilities\BinaryTool;

final class _dconst_1 implements MnemonicInterface
{
    use \PHPJava\Kernel\Core\Accumulator;

    public function execute(): void
    {
        $this->pushStack(1);
        
    }

}   