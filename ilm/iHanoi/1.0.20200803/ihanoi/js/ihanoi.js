/* 
iHanói
http://www.usp.br/line

Uso: localhost/ihanoi/index.html?n=3&lang=pt
@TODO ainda nao implementado multi-lingua

@AUTHOR Leônidas de Oliveira Brandão (coord. LInE)
v0.5: 2020/11/22 (novo fundo; evita erro de disco sumir se de=para: nova msg 'msgDeParaIguais'; em "movaHaste(hi)" acresc. "if (topoDe == topoPara)...")
v0.4: 2020/08/03
v0.1: 2020/07/31
v0: 2020/07/28
*/

/*
No arquivo HTML que carrega esse JavaScript deve existir as seguintes imagens:
  <img id="fundo" style="display:none;" src="img/img_fundo_hanoi.png" />
  <img id="haste0" style="display:none;" src="img/hasteA.png" />
  <img id="haste1" style="display:none;" src="img/hasteB.png" />
  <img id="haste2" style="display:none;" src="img/hasteC.png" />
  <img id="disco0" style="display:none;" src="img/disk1.png" />
  <img id="disco1" style="display:none;" src="img/disk2.png" />
  <img id="disco2" style="display:none;" src="img/disk3.png" />
  <img id="disco3" style="display:none;" src="img/disk4.png" />
  <img id="disco4" style="display:none;" src="img/disk5.png" />
  <img id="disco5" style="display:none;" src="img/disk6.png" />

Dimensoes e posicionamento das imagens
 Hastes: 325 x 416
  #   Posicao e tamanho dos discos:
  6:  34, 250   294 130
  5:  48, 210   267 130   +14 -40 -27 +0 
  4:  62, 170   240 130   +14 -40 -27 +0 
  3:  76, 130   213 130   +14 -40 -27 +0 
  2:  90,  90   186 130   +14 -40 -27 +0 
  1: 104,  50   159 130   +14 -40 -27 +0  (mas disk1 esta com 160x130)
*/

console.log("iHanoi: inicio");

var canvas;
var context;

var width = 1100;
var height = 460;
var posY0  = 290; // posicionamento do disco maior (depende de 'height')

// Posicionamento dos discos nas hastes
var matHastes = [ [ 5,  4,  3,  2,  1,  0],   // haste A: pilha de discos (id discos em ordem inversa na haste); haste B e C vazias
                  [-1, -1, -1, -1, -1, -1],   // haste B vazia
                  [-1, -1, -1, -1, -1, -1] ]; // haste C vazia

var vetorMovimentos = []; // vetor para registrar todos os movimentos do aluno - definido na 'movaHaste(hi)'

// Posicionamentos de coordenadas (x,y) para cada um dos 6 discos (no maximo)
var posTx = [  34,  48,  62,  76, 90, 104 ]; // posicoes x para discos: 6, 5, 4...  +14
var posTy = [ 240, 200, 160, 120, 80,  40 ]; // posicoes y para discos: 6, 5, 4...  +40

var nDiscos = 4; // Default entrar com 4 discos
var contador = 0; // conta numero de movimentos
var posx = [  52,  66, 80, 94 ]; // posicoes x para discos: 6, 5, 4...  +14
var posy = [ 160, 120, 80, 40 ]; // posicoes y para discos: 6, 5, 4...  +40

var posx_HA =  20, posy_HA = 40; // posicao haste A
var posx_HB = 370, posy_HB = 40; // posicao haste A
var posx_HC = 720, posy_HC = 40; // posicao haste A

redefineDiscos(nDiscos); // redefinir 'matHastes[][]'

var topoHasteA = nDiscos-1, topoHasteB = topoHasteC = -1; // indice do disco no topo de cada haste

var iHanoi = "iHanói";
var LInE = "LInE-IME-USP";

