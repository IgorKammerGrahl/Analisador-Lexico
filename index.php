<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Analisador Léxico</title>
    <link href="estilos/estilo.css" rel="stylesheet" type="text/css" />
    <script src="javascript/funcoes.js" type="text/javascript"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        #corpo {
            width: 800px;
			height : 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #titulo {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        #entrada,
        #saida {
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none; /* Desabilita redimensionamento */
        }

        #status {
            background: #e9ecef;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow: auto; /* Permite rolagem quando necessário */
            white-space: pre-wrap; /* Mantém quebras de linha */
        }

        #controles {
            text-align: center;
        }

        #botao {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #botao:hover {
            background-color: #0056b3; /* Efeito hover */
        }
    </style>
</head>

<body>

<div id="wait" style="display: none;">
    <b>Analisando seu Código...</b>
</div>

<div id="corpo">
    <div id="subcorpo">
    
        <div id="titulo">
            Analisador Léxico
        </div>
        
        <div id="entrada">
            Código: 
            <textarea name="codigo" id="codigo"></textarea>
        </div>

        <div id="saida">
            Resultado: 
            <div id="status"></div>
        </div>
        
    </div>
    
    <div id="controles">
		<br><br>
        <input id="botao" type="button" name="analisar" value="Analisar Lexicamente" onClick="analisar(GetObject('codigo').value);">
    </div>
</div>

</body>
</html>
