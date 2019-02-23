<?php
namespace PHPJava\Kernel\Attributes;

use \PHPJava\Exceptions\NotImplementedException;
use \PHPJava\Kernel\Utilities\BinaryTool;

final class StackMapTableAttribute implements AttributeInterface
{
    use \PHPJava\Kernel\Core\BinaryReader;

    
    private $NumberOfEntries = null;
    
    private $StackMapFrames = array();
    
    public function execute(): void
    {
        
        
        
        $this->NumberOfEntries = $this->readUnsignedShort();
        
        for ($i = 0; $i < $this->NumberOfEntries; $i++) {
            
            $this->StackMapFrames[] = new JavaStructureStackMapFrame($Class);
            
            
        }
        
    }
    
    public function getStackMapFrames () {
        
        return $this->StackMapFrames;
        
    }
    
}
    