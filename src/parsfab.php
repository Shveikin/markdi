<?php

namespace markdi;

class parsfab {

    private $code;
    private $tokens = [];
    private $list = [];

    private $namespace = false;

    function parse($code){
        $this->code = $code;
        $tokens = \PhpToken::tokenize($this->code);
        $lux = [
            'open' => ['T_OPEN_TAG'], 
            'namespace' => ['T_NAMESPACE', ';'],
        ];
        $this->tokens = [];


        $activeLuxName = false;
        $findingTocken = false;

        foreach ($tokens as $token) {
            $tag = $token->getTokenName();

            if ($findingTocken){
                $this->tokens[$activeLuxName]['texts'][] = $token->text;
                if ($findingTocken==$tag)
                    $findingTocken = false;
            } else {
                foreach ($lux as $tokenName => $range) {
                    if ($range[0]==$tag){
                        $activeLuxName = $tokenName;

                        $this->tokens[$activeLuxName] = [
                            'tag' => $tag,
                            'from' => $token->pos,
                            'texts' => [$token->text],
                        ]; 

                        if (isset($range[1]))
                            $findingTocken = $range[1];
                    }
                }
            }
        }
    }

    function namespace($namespace, $className){
        if (isset($this->tokens['namespace'])){
            $this->tockerReplace('namespace', "namespace $namespace;");
        } else {
            if (isset($this->tokens['open'])){
                $this->tockerReplace('open', "<?php\nnamespace $namespace;\n");
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

        $text = implode('', $tag['texts']);
        $code = '';

        if ($tag['from']!=0)
            $code .= mb_substr($this->code, 0, $tag['from']);

        $code .= $replace;
        $code .= mb_substr($this->code, $tag['from'] + mb_strlen($text));

        $this->code = $code;
    }


    function getCode(){
        return $this->code;
    }

}