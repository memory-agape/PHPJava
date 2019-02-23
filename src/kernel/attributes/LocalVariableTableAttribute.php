<?php
namespace PHPJava\Kernel\Attributes;

use \PHPJava\Exceptions\NotImplementedException;
use \PHPJava\Kernel\Utilities\BinaryTool;

final class LocalVariableTableAttribute implements AttributeInterface
{
    use \PHPJava\Kernel\Core\BinaryReader;

    private $LocalVariableTableLength;
    private $LocalVariableTables = array();
    public function execute(): void
    {
        
        $this->LocalVariableTableLength = $this->readUnsignedShort();
        for ($i = 0; $i < $this->LocalVariableTableLength; $i++) {
            $this->LocalVariableTables[] = new JavaStructureLocalVariableTable($Class);
        }
    }
}