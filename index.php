<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
	Analisador Léxico
</title>
<link href="estilos/estilo.css" rel="stylesheet" type="text/css" />
<script src="javascript/funcoes.js" type="text/javascript"></script>
</head>

<body topmargin="0" leftmargin="0">

<div id="wait" style="display: none;">
	<b>Analisando seu Código...</b>
</div>

<div id="corpo">
	<div id="subcorpo">
	
		<div id="titulo">
			Analisador Léxico
		</div>
		
		<div id="entrada">
			Código: <input type="text" name="codigo" id="codigo" maxlength="100">
		</div>
		
		<div id="saida">
			Resultado: 
			<div id="status"></div>
		</div>
		
	</div>
	
	<div id="controles">
		<input id="botao" type="button" name="analisar" value="Analisar Lexicamente" onClick="analisar(GetObject('codigo').value);">
	</div>
</div>
</body>
</html>