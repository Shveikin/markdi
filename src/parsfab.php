<?php

namespace markdi;

class parsfab {

    private $code;
    private $tokens = [];

    function parse($code){
        $this->code = $code;
        $tokens = \PhpToken::tokenize($this->code);
        $findLux = ['T_OPEN_TAG', 'T_NAME_QUALIFIED'];
        $this->tokens = [];

        foreach ($tokens as $token) {
            $tag = $token->getTokenName();
            
            if (in_array($tag, $findLux)){
                $this->tokens[$tag] = [
                    'from' => $token->pos,
                    'text' => $token->text,
                ];
            }
        }
    }

    function namespace($namespace, $className){
        if (isset($this->tokens['T_NAME_QUALIFIED'])){
            $this->tockerReplace('T_NAME_QUALIFIED', $namespace);
        } else {
            if (isset($this->tokens['T_OPEN_TAG'])){
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

        $code = '';

        if ($tag['from']!=0)
            $code .= mb_substr($this->code, 0, $tag['from']);

        $code .= $replace;
        $code .= mb_substr($this->code, $tag['from'] + mb_strlen($tag['text']));

        $this->code = $code;
    }


    function getCode(){
        return $this->code;
    }

}