var isExercise = false;  // se for exercicios, entao NAO permite alterar numero de discos
var isAuthoring = false; // se for edicao, entao permita alterar numero de discos (sobrepoe opcao 'isExercise=true')
var revendo = false;     // durante revisao de movimentos, NAO deveria movimentar discos, se o fizer, entao anule revisao!

//TODO Permitir internacionalizar botoes
var btnReiniciar="Reiniciar", btnRever="Rever", btnCodigo="Código";
var altBtnReiniciar="Reiniciar tudo, todos os discos para haste A", altBtnRever="Rever todos os movimentos realizados",
    altBtnCodigo="Examinar o código no formato do iHanói (extensão 'ihn')";

var mensagem0 = "Clique na regiao da haste para selecionar origem, depois destino";
var mensagem1_1 = "Parabéns! Você conseguiu mover todos os discos com ";
var mensagem1_2 = " movimentos";
var mensagem2_1 = "Não é permitido colocar disco maior sobre menor!";
var mensagem2_2 = " sobre ";
var mensagem3_1 = "Destino: ";
var mensagem3_2 = " - Para novo movimento, clique em nova haste inicial";

var msgTeste1 = "Parabéns conseguiu mover todos para B, mas lembre-se objetivo é C. Usou ";                  // 1
var msgTeste2 = "Parabéns conseguiu mover todos para B e com mínimo de movimentos, mas objetivo é C. Usou "; // 2
var msgTeste3 = "Parabéns conseguiu mover todos para C, mas não o mínimo de movimentos... Usou ";            // 3
var msgTeste4 = "Parabéns! Conseguiu mover todos para C e o mínimo de movimentos! Foram ";                   // 4
var msgEhExercicio = "Não pode alterar número de discos! É um exercício com número de discos pré-fixado.";
var msgReverProx = "Clique novamente no botão 'Rever' para o próximo movimento.";
var msgReverFim = "Acabaram os movimentos registrados.";
var msgReverPare = "Estava revendo movimentação, mas ao mover manualmente, a revisão foi finalizada!";
var msgDeParaIguais = "Para mover um disco é preciso que a haste de destino seja diferente da haste de origem!";

var mensagemNM = "Número de movimentos: ";
var mensagem = mensagem0; // mensagem inicial

// Posicionamento para mensagens
var txtTx = 10, txtTy = 20; // iHanoi
var txtMX = 10, txtMY = height-10; // barra de mensagens: posicao
var txtLInEx = width-180, txtLInEy = 20; // LInE-IME-USP

var tamNMX = 300, tamNMY = 20; // mensagem sobre num. movimentos: tamanho
//1 var txtNMX = 2*325+50, txtNMY = height-10; // mensagem sobre num. movimentos: posicao
var txtNMX = 120, txtNMY = 20; // mensagem sobre num. movimentos: posicao
var tamX = 900, tamY = 20;     // para area de mensagem

// Gerenciamento de evento: primeiro ou segundo clique?
var clickDe = -1, clickPara = -1; // origem e destino: -1,-1 = nada selecionado; x,-1 = selecionada origem; x,y = selecionadas ambas

// Elementos graficos principais: Fundo + Haste + Discos
var imgFundo  = document.getElementById("fundo");
var imgHastes = [ document.getElementById("haste0"), document.getElementById("haste1"), document.getElementById("haste2") ];
var imgDiscos = [ document.getElementById("disco0"), document.getElementById("disco1"), document.getElementById("disco2"),
                  document.getElementById("disco3"), document.getElementById("disco4"), document.getElementById("disco5") ];
var corFundo1 = "#26508c"; // para fundo de mensagem


canvas = document.createElement("canvas");
context = canvas.getContext("2d");
canvas.addEventListener("click", clickCanvas); //OK

// Tamanho da area de trabalho iHanoi
canvas.width = width; canvas.height = height;

document.body.appendChild(canvas); // iniciar area para desenho "canvas"

//D console.log("iHanoi: apos definir elementos graficos");


