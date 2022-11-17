<?php

namespace markdi;

class parsfab {

    private $code;
    private $tokens = [];

    function parse($code){
        $this->code = $code;
        $tokens = \PhpToken::tokenize($this->code);
        $findLux = ['T_OPEN_TAG', 'T_NAMESPACE'];
        $this->tokens = [];
        $lastTag = false;

        foreach ($tokens as $token) {
            $tag = $token->getTokenName();
            
            if (in_array($tag, $findLux)){
                if (!isset($thistokenstockens[$tag]))
                    $this->tokens[$tag] = [];

                $this->tokens[$tag]['from'] = $token->pos;
            }
            if (in_array($lastTag, $findLux)){
                $this->tokens[$lastTag]['to'] = $token->pos;
                $this->tokens[$lastTag]['text'] = mb_substr(
                    $this->code,
                    $this->tokens[$lastTag]['from'], 
                    $this->tokens[$lastTag]['from'] - $this->tokens[$lastTag]['to'], 
                );
            }

            $lastTag = $tag;
        }
    }

    function namespace($namespace, $className){
        if (isset($thistokenstockens['T_NAMESPACE'])){
            $this->tockerReplace('T_NAMESPACE', "namespace $namespace");
        } else {
            if (isset($thistokenstockens['T_OPEN_TAG'])){
                $this->tockerReplace('T_OPEN_TAG', "<?php\nnamespace $namespace;\n");
            } else {
                $this->code .= <<<CODE
                <?php

                namespace $namespace;

                class $className {

                }
                CODE;
            }
        }
    }



    function tockerReplace(string $tocken, string $replace){
        $tag = $this->tokens[$tocken];

        $code = 
            mb_substr($this->code, 0, $tag['from']) . 
            $replace .
            mb_substr($this->code, $tag['from'] + $tag['to']);

        $this->code = $code;
    }


    function getCode(){
        return $this->code;
    }

}