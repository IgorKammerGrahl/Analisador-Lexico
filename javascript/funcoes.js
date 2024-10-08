console.log("funcoes.js carregado");

function GetObject(obj){
    if(document.getElementById){
		return document.querySelector('#' + obj);
    }
}

function showLoadBar(show){
    if(show){
		console.log("showLoadBar foi chamada, show: ", show);
        GetObject("wait").style.display = "";
        GetObject("codigo").disabled = "true";
    }else{
        GetObject("wait").style.display = "none";
        GetObject("codigo").disabled = "";
    }
}

function ajaxInit(){
	
	var http_request = false;

	if (window.XMLHttpRequest) {
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
        	http_request.overrideMimeType('text/html');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				http_request = false;
			}
		}
	}
	return http_request;
}

function efetuaAnaliseLexica(codigo){

	showLoadBar(true);
	
	var http_request = ajaxInit();
	
	if (!http_request) {
		return false;
	}

	http_request.open("POST", "analiseLexica.php", true);
	http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=iso-8859-1");
	http_request.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
	http_request.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
	http_request.setRequestHeader("Pragma", "no-cache");
	
	http_request.send("codigo="+encodeURIComponent(codigo));
	
	http_request.onreadystatechange = function(){
		if(http_request.readyState == 4) {
			if(http_request.responseText){
				GetObject("status").innerHTML = http_request.responseText;
				showLoadBar(false);
			}else{
				showLoadBar(false);
				return false;
			}
		}
	}
	return true;
}

function analisar(codigo){
	if(!efetuaAnaliseLexica(codigo)){
		alert("Erro ao efetuar análise.\nTalvez seu navegador não tenha suporte a Ajax.");
	}
}