// Anote tratar-se de exercicio
function setExercise (valor) { // invocada em 'integration-functions.js: decodificaArquivo(strContent)'
  var element, i;
  // se for exercicios, entao NAO permite alterar numero de discos
  isExercise = true;
  if (valor) { // if defined, then is teacher, allow edit (iLM_PARAM_Authoring)
    isExercise = false;
    return; // nao altere permissoes de trocar numero de discos
    }
  //D alert("setExercise: " + valor + ", iLM_PARAM_Authoring=" + iLMparameters.iLM_PARAM_Authoring + ", isExercise=" + isExercise);
  var msg = "";
  for (i=1; i<7; i++) {
    element = document.getElementById("disco"+i);
    if (element!=null) // se for re-avaliacao NAO existe interface grafica
      element.disabled = true; // desabilita o botao
    // Apenas isso NAO impede entrar no tratamento de "clique" no botao, ver 'reiniciar(nD)'
    }
  //D
  console.log("setExercise: " + msg);
  }


// Redefine numero de discos a serem carregados e os posiciona (todos) na haste A
// Evento: quando "clicar" nos botoes com numero de discos (elemento id="disco"+i (i=0, 1, 2,...5)
function redefineDiscos (n) {
  dif = 6-n;
  for (i=0; i<n; i++) { // >
    matHastes[0][i] = n-i-1;
    posx[i] = posTx[i+dif];
    posy[i] = posTy[i+dif];
    }
  for (i=n; i<6; i++) { // >
    matHastes[0][i] = -1;
    posx[i] = -1;
    posy[i] = -1;
    }
  //D
  console.log("redefineDiscos("+n+"): final");
  }


// Inicio --- Para rever movimentos ja' realizados
var reverMov = -1;
var totalMov = -1;
var copiaMovimentos = [];

// @calledby: rever(), clickCanvas(mouseEvent)
function limparRevisao () { // durante revisao de movimentos, NAO deveria movimentar discos, se o fizer, entao anule revisao!
  revendo = false; // nao mais revendo
  reverMov = -1;
  copiaMovimentos = [];
  }

function rever () { // vetorMovimentos = { clickDe + "  " + clickPara, ... }
  if (reverMov == -1) { // inicio
    limparRevisao();
    revendo = true; // inicio de revisao
    totalMov = vetorMovimentos.length;
    for (i=0; i<totalMov; i++) copiaMovimentos.push(vetorMovimentos[i]);
    reverMov = 0;
    reiniciar();
    mensagem = msgReverProx;
    desenhaMensagem();
    revendo = true; // durante revisao de movimentos, NAO deveria movimentar discos, se o fizer, entao anule revisao!
    return;
    }
  if (reverMov == totalMov) { // final
    mensagem = msgReverFim;
    desenhaMensagem();
    totalMov = reverMov = -1; // pode rever novamente
    clickDe = clickPara = -1;
    return;
    }
  var para, copia = copiaMovimentos[reverMov];
  itens = copiaMovimentos[reverMov++].split(' ');
  if (itens.length == 3) { clickDe = eval(itens[0]); para = eval(itens[2]); }
  else { clickDe = eval(itens[0]); para = eval(itens[1]); }
  // alert(itens + ": " + itens.length + ": rever: (" + copia + "): " + clickDe + "-" + clickPara);
  console.log(itens + ", rever: (" + copia + "): " + clickDe + " + " + clickPara + " + " + para); // itens
  movaHaste(para); // 'clickPara' tem que estar com -1 para completar movimento
  mensagem = msgReverProx; // clique novamente no 'Rever'
  desenhaTudo();
  console.lgo("rever(): final");
  } // rever()
// Fim --- Para rever movimentos ja' realizados


