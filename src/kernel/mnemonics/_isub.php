<?php
namespace PHPJava\Kernel\Mnemonics;

use \PHPJava\Exceptions\NotImplementedException;
use \PHPJava\Kernel\Utilities\BinaryTool;

final class _isub implements MnemonicInterface
{
    use \PHPJava\Kernel\Core\Accumulator;

    public function execute(): void
    {
        $leftValue = $this->getStack();
        $rightValue = $this->getStack();

        $this->pushStack(BinaryTool::sub($leftValue, $rightValue, 4));

    }

}