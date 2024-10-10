<?php
header("Content-type: text/html; charset=utf-8");

$codigo = isset($_POST["codigo"]) ? $_POST["codigo"] : "";

class Automato {
    private $estadoInicial;
    private $estadosFinais;
    private $transicoes;

    public function __construct($estadoInicial, $estadosFinais, $transicoes) {
        $this->estadoInicial = $estadoInicial;
        $this->estadosFinais = $estadosFinais;
        $this->transicoes = $transicoes;
    }

    public function executa($palavra) {
        $estadoAtual = $this->estadoInicial;

        for ($i = 0; $i < strlen($palavra); $i++) {
            $caracter = $palavra[$i];

            if (isset($this->transicoes[$estadoAtual][$caracter])) {
                $estadoAtual = $this->transicoes[$estadoAtual][$caracter];
            } else {
                return false;
            }
        }

        return in_array($estadoAtual, $this->estadosFinais);
    }
}

// Função para criar autômatos
function criaAutomatos() {
    // Autômato para palavras reservadas e operadores
    $palavrasReservadas = new Automato(
        'q0',
        ['q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'q90'], // Estados finais para palavras reservadas e WRITE
        [
            'q0' => [
                'i' => 'q1', 'I' => 'q1', // "if" e "IF"
                'e' => 'q10', 'E' => 'q10', // "else" e "ELSE"
                'w' => 'q20', 'W' => 'q20', // Início de "while" e "WRITE"
                'f' => 'q30', 'F' => 'q30', // "for" e "FOR"
                'p' => 'q40', 'P' => 'q40', // "print" e "PRINT"
                'v' => 'q50', 'V' => 'q50', // "var" e "VAR"
                'r' => 'q60', 'R' => 'q60', // "read" e "READ"
            ],
            
            'q1' => ['f' => 'q2', 'F' => 'q2'], // "if" e "IF"
            
            'q10' => ['l' => 'q11', 'L' => 'q11'],
            'q11' => ['s' => 'q12', 'S' => 'q12'],
            'q12' => ['e' => 'q3', 'E' => 'q3'], // "else" e "ELSE"
            
            'q20' => ['h' => 'q21', 'H' => 'q21', 'r' => 'q24', 'R' => 'q24'], // "while" segue este caminho
            'q21' => ['i' => 'q22', 'I' => 'q22'],
            'q22' => ['l' => 'q23', 'L' => 'q23'],
            'q23' => ['e' => 'q4', 'E' => 'q4'], // Estado final para "while"
    
            'q24' => ['i' => 'q25', 'I' => 'q25'],
            'q25' => ['t' => 'q26', 'T' => 'q26'],
            'q26' => ['e' => 'q9', 'E' => 'q9'], // Estado final para "write"
            
            'q30' => ['o' => 'q31', 'O' => 'q31'],
            'q31' => ['r' => 'q5', 'R' => 'q5'], // "for" e "FOR"
            
            'q40' => ['r' => 'q41', 'R' => 'q41'],
            'q41' => ['i' => 'q42', 'I' => 'q42'],
            'q42' => ['n' => 'q43', 'N' => 'q43'],
            'q43' => ['t' => 'q6', 'T' => 'q6'], // "print" e "PRINT"
            
            'q50' => ['a' => 'q51', 'A' => 'q51'],
            'q51' => ['r' => 'q7', 'R' => 'q7'], // "var" e "VAR"
            
            'q60' => ['e' => 'q61', 'E' => 'q61'],
            'q61' => ['a' => 'q62', 'A' => 'q62'],
            'q62' => ['d' => 'q8', 'D' => 'q8'], // "read" e "READ"
        ]
    );
    

    // Autômato para identificadores
    $identificador = new Automato(
        'q0',
        ['q1'],
        [
            'q0' => array_merge(array_fill_keys(range('a', 'z'), 'q1'), array_fill_keys(range('A', 'Z'), 'q1')),
            'q1' => array_merge(array_fill_keys(range('a', 'z'), 'q1'), array_fill_keys(range('A', 'Z'), 'q1'), array_fill_keys(range('0', '9'), 'q1')),
        ]
    );

    // Autômato para constantes numéricas
    $constante = new Automato(
        'q0',
        ['q1'],
        [
            'q0' => array_fill_keys(range('0', '9'), 'q1'),
            'q1' => array_fill_keys(range('0', '9'), 'q1'),
        ]
    );

    // Autômato para operadores e delimitadores
    $operadores = new Automato(
        'q0',
        ['q1', 'q3', 'q33', 'q100'],
        [
            'q0' => [
                '==' => 'q100', 
                '=' => 'q100', 
                '+' => 'q1', '-' => 'q1', '*' => 'q1', '/' => 'q1', '%' => 'q1',
                '(' => 'q1', ')' => 'q1', '[' => 'q1', ']' => 'q1', '{' => 'q1', '}' => 'q1', 
                '.' => 'q1', ',' => 'q1', ';' => 'q1', '!' => 'q1'
            ],
            'q100' => ['=' => 'q1']
        ]
    );

    return [
        'PALAVRARESERVADA' => $palavrasReservadas,
        'IDENTIFICADOR' => $identificador,
        'CONSTANTE' => $constante,
        'OPERADOR' => $operadores
    ];
}

// Função que realiza a análise léxica
function analisadorLexico($sourceCode) {
    $tokens = [];
    $erros = []; // Array para armazenar os erros
    $automatos = criaAutomatos();
    $length = strlen($sourceCode);
    $i = 0;

    // Iniciar contadores de linha e coluna
    $linha = 1;
    $coluna = 1;

    while ($i < $length) {
        $char = $sourceCode[$i];

        // Ignora espaços e novas linhas
        if (ctype_space($char)) {
            if ($char === "\n") {
                $linha++;
                $coluna = 1;
            } else {
                $coluna++;
            }
            $i++;
            continue;
        }

        $word = '';
        $startColuna = $coluna;

        // Tratamento de strings
        if ($char == '"') {
            $i++;
            $coluna++;
            while ($i < $length && $sourceCode[$i] != '"') {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }

            if ($i < $length && $sourceCode[$i] == '"') {
                $tokens[] = ['STRING', '"' . $word . '"'];
                $i++;
                $coluna++;
            } else {
                $erros[] = "Erro léxico: string não terminada na linha $linha, coluna $coluna."; // Armazenar o erro
            }
            continue;
        }

        // Verificar se é uma palavra alfabética
        if (ctype_alpha($char)) {
            while ($i < $length && ctype_alnum($sourceCode[$i])) {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }

            // Verificar se a palavra é reservada ou identificador
            $found = false;

            if ($automatos['PALAVRARESERVADA']->executa($word)) {
                $tokens[] = ['PALAVRARESERVADA', $word];
                $found = true;
            } elseif ($automatos['IDENTIFICADOR']->executa($word)) {
                $tokens[] = ['IDENTIFICADOR', $word];
                $found = true;
            }

            if (!$found) {
                $erros[] = "Erro léxico: token desconhecido '$word' na linha $linha, coluna $startColuna."; // Armazenar o erro
            }
        } 
        // Tratamento de números
        elseif (ctype_digit($char)) {
            while ($i < $length && ctype_digit($sourceCode[$i])) {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }
            $tokens[] = ['CONSTANTE', $word];
        } 
        // Tratamento de operadores e símbolos
        else {
            if ($char == '=') {
                while ($i < $length && $sourceCode[$i] == '=') {
                    $word .= $sourceCode[$i];
                    $i++;
                    $coluna++;
                }
            } else {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }

            $found = false;
            foreach ($automatos as $token => $automato) {
                if ($automato->executa($word)) {
                    $tokens[] = [$token, $word];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $erros[] = "Erro léxico: token desconhecido '$word' na linha $linha, coluna $startColuna."; // Armazenar o erro
            }
        }
    }

    if (!empty($erros)) {
        foreach ($erros as $erro) {
            echo "$erro\n"; // Exibir todos os erros
        }
    }

    return $tokens;
}
// Executar a análise léxica e mostrar os resultados
try {
    $tokens = analisadorLexico($codigo); // Certifique-se de que você está chamando a função correta
    echo "<h2>Tokens Encontrados:</h2><pre>";
    
    foreach ($tokens as $token) {
        // Descrição dos tokens
        $descricao = '';
        switch ($token[0]) {
            case 'PALAVRARESERVADA':
                switch ($token[1]) {
                    case 'if':
                        $descricao = 'Palavra Reservada: Condicional If: ' . $token[1];
                        break;
                    case 'IF':
                        $descricao = 'Palavra Reservada: Condicional If: ' . $token[1];
                        break;
                    case 'else':
                        $descricao = 'Palavra Reservada: Condicional Else: ' . $token[1];
                        break;
                    case 'ELSE':
                        $descricao = 'Palavra Reservada: Condicional Else: ' . $token[1];
                        break;
                    case 'while':
                        $descricao = 'Palavra Reservada: Laço While: ' . $token[1];
                        break;
                    case 'WHILE':
                        $descricao = 'Palavra Reservada: Laço While: ' . $token[1];
                        break;
                    case 'for':
                        $descricao = 'Palavra Reservada: Laço For: ' . $token[1];
                        break;
                    case 'FOR':
                        $descricao = 'Palavra Reservada: Laço For: ' . $token[1];
                        break;
                    case 'print':
                        $descricao = 'Palavra Reservada: Imprima: ' . $token[1];
                        break;
                    case 'PRINT':
                        $descricao = 'Palavra Reservada: Imprima: ' . $token[1];
                        break;
                    case 'var':
                        $descricao = 'Palavra Reservada: Declaração de variável: ' . $token[1];
                        break;
                    case 'VAR':
                        $descricao = 'Palavra Reservada: Declaração de variável: ' . $token[1];
                        break;
                    case 'read':
                        $descricao = 'Palavra Reservada: Leia: ' . $token[1];
                        break;
                    case 'READ':
                        $descricao = 'Palavra Reservada: Leia: ' . $token[1];
                        break;
                    case 'write':
                        $descricao = 'Palavra Reservada: Escreva: ' . $token[1];
                        break;
                    case 'WRITE':
                        $descricao = 'Palavra Reservada: Escreva: ' . $token[1];
                        break;
                    // Adicione mais palavras reservadas conforme necessário
                    default:
                        $descricao = 'Palavra Reservada: ' . $token[1];
                }
                break;

            case 'IDENTIFICADOR':
                $descricao = 'Identificador: ' . $token[1];
                break;

            case 'CONSTANTE':
                $descricao = 'Constante Numérica: ' . $token[1];
                break;

            case 'OPERADOR':
                switch ($token[1]) {
                    case '+':
                        $descricao = 'Operador de Adição: ' . $token[1];
                        break;
                    case '-':
                        $descricao = 'Operador de Subtração: ' . $token[1];
                        break;
                    case '*':
                        $descricao = 'Operador de Multiplicação: ' . $token[1];
                        break;
                    case '/':
                        $descricao = 'Operador de Divisão: ' . $token[1];
                        break;
                    case '=':
                        $descricao = 'Operador de Atribuição: ' . $token[1];
                        break;
                    case '==':
                        $descricao = 'Operador de Igualdade: ' . $token[1];
                        break;
                    case '(':
                        $descricao = 'Abre Parênteses: ' . $token[1];
                        break;
                    case ')':
                        $descricao = 'Fecha Parênteses: ' . $token[1];
                        break;
                    case '[':
                        $descricao = 'Abre colchetes: ' . $token[1];
                        break;
                    case ']':
                        $descricao = 'Fecha colchetes: ' . $token[1];
                        break;
                    case '{':
                        $descricao = 'Abre chaves: ' . $token[1];
                        break;
                    case '}':
                        $descricao = 'Fecha chaves: ' . $token[1];
                        break;
                    // Adicione mais operadores conforme necessário
                    default:
                        $descricao = 'Operador: ' . $token[1];
                }
                break;

            case 'STRING':
                $descricao = 'String: ' . $token[1];
                break;

            default:
                $descricao = 'Token Desconhecido: ' . $token[1];
        }
        
        echo "{$descricao}\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<h2>Erro:</h2><pre>{$e->getMessage()}</pre>";
}
?>