// Reiniciar o "jogo": zerar movimentos, colocar todos os discos sobre haste A
function reiniciar (nD) {
  getEvaluation(); // registrar 
  vetorMovimentos = []; // zerar movimentos
  if (nD!="" && nD!=undefined) {
    var element = document.getElementById("disco1");
    if (element.disabled) { // verifica se botao esta' desabilitado (neste caso e' exercicio)
      console.log("Nao pode alterar numero de discos!");
      mensagem = msgEhExercicio;
      desenhaMensagem();
      return;
      }
    redefineDiscos(nD);
    nDiscos = nD;
    }
  topoHasteA = nDiscos-1;
  topoHasteB = topoHasteC = -1;
  for (i=0; i<nDiscos; i++) { // >
    matHastes[1][i] = -1;
    matHastes[2][i] = -1;
    }
  contador = 0;
  redefineDiscos(nDiscos);
  mensagem = mensagem0;
  desenhaTudo();
  console.log("reiniciar(nD): final");
  }


// Decompor parametros recebidos via GET: ?lang=pt&n=4
// Devolve vetor: { 4, "pt" } nesta ordem
function analisa_parametros_url (strParametros) {
  var vars = strParametros.split("&");
  var vetorParametros = [ 3, "pt" ]; // por padrao devolve { 3, "pt" }
  var msg = ""; //D
  var pair, key, value;
  //?par1=val1&par2=val2&
  for (var i = 0; i < vars.length; i++) { // >
    pair = vars[i].split("=");
    if (pair == "") break;
    key = decodeURIComponent(pair[0]);
    value = decodeURIComponent(pair[1]);
    if (key=="n") {
      vetorParametros[0] = value; // vetorParametros[i].push(decodeURIComponent(value));
      nDiscos = value; // redefine 'nDiscos'
      redefineDiscos(nDiscos);
      }
    else
    if (key=="lang") 
      vetorParametros[1] = value; // vetorParametros[i].push(decodeURIComponent(value));
    msg += "("+key+","+value+") "; //D
    }
  //D console.log(vetorParametros); console.log("msg="+msg);  
  return vetorParametros;
  }


// Pegar parametros via GET
function listaURL () {
  // window.location. [ href | protocol | host | hostname | port | pathname | search | hash
  parametros = window.location.search;
  if (parametros=="undefined")
    return;
  if (parametros.length>0) // >
    parametros = parametros.substring(1); // elimina primeiro caractere '?'
  analisa_parametros_url(parametros);
  }


// Para depuracao
function imprimeMovimentos (hi) {
  var i;
  var msg, hA = "[", hB = "[", hC = "[";
  for (i=0; i<nDiscos; i++) { // >
    hA += matHastes[0][i] + " ";
    hB += matHastes[1][i] + " ";
    hC += matHastes[2][i] + " ";
    }
  msg = hA + "], " + hB + "], " + hC + "]";
  return msg;
  }


// Pegar o valor do disco no topo da haste 'ind_haste'
// Se haste vazia, devolve -1
function pegaTopoHaste (ind_haste) { // pega indice do topo da haste
  var topo, i;
  //D alert("pegaTopoHaste: ind_haste=" + ind_haste + ": " + matHastes[ind_haste] + ", matHastes=" + matHastes);
  i=0; while (matHastes[ind_haste][i]!=-1 && i<nDiscos) i++; // >
  return i-1;
  // Para melhorar a eficiencia, poderiamos usar diretamente as variaveis que tem indice dos topos: topoHasteA, topoHasteB, topoHasteC
  }


// Apos movimentacao de discos entre haste, acertar variaveis de topo e "clique"
// Copia no topo de destino o disco do topo de origem
function atualizaTopos (topoDe, topoPara) { // Tira topo "de" e insere em "para"
  topoPara++;
  matHastes[clickPara][topoPara] = matHastes[clickDe][topoDe]; // mova disco do topo de origem para topo de destino
  if (matHastes[clickPara][topoPara] == undefined) { console.log("atualizaTopos("+topoDe+","+topoPara+"): erro! matHastes[clickPara][topoPara] undefined"); }  
  // Tira disco do topo de origem
  matHastes[clickDe][topoDe] = -1; // remova disco que estava no topo da haste de origem
  topoDe--;
  // Atualiza globais
  if (clickDe==0) // haste A
    topoHasteA = topoDe;
  else
  if (clickDe==1) // haste B
    topoHasteB = topoDe;
  else // haste C
    topoHasteC = topoDe;
  if (clickPara==0) // haste A
    topoHasteA = topoPara;
  else
  if (clickPara==1) // haste B
    topoHasteB = topoPara;
  else // haste C
    topoHasteC = topoPara;
  //D alert("atualizaTopos: " + clickDe + " :: " + clickPara + ": " + imprimeMovimentos(clickPara));
  clickDe = clickPara = -1; // comeca novamente...
  }


