<?php /*
			Criado por Renan Denadai (renan@ipeuna.com)
*/?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<title>MMO RPG</title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/socket.io.js"></script>
	</head>
	<body id="tagBody" style="margin: 0px;background-color: #000;color: white;" oncontextmenu="return false;">

	
	<canvas id="canvasMapa" width="800" height="500" style="position: absolute;"></canvas>
	<canvas id="canvasPersonagem" width="800" height="500" style="position: absolute;"></canvas>
	<canvas id="canvasTexto" width="800" height="500" style="position: absolute;"></canvas>
	
	
	<div style="background-color: #000;opacity:0.50;-moz-opacity: 0.50;filter: alpha(opacity=50);position: absolute;left: 0px;right: 0px;top: 0px;height: 60px;color:#CCC;font-family: Verdana,Arial;font-size: 11px;padding: 5px;">
		<div style="width: 200px;float: right;text-align: right;margin: 10px;color: #666;">
			Velocidade: <span id="velocidade"></span> | <span id="fps"></span><br/>
			<span id="cordXY"></span><br/>
			<span id="console"></span>
		</div>
		
		  <input id="musicaCheck" name="nameMusicaCheck" type="checkbox" checked="checked" onchange="verificaAudio()" /> Música  | Players Online: <span id="playerOnline">0</span> <br/>
		Tempo Render por frame: <span id="tempoFrameRender"></span>ms<br/>
		Demo: <a onclick="montaria=0;">sem montaria</a> | <a onclick="montaria=1;moveVeloMontaria=3;">montaria 1</a> | <a onclick="montaria=2;moveVeloMontaria=4;">montaria 2</a>
		
	</div>
	
	<style>
		table tr td{
			margin: 0px;
			padding: 3px;
			border: 1px solid #333;
			background-color: #000;
			width: 27px;
			height: 27px;
			text-align: right;
			vertical-align: top;
			color: #444;
		}
	
	</style>
	
	<div align="center" style="position: absolute;bottom: 0px;left: 50%;margin-left: -300px;width:600px;opacity:0.70;-moz-opacity: 0.70;filter: alpha(opacity=70);">
	
	<input id="inputChat" type="text" autocomplete="off" maxlength="45" style="border: 1px solid #333;background-color: #000;color:#FFF;width: 300px;">
	
		<table style="font-size: 9px;">
			<tr>
				<td>1</td>
				<td>2</td>
				<td>3</td>
				<td>4</td>
				<td>5</td>
				<td>6</td>
				<td>7</td>
				<td>8</td>
				<td>9</td>
				<td>0</td>
			</tr>
		</table>
		
		<table  style="font-size: 9px;">
			<tr>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px; border-color: #ffcc00;" equipa="1"><div><img id="drag1" equipa="1" draggable="true" ondragstart="drag(event)" src="img/item/adaga.png"  /></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px; border-color: #ffcc00;" equipa="1"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div></div></td>
				<td ondrop="drop(event)" ondragover="allowDrop(event)" style="width: 29px;height:29px;"><div><img id="drag2" equipa="0" draggable="true" ondragstart="drag(event)" src="img/item/gold.png"  /></div></td>
			</tr>
		</table>
		
		<script>
		function allowDrop(ev) {
		    ev.preventDefault();
		}
		
		function drag(ev) {
		    ev.dataTransfer.setData("text", ev.target.id);
		}
		
		function drop(ev) {
		    ev.preventDefault();
		    var data = ev.dataTransfer.getData("text");
		    ev.target.appendChild(document.getElementById(data));
		}
	</script>
	
	</div>

	<script type="text/javascript">

		
		var nickname = window.navigator.appVersion;
		
		var socket = io.connect('http://127.0.0.1:8080');
		var canvasMapa = document.getElementById('canvasMapa');
		var contextMapa = canvasMapa.getContext('2d');

		var canvasPersonagem = document.getElementById('canvasPersonagem');
		var contextPersonagem = canvasPersonagem.getContext('2d');

		var canvasTexto = document.getElementById('canvasTexto');
		var contextTexto = canvasTexto.getContext('2d');

		var updateMapaRespostaSegundos = 1;
		var fpsLimite=20;
		var moveVeloInicial=3.4;
		var moveVelo = moveVeloInicial;

		//montaria inicial
		var montaria = 2; //id da montaria
		var moveVeloMontaria=4; //velocidade
		var contAnimeMont = 0;
		var contAnimeMontSen = true;
		
		var str="";
		var mapa;
		var timestamp;
		var timestampOld;
		var fps=0;
		var timeout;
		var first = true;

		var contAnimed=0;
		var contAnimedFps=0;
		var animedPause = true;
		var players = new Array();
		var playerId;
		var updatePosPlayer = new Array;
		var upPosMapAr = new Array();
		var firstTileMove = 0;
		var restoXp = 0;
		var restoYp = 0;
		var posMoveXcolisao = 0;
		var posMoveYcolisao = 0;
		var posMoveX = 0;
		var posMoveY = 0;
		var posMoveTotalXold = 0;
		var posMoveTotalYold = 0;
		var posMoveTotalX = 0;
		var posMoveTotalY = 0;
		var direcaoMouse = 7;
		var mousex = 0;
		var mousey = 0;
		var mousepress = false;
		var tileAr = new Array();
		
		var telax;
		var telay;

		 // tamanho da tela
		// mozilla/netscape/opera/IE7
		if (typeof window.innerWidth != 'undefined'){
		     telax = window.innerWidth,
		     telay = window.innerHeight
		} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0){//IE6
		      telax = document.documentElement.clientWidth,
		      telay = document.documentElement.clientHeight
		}else{ // older versions of IE
		      telax = document.getElementsById("tagBody")[0].clientWidth,
		      telay = document.getElementsById("tagBody")[0].clientHeight
		}

		//barra preta abaixo
		telay += -60;

		function render() {
			timeout = setTimeout(render, 1000/fpsLimite);

			//contador fps
			timestamp = +new Date;
			if(timestamp-timestampOld>1000 || fps==0){
				timestampOld=+new Date;
				document.getElementById("fps").innerHTML=Math.round(fps)+" fps";
				
				firstTileMove++;

				if(montaria>0)
					moveVeloMontariaTemp=moveVeloMontaria;
				else
					moveVeloMontariaTemp=0;

				//corrige velocidade conforme fps
				if(fps>1)
					moveVelo = (moveVeloInicial*(fpsLimite-fps)/fpsLimite)+moveVeloInicial+moveVeloMontariaTemp;
				document.getElementById("velocidade").innerHTML=moveVelo;

				fps=0;

				// atualiza mapa a cada X segundos
				if(upMapaLiberado)
					server.atualizaMapa();

				//players online
				document.getElementById("playerOnline").innerHTML=Object.keys(players).length;
			}
			fps++;

			

			// Old colisao
			if(posMoveXcolisao == posMoveX && posMoveYcolisao == posMoveY)
				animedPause = true;
			else
				animedPause = false;
			posMoveXcolisao = posMoveX;
			posMoveYcolisao = posMoveY;
			posMoveTotalXcolisao = posMoveTotalX;
			posMoveTotalYcolisao = posMoveTotalY;

			//move mouse
			if(mousepress){
				if(mousex<telax*2/5 && mousey<telay*2/5){ // 1 esquerda cima
					posMoveX+=2*moveVelo;
					posMoveY+=1*moveVelo;
					posMoveTotalX+=2*moveVelo;
					posMoveTotalY+=1*moveVelo;
					direcaoMouse = 1;
					
					document.getElementById("tagBody").style.cursor="url('img/cursor/1.png'),poiter";
				}else if(mousex>=telax*2/5 && mousex<=telax*3/5 && mousey<telay*2/5){ // 2 cima
					posMoveY+=2*moveVelo;
					posMoveTotalY+=2*moveVelo;
					direcaoMouse = 2;

					document.getElementById("tagBody").style.cursor="url('img/cursor/2.png'),poiter";
				}else if(mousex>telax*3/5 && mousey<telay*2/5){ // 3 direita cima
					posMoveX-=2*moveVelo;
					posMoveY+=1*moveVelo;
					posMoveTotalX-=2*moveVelo;
					posMoveTotalY+=1*moveVelo;
					direcaoMouse = 3;

					tileMoveSomaX = 0;
					tileMoveSomaY = 0;
					
					document.getElementById("tagBody").style.cursor="url('img/cursor/3.png'),poiter";
				}else if(mousex<telax*2/5 && mousey>telay*2/5 && mousey<telay*3/5){ // 4 esquerda
					posMoveX+=3*moveVelo;
					posMoveTotalX+=3*moveVelo;
					direcaoMouse = 4;

					document.getElementById("tagBody").style.cursor="url('img/cursor/4.png'),poiter";
				}else if(mousex>telax*3/5 && mousey>telay*2/5 && mousey<telay*3/5){ // 5 direita
					posMoveX-=3*moveVelo;
					posMoveTotalX-=3*moveVelo;
					direcaoMouse = 5;

					document.getElementById("tagBody").style.cursor="url('img/cursor/6.png'),poiter";
				}else if(mousex<telax*2/5 && mousey>telay*3/5){ // 6 baixo esquerda
					posMoveX+=2*moveVelo;
					posMoveY-=1*moveVelo;
					posMoveTotalX+=2*moveVelo;
					posMoveTotalY-=1*moveVelo;
					direcaoMouse = 6;

					document.getElementById("tagBody").style.cursor="url('img/cursor/7.png'),poiter";
				}else if(mousex>telax*2/5 && mousex<telax*3/5 && mousey>telay*3/5){ //7 baixo
					posMoveY-=2*moveVelo;
					posMoveTotalY-=2*moveVelo;
					direcaoMouse = 7;

					document.getElementById("tagBody").style.cursor="url('img/cursor/8.png'),poiter";
				}else if (mousex>telax*3/5 && mousey>telay*3/5){ // 8 baixo direita
					posMoveX-=2*moveVelo;
					posMoveY-=1*moveVelo;
					posMoveTotalX-=2*moveVelo;
					posMoveTotalY-=1*moveVelo;
					direcaoMouse = 8;

					document.getElementById("tagBody").style.cursor="url('img/cursor/9.png'),poiter";
				}else{
					animedPause = true;
					document.getElementById("tagBody").style.cursor="url('img/cursor/default.png'),poiter";
				}
			}else{
				document.getElementById("tagBody").style.cursor="url('img/cursor/default.png'),poiter";
				animedPause = true;
			}


			if(montaria>0)
				animaVeloPers=2;
			else
				animaVeloPers=3;
			
			//personagem
			if(contAnimedFps>=animaVeloPers){
				contAnimed++;
				contAnimedFps=0;

				//jQuery("input").val(contAnimed+1);
			}
			if (animedPause){
				imgPersImg = "hParado";
				imgPers = img[imgPersImg];
				contAnimed=0;
			}else{
				imgPersImg = "hAndando";
				imgPers = img[imgPersImg];
				contAnimedFps++;
				if(contAnimed>=8)
					contAnimed=0;
			}

			contextPersonagem.clearRect(0, 0, contextPersonagem.canvas.width, contextPersonagem.canvas.height);

			// anima personagem na montaria
			if(animedPause || montaria==0){
				contAnimeMont=0;
			}else{
				if(contAnimeMontSen){
					contAnimeMont++;
					if(contAnimeMont>=3)
						contAnimeMontSen = false;
				}else{
					contAnimeMont--;
					if(contAnimeMont<=0)
						contAnimeMontSen = true;
				}
			}

			contAnimedPers=contAnimed;

			

			
			
			// montarias
			if(montaria>0){

				contextPersonagem.drawImage(img["montaria"+montaria],168*contAnimed+1,148*(direcaoMouse-1),168,148,telax/2-82,telay/2-45,168,148);

				imgPersImg = "hAndando";
				imgPers = img[imgPersImg];
				contAnimedPers=8;
			}


			//Envia posição para o servidor do player
			if(!animedPause || first){
				updatePosPlayer[0] = playerId;
				updatePosPlayer[1] = posMoveTotalX;
				updatePosPlayer[2] = posMoveTotalY;
				updatePosPlayer[3] = contAnimedPers;
				updatePosPlayer[4] = (direcaoMouse-1);
				updatePosPlayer[5] = imgPersImg;
				updatePosPlayer[6] = montaria;
				updatePosPlayer[7] = contAnimeMont;
				updatePosPlayer[8] = contAnimed;
				server.envia("updatePosicaoPlayer",updatePosPlayer);
			}
			
			// monta player e texto
			
			contextPersonagem.drawImage(imgPers,128*contAnimedPers,128*(direcaoMouse-1),128,128,telax/2-64,telay/2-64+contAnimeMont,128,128);
			contextTexto.clearRect(0, 0, contextTexto.canvas.width, contextTexto.canvas.height);
			for(var i in players){

				//mostra players
				if(players[i].nickname!=nickname && players[i].nickname!=null){

					posXArredondaPlayers = Math.round(((players[i].width*-1)+telax/2-64)+posMoveTotalX);
					posYArredondaPlayers = Math.round(((players[i].height*-1)+telay/2-64)+posMoveTotalY);

					if(players[i].montaria>0)
						contextPersonagem.drawImage(img["montaria"+players[i].montaria],168*players[i].contAnimed+1,148*players[i].cropH,168,148,posXArredondaPlayers-20,posYArredondaPlayers+20,168,148);
						
					
					contextPersonagem.drawImage(img[players[i].img],128*players[i].cropW,128*players[i].cropH,128,128,posXArredondaPlayers,posYArredondaPlayers+players[i].contAnimeMont,128,128);
				}

				//mostra texto
				if(chatAr[players[i].nickname]!= null && timestamp-chatAr[players[i].nickname].time<chatAr[players[i].nickname].chatmsg.length*200+1000){
					contextTexto.font="13px Georgia";
					contextTexto.fillStyle = 'white';
					contextTexto.textAlign = 'center';

					if(players[i].nickname==nickname)
						contextTexto.fillText(chatAr[players[i].nickname].chatmsg,telax/2,telay/2-40+contAnimeMont,200);
					else
						contextTexto.fillText(chatAr[players[i].nickname].chatmsg,Math.round(((players[i].width*-1)+telax/2)+posMoveTotalX),Math.round(((players[i].height*-1)+telay/2-40)+posMoveTotalY),200);
				}

				
			}

			// monta mapa
			for(var h=0;mapa.layers[h];h++){


				//corta imagem para os tiles
				if(first){
					imageObj[h] = img[mapa.layers[h].name];
					tile = mapa.tilesets[h].firstgid-1;
					tileAr[h] = new Array();
					for(y=0;y<mapa.tilesets[h].imageheight;y=y+mapa.tilesets[h].tileheight){
						for(x=0;x<mapa.tilesets[h].imagewidth;x=x+mapa.tilesets[h].tilewidth){
							
							tile++;		
							tileAr[h][tile]= {width:x,height:y};
						}
					}
				}

				linha=0;
				coluna=0;

				//monta mapa
				for(var i in mapa.layers[h].data){

					//Processa posição do mapa
					if (i%mapa.width==0){
						posX = -32*i/mapa.width+posXInicial+posMoveX;
						posY = 16*i/mapa.width+posYInicial+posMoveY;

						linha=0;
						coluna++;
						
						//correcao inicio tile construcao
						if(h==3)
							posY -=336;
						else if(h==2)
							posY -=20;
						else if(h==0){
							posX -=10;
							posY -=26;

						}
					}
					linha++;

					
					posX =  Math.round(posX+32);
					posY =  Math.round(posY+16);

					// não desenha as quinas do mapa
					if(linha<=mapa.width/2-coluna || linha>=mapa.width/2+coluna || (coluna>=mapa.width/2 && linha<coluna-mapa.width/2) || (coluna>mapa.width/2 && linha>mapa.width-(coluna-mapa.width/2)))
						continue;
					
					// se não tiver tile continua
					if(mapa.layers[h].data[i]==0)
						continue;

					//colisao
					if(h==0){

						//radio do personagem
						radioPers = 50;
						if(telax/2-64-radioPers<posX-30 && telax/2-64+radioPers>posX-30 && telay/2-64-radioPers/2<posY-65 && telay/2-64+radioPers/2>posY-65){
							posMoveX = posMoveXcolisao;
							posMoveY = posMoveYcolisao;
							posMoveTotalX = posMoveTotalXcolisao;
							posMoveTotalY = posMoveTotalYcolisao;
						}

						//pula layer colisão
						continue;
					}


					// Não monta mapa quando estiver parado
					if(animedPause && !first)
						return;

					contextMapa.drawImage(imageObj[h],tileAr[h][mapa.layers[h].data[i]].width,tileAr[h][mapa.layers[h].data[i]].height,mapa.tilesets[h].tilewidth,mapa.tilesets[h].tileheight,posX,posY,mapa.tilesets[h].tilewidth,mapa.tilesets[h].tileheight);
				}

			}

		  	//mostra cordenadas
			document.getElementById("cordXY").innerHTML = Math.round(posMoveTotalX)+"px ,"+Math.round(posMoveTotalY)+"px";

			document.getElementById("tempoFrameRender").innerHTML = (+new Date)-timestamp;
			
			first = false;
		}


		var upMapaLiberado = true;


		var server = new Object();

		server = {
			status: true,
			playersOnline: 0,
			time: 0
		}
		
		server.envia = function(nome,data){
			setTimeout(function(){
				socket.emit(nome,data);
			}, 0);
		}

		server.atualizaMapa = function(){

			upMapaLiberado=false;
			
			YpTemp = (posMoveTotalX*-0.447213595499958+posMoveTotalY*-1*0.894427190999916)/28.6216701119973;
			XpTemp = (posMoveTotalX*0.894427190999916-posMoveTotalY*-1*-0.447213595499958)/35.7770876399966+0.6*YpTemp;

			upPosMapAr[0] = parseInt(XpTemp);
			upPosMapAr[1] = parseInt(YpTemp);
			
			upPosMapAr[2] = nickname;

			XpTemp = XpTemp - upPosMapAr[0];
			YpTemp = YpTemp - upPosMapAr[1];

			restoXp = 35.7770876399966*0.894427190999916*(XpTemp-0.6*YpTemp)+28.6216701119973*-0.447213595499958*YpTemp;
			restoYp = 35.7770876399966*-0.447213595499958*(XpTemp-0.6*YpTemp)-28.6216701119973*0.894427190999916*YpTemp;

			posMoveTotalXold = posMoveTotalX;
			posMoveTotalYold = posMoveTotalY;

			document.getElementById("console").innerHTML = upPosMapAr[0]+"t ,"+upPosMapAr[1]+"t";

			server.envia("updatePosicaoMapa",upPosMapAr);
			firstTileMove=0;
		}


		function updateMapaResposta(){

			//posição inicial é o centro da tela + resto de tile fracionado
			posXInicial = telax/2 + restoXp + posMoveTotalX - posMoveTotalXold;
			posYInicial = telay/2 - mapa.height*16 + restoYp + posMoveTotalY - posMoveTotalYold;
			//limpar resto de tile fracionado
			restoXp = 0;
			restoYp = 0;
			
			posX = posXInicial;
			posY = posYInicial;

			posMoveX= 0;
			posMoveY= 0;

			upMapaLiberado=true;
			
			//limpa mapa
			//canvas.width = canvas.width; // ou context.clearRect(0, 0, 2000, 2000);
			clearTimeout(timeout);
			render();
		}

		socket.on('loginResposta', function (data) {
			playerId = data;
			iniciaAudio();
	    });

		socket.on('mapaUpdate', function (json) {
			mapa = json;
			updateMapaResposta();
	    });

		socket.on('updatePosicaoPlayerResposta', function (data) {
			players = data;
	    });

		// login apos carregar todas as imagens
		//imagens
		var img = new Array();
		var imageObj = new Array();
		var source = {hAndando:"img/hAndando.png",hParado:"img/hParado.png",montaria1:"img/montaria1.png",montaria2:"img/montaria2.png",piso:"img/piso.png",construcoes:"img/construcoes.png",incrementos:"img/incrementos.png"};

		var srcCont = 0;
		var srcContTotal=0;
		for(var src in source){
			srcContTotal++;
		}
		for(var src in source){
			img[src] = new Image();
			img[src].src = source[src];
			img[src].onload = function() {
				srcCont++;
				if(srcCont==srcContTotal)
					server.envia("login",nickname);
			}
		}
		
		// eventos do mouse
	    function getMousePos(canvas, evt) {
	    	var rect = canvasTexto.getBoundingClientRect();
	        return {
	        	x: evt.clientX - rect.left,
	        	y: evt.clientY - rect.top
	        };
	    }

		canvasTexto.addEventListener('mousemove', function(evt) {
	    	var mousePos = getMousePos(canvasTexto, evt);
	        mousex = mousePos.x;
	        mousey = mousePos.y;
		}, false);

		
		//mobile fullscreen
		window.scrollTo(0,1);

		//mouse 
		canvasTexto.addEventListener("mousedown", function(e){
		 	if(e.button === 2)
		 		mousepress=true;
		}, false);
	
		canvasTexto.addEventListener("mouseup", function(e){
		 	if(e.button === 2)
		 		mousepress=false;
		}, false);

		//touch 
		canvasTexto.addEventListener("touchmove", function(e){

			//no scroll
			if(e.target.id != 'slider_id')
				e.preventDefault();
			
			mousex = e.changedTouches[0].pageX;
			mousey = e.changedTouches[0].pageY;
		 	mousepress=true;
		}, false);
	
		canvasTexto.addEventListener("touchend", function(e){
		 	mousepress=false;
		}, false);

		//chat
		$(document).keypress(function(e) {
			if ( e.which == 13 ) {
				chatmsg = document.getElementById("inputChat").value;
				if(chatmsg.length>0){
					server.envia("updateChat",{nickname:nickname,chatmsg:chatmsg});
					chatmsg = document.getElementById("inputChat").value="";
					
				}
				document.getElementById("inputChat").focus();
			}
		});


		//atualiza chat
		var chatAr=new Array();
		socket.on('updateChatResposta', function (data) {
			chatAr[data.nickname]={chatmsg:data.chatmsg,time:+new Date};
	    });

		// Canvas FullScreen
		jQuery("canvas").attr("width",telax).attr("height",telay+60); // barra preta abaixo

		//cookie
		function setCookie(cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+d.toGMTString();
		    document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		function getCookie(cname) {
		    var name = cname + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0; i<ca.length; i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1);
		        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
		    }
		    return "";
		}

    </script>
    
   <audio id="player">
	  <source src="musicas/1.mp3" type="audio/mpeg">
	</audio>
	
	
	<script type="text/javascript">
		//audio
		var audio = jQuery("#player")[0];
		audio.volume=0.2;
		//audio.loop=true;
		audio.src = "musicas/"+(Math.floor(Math.random() * 4) + 1)+".mp3";

		function iniciaAudio(){
			if(getCookie("musica")==""){
				setCookie("musica","true",1);
				audio.play();
			}else{
				if(getCookie("musica")=="true"){
					audio.play();
					jQuery("#musicaCheck").attr('checked','checked');
				}else{
					audio.pause();
					jQuery("#musicaCheck").removeAttr('checked');
				}
			}
		}
		
		function verificaAudio(){
			if(jQuery("#musicaCheck").is(':checked')){
				audio.play();
				setCookie("musica","true",1);
			}else{
				audio.pause();
				setCookie("musica","false",1);
			}
		}
		
		audio.addEventListener("ended", function() {
		    audio.src = "musicas/"+(Math.floor(Math.random() * 4) + 1)+".mp3";
		    audio.play();
		});
	</script>


	</body>
</html>