<?php

namespace markdi;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

class nsconv extends \PhpParser\NodeVisitorAbstract {

    function __construct($namespace){
        $this->namespace = $namespace;
    }

    public function leaveNode(Node $node) {
        if ($node instanceof Class_){
            $ns = $node->getProperties('namespace');
            if ($ns){

            } else {
                $node->addProperty('namespace');
            }
            echo "ns $ns";
        }

        // if ($node instanceof Node\Name) {
        //     return new Node\Name($this->namespace);
        // }
    }
}