// Devolve rotulo da haste de indice 'hi'
function pegaHaste (hi) {
  if (hi==0) return "A";
  if (hi==1) return "B";
  return "C";
  }


// Verifica se todos os discos estao na haste C
// Devolve: 0=nao moveu tudo; 1=moveu tudo para haste B; 2=moveu tudo para haste B com minimo de movimentos;
//          3=moveu tudo par haste C; 4=moveu tudo par haste C com minimo de movimentos
function movimentoFinal (haste, num) {
  var topo = pegaTopoHaste(haste);
  if (topo == nDiscos-1) { // moveu tudo!
    if (haste == 2) { // moveu para haste C
      if (contador == 2^nDiscos-1) { // moveu para haste C com minimo
        return 4;
        }
      return 3; // moveu para haste C mas nao e' minimo
      }
    if (haste == 1) { // moveu para haste B
      if (contador == 2^nDiscos-1) { // moveu para haste B com minimo
        return 2; // msgTeste2
        }
      return 1; // moveu para haste C mas nao e' minimo
      }
    }
  return 0;
  }


// Mover disco do topo da haste 'clickDe' para a haste 'hi' (sem 'clickDe' definido)
function movaHaste (hi) {
  var strHaste = pegaHaste(hi);
  var de0 = clickDe, para0 = clickPara;
  if (clickDe==-1 && clickPara==-1) { // inicio movimento
    clickDe = hi;
    topoDe = pegaTopoHaste(clickDe); // pega disco no topo de haste
    if (topoDe==-1) { // nao tem discos
      mensagem = "Haste " + strHaste + " está vazia! Por favor, selecione haste inicial com algum disco";
      clickDe = clickPara = -1;
      desenhaMensagem();
      return;
      }
    mensagem = "Origem: " + strHaste + " - Agora clique na haste destino";
    de0 = hi;
    desenhaMensagem();
    }
  else
  if (clickDe>-1 && clickPara==-1) { // final do movimento
    clickPara = hi;
    para0 = hi;
    //D alert("De="+clickDe+", Para="+clickPara+", hi="+hi);
    topoDe = pegaTopoHaste(clickDe);     // devolve indice topo de haste
    topoPara = pegaTopoHaste(clickPara); // devolve indice topo de haste
    if (clickDe == clickPara) {
      str_haste = pegaHaste(clickDe); // nome da haste: "A", "B" ou "C"
      mensagem = msgDeParaIguais + " (haste " + str_haste + ")";
      console.log("Erro: Tentando mover disco para a mesma haste! (haste " + str_haste + ")");
      clickDe = clickPara = -1; // comeca novamente...
      desenhaMensagem();
      return -1;
      }
    if (topoPara>-1 && matHastes[clickDe][topoDe]>matHastes[clickPara][topoPara]) { // disco maior sobre menor : proibido!
      mensagem = mensagem2_1 + " (" + matHastes[clickDe][topoDe] + mensagem2_2 + matHastes[clickPara][topoPara] + ")";
      //D alert("De="+clickDe+", Para="+clickPara+": "+topoDe+","+topoPara+": " + imprimeMovimentos(-1));
      clickDe = clickPara = -1; // comeca novamente...
      desenhaMensagem();
      return -1;
      }
    vetorMovimentos.push(clickDe + "  " + clickPara);
    if (topoDe<0) { console.log("movaHaste("+hi+"): "+clickDe + "  " + clickPara+": erro! undefined"); } //DEBUG
    atualizaTopos(topoDe, topoPara);
    contador++;

    // 0=nao moveu tudo; 1=moveu tudo para haste B; 2=moveu tudo para haste B com minimo de movimentos;
    // 3=moveu tudo par haste C; 4=moveu tudo par haste C com minimo de movimentos
    respostaMov = movimentoFinal(hi, contador);

    switch (respostaMov) {
      case 0: mensagem = mensagem3_1 + strHaste + mensagem3_2; break;
      case 1: mensagem = msgTeste1 + contador + mensagem1_2; break;
      case 2: mensagem = msgTeste2 + contador + mensagem1_2; break; 
      case 3: mensagem = msgTeste3 + contador + mensagem1_2; break; 
      case 4: mensagem = msgTeste4 + contador + mensagem1_2; break; 
      mensagem = mensagem1_1 + contador + mensagem1_2; // Paranbens! (falta comparar com numero minimo!)
      }
      
    desenhaTudo();
    }
  console.lgo("movaHaste(hi): final");
  return 1;
  } // movaHaste(hi)


