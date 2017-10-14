// Inicio: 28/06/2014

var io = require('socket.io').listen(8080);
var inicioMapX = 150; // Y > que a metade do playerMapAlcance
var inicioMapY = 190; // X > que a metade do playerMapAlcance
var playerMapAlcance = 80; // Só pode números par -> definir o numero apartir do tamanho da tela de cada usuario
var players = {};
var playersIds = {};
var chatAr={};
var mapaJSon = "";
var mapaAr = new Array({},{},{},{}); // atualmente 4 camadas: {} {} {} {}
var json = "";
//load map
var fs = require('fs');
var file = '../mapa.json';
fs.readFile(file, 'utf8', function (err, data) {
	if (err) {
	    console.log('Error: ' + err);
	    return;
	}
	
	//tirar quebras de linhas e espaços aqui para o arquivo ficar menor na hora de enviar para o usuário
	json = JSON.parse(data);
	
	for(var i=0;json.layers[i];i++){
		
		first=0;
		for(var j=1;j<=json.height;j++){//divite mapa em colunas em um array
			
			mapaAr[i][j] = json.layers[i].data.slice((json.width*(first*(j-1)))+1, json.width*j);
			first=1;
		}
	}
	
});

// gerar id player
function randomString(length) {
	chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
    return result;
}

io.sockets.on('connection', function (socket) {

	function cortaMapa(mapaAr,playerInicioX,playerInicioY,json){
		
		for(var i=0;json.layers[i];i++){
			var mapaArCortado = new Array();
			for(var k=0;k<playerMapAlcance;k++){
				mapaArCortado = mapaArCortado.concat(mapaAr[i][playerInicioX-playerMapAlcance/2+k].slice(playerInicioY-playerMapAlcance/2,playerInicioY+playerMapAlcance/2));
			}
			json.layers[i].data = mapaArCortado;
		}
		
		json.height = playerMapAlcance;
		json.width  = playerMapAlcance;
		socket.emit('mapaUpdate',json);
	}

	//login
	socket.on('login', function (nickname) {
		
		var idPlayer = socket.id;
		
		players[nickname] = {width:0,height:0,cropW:0,cropH:0};
		playersIds[idPlayer] = nickname;
		socket.emit('loginResposta',idPlayer);
		
		console.log("Entrou: "+socket.id);
		console.log((Object.keys(players).length)+" players online");
		
		cortaMapa(mapaAr,inicioMapX,inicioMapY,json);
    });
	
	
	//logout
	socket.on('disconnect', function () {
		
		console.log("Saiu: "+socket.id);
		
		delete players[playersIds[socket.id]];
		delete playersIds[socket.id];
		
		console.log((Object.keys(players).length)+" players online");
	});

	
	//player update
	socket.on('updatePosicaoPlayer', function (data) {
		
		var PlayerAtualNickName = playersIds[data[0]];
		var playerAtual = players[PlayerAtualNickName];
		
		
		
		if(playerAtual=="undefined" || playerAtual==null || players=="undefined" || players==null)
			return;
		
		playerAtual.nickname = PlayerAtualNickName;
		playerAtual.width = data[1];
		playerAtual.height = data[2];
		playerAtual.cropW = data[3];
		playerAtual.cropH = data[4];
		playerAtual.img = data[5];
		playerAtual.montaria = data[6];
		playerAtual.contAnimeMont = data[7];
		playerAtual.contAnimed = data[8];
		
	});
	
	setInterval(function(){
		io.sockets.emit('updatePosicaoPlayerResposta',players);
	}, 1000/20);
	
	//mapa update
	socket.on('updatePosicaoMapa', function (data) {
		cortaMapa(mapaAr,data[0]+inicioMapX,data[1]+inicioMapY,json);
    });
	
	//chat
	socket.on('updateChat', function (data) {
		//chatAr[data.nickname] = {chatmsg:data.chatmsg,time:+new Date};
		console.log(data.nickname+": "+data.chatmsg);
		//envia resposta para todos os clientes io.
		io.sockets.emit('updateChatResposta',data); 
    });
	
});