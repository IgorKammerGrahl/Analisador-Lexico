<?php
header("Content-type: text/html; charset=utf-8");

$codigo = isset($_POST["codigo"]) ? $_POST["codigo"] : "";

function verificaCaracter($caracter, $apenasLetras){
    for($i=65; $i<=90; $i++){
        if(strtoupper($caracter) == chr($i)){
            return true;
        }
    }

    if(!$apenasLetras){
        for($i=48; $i<=57; $i++){
            if($caracter == chr($i)){
                return true;
            }
        }

        if($caracter == "_"){
            return true;
        }
    }

    return false;
}

function reservadas(){
    return array("auto", "break", "case", "char", "const", "continue", "default", "do", "double", "else",
                 "enum", "extern", "float", "for", "goto", "if", "int", "long", "register", "return", 
                 "short", "signed", "sizeof", "static", "struct", "switch", "typedef", "union", 
                 "unsigned", "void", "volatile", "while", "asm", "cdecl", "far", "fortran", "huge", 
                 "interrupt", "near", "pascal", "typeof");
}

function operadores(){
    return array("+" => "matemático de soma",
                 "-" => "matemático de subtração",
                 "*" => "matemático de multiplicação",
                 "/" => "matemático de divisão",
                 "=" => "de atribuição",
                 "==" => "lógico de igualdade",
                 "!=" => "lógico de diferença",
                 "%" => "matemático de percentual",
                 ">" => "lógico, indicando maior",
                 "<" => "lógico, indicando menor",
                 "<=" => "lógico, indicando menor ou igual",
                 ">=" => "lógico, indicando maior igual",
                 "&&" => "relacional, indicando \"e\"",
                 "||" => "relacional, indicando \"ou\"");
}

function ehNumero($caracter){
    return $caracter >= '0' && $caracter <= '9';
}

function ehLetra($caracter){
    return ($caracter >= 'a' && $caracter <= 'z') || ($caracter >= 'A' && $caracter <= 'Z');
}

function analiseLexica($codigo){
    $reservadas = reservadas();
    $operadores = operadores();
    $resultados = [];
    $token = "";
    $comprimento = strlen($codigo);
    
    for($i = 0; $i < $comprimento; $i++){
        $caracter = $codigo[$i];
        
        if($caracter == ' ' || $caracter == "\n" || $caracter == "\t"){
            continue;
        }

        if(ehLetra($caracter) || $caracter == '_'){
            $token = $caracter;
            while($i + 1 < $comprimento && (ehLetra($codigo[$i + 1]) || ehNumero($codigo[$i + 1]) || $codigo[$i + 1] == '_')){
                $token .= $codigo[++$i];
            }

            if(in_array($token, $reservadas)){
                $resultados[] = "<b>$token</b> é uma palavra reservada.";
            } else {
                $resultados[] = "<b>$token</b> pode ser o nome de uma variável ou procedimento.";
            }
            continue;
        }

        if(ehNumero($caracter)){
            $token = $caracter;
            while($i + 1 < $comprimento && ehNumero($codigo[$i + 1])){
                $token .= $codigo[++$i];
            }

            $resultados[] = "<b>$token</b> é um número.";
            continue;
        }

        if(array_key_exists($caracter, $operadores)){
            $token = $caracter;
            if($i + 1 < $comprimento && array_key_exists($caracter . $codigo[$i + 1], $operadores)){
                $token .= $codigo[++$i];
            }

            $resultados[] = "<b>$token</b> é um operador " . $operadores[$token] . ".";
            continue;
        }

        if(in_array($caracter, ['(', ')', '{', '}', '[', ']'])){
            $resultados[] = "<b>$caracter</b> é um delimitador.";
            continue;
        }

        $resultados[] = "<b>$caracter</b> causará erro de sintaxe.";
    }

    return implode("<br>", $resultados);
}

echo analiseLexica($codigo);
?>