// Dispara eventos
function clickCanvas (mouseEvent) {
  var posx = mouseEvent.offsetX, posy = mouseEvent.offsetY; // Posicao do "mouse", valores para parametros de '.drawImage(...)'
  if (posx>25 && posx<350 && posy>30 && posy<440) { // > clicou na haste 1
    resp = movaHaste(0);
    }
  else
  if (posx>350 && posx<690 && posy>30 && posy<440) { // > clicou na haste 2
    resp = movaHaste(1);
    }
  else
  if (posx>690 && posx<1030 && posy>30 && posy<440) { // > clicou na haste 3
    resp = movaHaste(2);
    }
  if (revendo) { // estava revendo movimento mas clicou em haste, entao cancele revisao!
    mensagem = msgReverPare; //  "Estava revendo movimentação, mas ao mover manualmente, a revisão foi finalizada!"
    limparRevisao();
    desenhaMensagem(); //D sem efeito, nao 'sleep(.)' nao permite aparecer a mensagem
    //sleep(1600); // em 'integration-functions.js'
    }
  }


// Desenha um retangulo - modelo de http://jsfiddle.net/vu7dZ/1/
function roundRect (ctx, x, y, width, height, radius, fill, stroke) {
  if (typeof stroke == "undefined" ) { stroke = true; }
  if (typeof radius === "undefined") { radius = 5; }
  ctx.beginPath();
  ctx.moveTo(x + radius, y);
  ctx.lineTo(x + width - radius, y);
  ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
  ctx.lineTo(x + width, y + height - radius);
  ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
  ctx.lineTo(x + radius, y + height);
  ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
  ctx.lineTo(x, y + radius);
  ctx.quadraticCurveTo(x, y, x + radius, y);
  ctx.closePath();
  if (stroke) { ctx.stroke(); }
  if (fill) { ctx.fill(); }        
  }


