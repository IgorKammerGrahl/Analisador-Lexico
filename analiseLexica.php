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
    // Autômato para palavras reservadas
    $palavrasReservadas = new Automato(
        'q0',
        ['q2', 'q3', 'q4'], // Estados finais que indicam palavras reservadas
        [
            'q0' => ['i' => 'q1', 'e' => 'q10', 'w' => 'q20'],
            'q1' => ['f' => 'q2'],  // Reconhece "if"
            'q10' => ['l' => 'q11'],
            'q11' => ['s' => 'q12'],
            'q12' => ['e' => 'q3'],  // Reconhece "else"
            'q20' => ['h' => 'q21'],
            'q21' => ['i' => 'q22'],
            'q22' => ['l' => 'q23'],
            'q23' => ['e' => 'q4'],  // Reconhece "while"
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
        ['q1', 'q3', 'q120'],
        [
            'q0' => [
                '==' => 'q120', '=' => 'q120', 
                '+' => 'q1', '-' => 'q1', '*' => 'q1', '/' => 'q1', '%' => 'q1',
                '(' => 'q1', ')' => 'q1', '[' => 'q1', ']' => 'q1', '{' => 'q1', '}' => 'q1', 
                '.' => 'q1', ',' => 'q1', ';' => 'q1', '!' => 'q1'
            ],
            'q120' => ['=' => 'q1']
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
function lexer($sourceCode) {
    $tokens = [];
    $automatos = criaAutomatos();
    $length = strlen($sourceCode);
    $i = 0;
    
    // Iniciar contadores de linha e coluna
    $linha = 1;
    $coluna = 1;

    while ($i < $length) {
        $char = $sourceCode[$i];

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
                throw new Exception("Erro léxico: string não terminada na linha $linha, coluna $coluna.");
            }
            continue;
        }

        if (ctype_alpha($char)) {
            while ($i < $length && ctype_alnum($sourceCode[$i])) {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }
        } elseif (ctype_digit($char)) {
            while ($i < $length && ctype_digit($sourceCode[$i])) {
                $word .= $sourceCode[$i];
                $i++;
                $coluna++;
            }
        } else {
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
            throw new Exception("Erro léxico: token desconhecido '$word' na linha $linha, coluna $startColuna.");
        }
    }

    return $tokens;
}

// Executar a análise léxica e mostrar os resultados
// Executar a análise léxica e mostrar os resultados
try {
    $tokens = lexer($codigo); // Certifique-se de que você está chamando a função correta
    echo "<h2>Tokens Encontrados:</h2><pre>";
    
    foreach ($tokens as $token) {
        // Descrição dos tokens
        $descricao = '';
        switch ($token[0]) {
            case 'PALAVRARESERVADA':
                switch ($token[1]) {
                    case 'if':
                        $descricao = 'Palavra Reservada: Condicional If';
                        break;
                    case 'else':
                        $descricao = 'Palavra Reservada: Condicional Else';
                        break;
                    case 'while':
                        $descricao = 'Palavra Reservada: Laço While';
                        break;
                    case 'for':
                        $descricao = 'Palavra Reservada: Laço For';
                        break;
                    case 'function':
                        $descricao = 'Palavra Reservada: Declaração de Função';
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