// Desenha os discos em cada Haste (Haste A = matHastes[0][]; Haste B = matHastes[1][]; Haste C = matHastes[2][])
// Cada imagem tem 28 pixels a mais que o disco menor (dai o "(nDiscos - ind_disco-1)*14")
function desenhaDiscos () { // 'context' e' global
  var posx, posy, i;
  //D console.log("desenhaDiscos(): inicio");

  // Haste A
  posy = posY0;
  ind_disco = matHastes[0][0];
  i = 0;
  while (ind_disco!=-1) { // enquanto ainda tem disco, nao e' o ultimo
    posx = 33 + (6 - ind_disco-1)*14; // para nDiscos=6 : usar 34 + ...
    //TODO: precisa resolver um erro/advertencia que aparece
    // TypeError: Argument 1 of CanvasRenderingContext2D.drawImage could not be converted to any of: HTMLImageElement, SVGImageElement, HTMLCanvasElement, HTMLVideoElement, ImageBitmap.
    context.drawImage(imgDiscos[ind_disco], posx, posy);
    posy -= 40;
    i++;
    ind_disco = matHastes[0][i];
    }
  // Haste B
  posy = posY0;
  ind_disco = matHastes[1][0];
  i = 0;
  while (ind_disco!=-1) { // enquanto ainda tem disco, nao e' o ultimo
    posx = 382 + (6 - ind_disco-1)*14;
    if (ind_disco == undefined) { console.log("desenhaDiscos(): disco 1: erro: i=" + i); return; } // alert("desenhaDiscos(): erro: i=" + i);
    // console.log("desenhaDiscos(): " + imprimeMovimentos(0)); // + ", " + imprimeMovimentos(1) + ", " + imprimeMovimentos(2));
    context.drawImage(imgDiscos[ind_disco], posx, posy);
    posy -= 40;
    i++;
    ind_disco = matHastes[1][i];
    }
  // Haste C
  posy = posY0;
  ind_disco = matHastes[2][0];
  i = 0;
  while (ind_disco!=-1) { // enquanto ainda tem disco, nao e' o ultimo
    posx = 732 + (6 - ind_disco-1)*14;
    context.drawImage(imgDiscos[ind_disco], posx, posy);
    posy -= 40;
    i++;
    ind_disco = matHastes[2][i];
    }
  console.log("desenhaDiscos(): final");
  } // desenhaDiscos()


// Apenas muda a mensagem informativa
function desenhaMensagem () {
  context.font = 'bold 14px serif';
  context.fillStyle = "white";
  //context.clearRect(txtMX, txtMY-15, tamX, tamY);
  context.fillRect(txtMX, txtMY-15, tamX, tamY);
  context.fillStyle = "black"; //"white";
  context.fillText(" " + mensagem, txtMX, txtMY);
  roundRect(context, txtMX, txtMY-15, tamX, tamY);
  }


// Redesenha tudo
function desenhaTudo () {
  console.log("desenhaTudo(): inicio");
  context.font = 'bold 20px serif';
  context.drawImage(imgFundo,   0,  0, width, height );
  context.fillStyle = "white";
  context.fillText(iHanoi, txtTx, txtTy); // iHanoi
  context.fillText(LInE, txtLInEx, txtLInEy); // LInE-IME-USP
  context.drawImage(imgHastes[0], posx_HA, posy_HA); // posicao haste A
  context.drawImage(imgHastes[1], posx_HB, posy_HB); //
  context.drawImage(imgHastes[2], posx_HC, posy_HC); //
  context.font = 'bold 14px serif';
  context.fillStyle = "white"; // "#26508c"; // para fundo de mensagem
  //context.clearRect(txtMX, txtMY-15, tamX, tamY);  // Mensagens
  context.fillRect(txtMX, txtMY-15, tamX, tamY);  // Mensagens
  roundRect(context, txtMX, txtMY-15, tamX, tamY); // Mensagens
  //context.clearRect(txtNMX, txtNMY-15, tamNMX, tamNMY);  // Numero de movimentos
  context.fillRect(txtNMX, txtNMY-15, tamNMX, tamNMY);  // Numero de movimentos
  roundRect(context, txtNMX, txtNMY-15, tamNMX, tamNMY); // Numero de movimentos
  context.fillStyle = "black"; //"white";
  context.fillText(" " + mensagem, txtMX, txtMY); // mensagens
  context.fillText(" " + mensagemNM + contador, txtNMX, txtNMY); // numero de movimentos
  desenhaDiscos();
  console.log("desenhaTudo(): final");
  } // desenhaTudo()


// Versao distinta para inicia - removida em favor do 'onload' no 'body'
// window.addEventListener("DOMContentLoaded", function () {
//  //D alert("DOMContentLoaded: " + canvas.width + "," + canvas.height);
//  desenhaTudo();
//  });


console.log("iHanoi: final do JavaScript principal"); //D
