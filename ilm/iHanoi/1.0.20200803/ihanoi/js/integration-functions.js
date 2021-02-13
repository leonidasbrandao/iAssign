/* 
iHano'i
http://www.usp.br/line

Uso: localhost/ihanoi/index.html?n=3&lang=pt
@TODO ainda nao implementado multi-lingua

Leo^nidas de Oliveira Branda~o
v0.5: 2020/11/22 (getiLMContent(): se 'iLMparameters.iLM_PARAM_Assignment' vazio, nem tenta carregar arquivo do iHanoi (IHN))
v0.4: 2020/08/03
v0.1: 2020/07/31
v0: 2020/07/28
*/

console.log("integration-functions.js: inicio");

// Variaveis externas
// nDiscos = numero de disco definido na funcao principal iHanoi
// contador = contador de numero de movimentos realizados
// topoHasteA, topoHasteB, topoHasteC = indice na haste do maior disco nela

const NOTA_MINIMO_B = 0.8; // alvo nao era haste B, descontar
const ESPERA = 0; // retardo para permitir ver movimentos qdo carga automatica

// Funcao para ler parametros informados pelo iTarefa via URL
// Apesar de nao ser obrigatorio, sera muito útil para capturar os parametros
function getParameterByName (name) {
  var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
  return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : null;
  }

// Criando um vetor com os parametros informados pelo iTarefa
// Observe que para cada parametro, e realizada a chamada do metodo getParameterByName, implementado acima
var iLMparameters = {
  // Exemplo de como seria a URL via iTarefa/Moodle: http://.../moodle/mod/iassign/ilm_manager.php?from=iassign&id=2&action=update&ilmid=53&dirid=41800&fileid=282593
  iLM_PARAM_Authoring: getParameterByName("iLM_PARAM_Authoring"), // if defined, then is teacher, allow edit
  iLM_PARAM_ServerToGetAnswerURL: getParameterByName("iLM_PARAM_ServerToGetAnswerURL"),
  iLM_PARAM_SendAnswer: getParameterByName("iLM_PARAM_SendAnswer"),
  iLM_PARAM_AssignmentURL: getParameterByName("iLM_PARAM_AssignmentURL"),
  iLM_PARAM_Assignment: getParameterByName("iLM_PARAM_Assignment"),
  iLM_PARAM_TeacherAutoEval: getParameterByName("iLM_PARAM_TeacherAutoEval"),
  lang: getParameterByName("lang")
  };

// Funcao chamada pelo iTarefa quando o professor finaliza a criacao da atividade
// ou quando o aluno finaliza a resolucao do exercicio
// O retorno e um JSON com os dados do exercicio ou da resolucao
// Esse retorno sera armazenado no banco de dados do Moodle, pelo iTarefa
function getAnswer () {
  // Se o parametro "iLM_PARAM_SendAnswer" for false,
  // entao trata-se de resolucao de atividade
  if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
    // Montar o retorno da resposta do aluno
    var studentAnswer = "Numero de discos: " + nDiscos + " \nQuantidade de Movimentos: " + contador + " \nMovimentos:";
    for (var i = 0; i < vetorMovimentos.length; i++) {
      studentAnswer += "\n" + vetorMovimentos[i]; // vetorMovimentos[]: global definida em 'ihanoi.js'
      }
    // alert(studentAnswer);
    return studentAnswer; // teacherReturn;
  } else { //se for o professor acessando, mostra a pagina de elaboracao
    return "Número de Discos: " + nDiscos;
    }
  }

function potencia2 (n) {
  var pot = 1, i;
  for (i=0; i<n; i++) pot *= 2;
  return pot;
  }

// Funcao chamada pelo iTarefa para receber a nota do aluno na atividade
// O retorno e um valor entre 0.0 e 1.0
function getEvaluation () {
  if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
    // Calculo da nota: resposta correta = 1 (C em minimo), 0.7 (B em minimo), errada = 0
    var aux;
    var nota;
    var minimo = potencia2(nDiscos)-1; // 2^nDiscos-1
    if (topoHasteC+1 == nDiscos) { // moveu todos para C
      if (contador == minimo) { // com o minimo
        nota = 1;
        aux = 1;
        }
      else {
        nota = minimo / contador; // quanto mais movimentos, menor nota
        aux = 2;
        }
      }
    else
    if (topoHasteB+1 == nDiscos) { // moveu todos para B
      if (contador == minimo) { // com o minimo
        nota = NOTA_MINIMO_B; // alvo nao era haste B, descontar
        aux = 3;
        }
      else {
        nota = minimo / contador; // quanto mais movimentos, menor nota
        aux = 4;
        }
      }
    else {
      nota = 0;
      aux = 5;
      }

    // getEvaluation(): topoHasteB=2, topoHasteC=-1, nota=0 :: 4
    // getEvaluation(): topoHasteB=-1, topoHasteC=2, nota=0 :: 2 minimo=0 contador=7 0
    console.log("getEvaluation(): topoHasteB=" + topoHasteB + ", topoHasteC=" + topoHasteC + ", nota="+nota + " :: " + aux + " minimo="+minimo+" contador="+contador+" "+(minimo/contador));
    // A chamada do metodo abaixo e obrigatoria!
    // Observe que a chamada parte do iLM para o iTarefa
    //D alert("nota="+nota+"\n"+msg);
    parent.getEvaluationCallback(nota); //TODO NAO usado!!!!????
    return nota;
    }
  }


// Formato do arquivo iHanoi: exercicio, apenas com numero de discos
//    Numero de Discos: 2
// Formato do arquivo iHanoi: resposta do aluno, com discos e movimentos
//    Numero de Discos: 2
//    Quantidade de Movimentos: 3 
//    Movimentos:
//    0  1
//    0  2
//    1  2
function decodificaArquivo (strContent) {
  var linhas = strContent.split("\n");
  var msg = "";
  var nlinhas = linhas.length, nmov;
  var itens, i1, i2;
  if (nlinhas>0) {
    itens = linhas[0].split(":");
    nDiscos = eval(itens[1]);
    nDiscos0 = nDiscos;
    if (iLMparameters.iLM_PARAM_Authoring == 'true')
      setExercise(true); // global definidas em 'ihanoi.js': indica tratar-se de exercicio
    else
      setExercise(false); // global definidas em 'ihanoi.js': indica tratar-se de exercicio
    //D alert("decodificaArquivo: iLM_PARAM_Authoring=" + iLMparameters.iLM_PARAM_Authoring);
    // nDiscos = 0;
    reiniciar(); // Funcao externa: reinicia iHanoi
    if (nlinhas>1) {
      itens = linhas[1].split(":");
      nmov = itens[1]; // numero de movimentos do aluno
      contador = nmov;
      }
    if (nlinhas>2) {
      contador = 0; // global definidas em 'ihanoi.js': conta numero de movimentos
      for (i=3; i<nlinhas; i++) { // pula linha com "Movimentos:"
        itens = linhas[i].split(" ");
	if (itens=="" || itens.length<2) {
          console.log("Erro: arquivo nao está no formato iHanoi. Linha " + i + ": " + linhas[i]);
          return;
          }//decodificaArquivo: "0,,1
        i0 = 0; i1 = 1;
        if (itens.length==3) // se decomposicao tratar "0 1" como {0,,1}
          i1 = 2;
        clickDe = itens[i0];   // global definidas em 'ihanoi.js': haste de partida
        clickPara = -1;        // global definidas em 'ihanoi.js': haste de chegada
        //D alert("decodificaArquivo: \"" + itens + "\":" + clickDe + " - " + itens[i1]);
        movaHaste(eval(itens[i1]));  // funcao definidas em 'ihanoi.js': mover de hastes - 'eval(.)' elimina eventual \n ou ' '
	desenhaTudo(); // funcao definidas em 'ihanoi.js': desenhar novo configuracao
	// sleep(ESPERA);
        msg += "\n" + linhas[i];
        }
      }
    }
  } // function decodificaArquivo(strContent)


// Funcao para que o iMA leia os dados da atividade fornecidos pelo iTarefa
function getiLMContent () {
  var msg = "";
  // O parametro "iLM_PARAM_Assignment" fornece o URL do endereco que deve ser
  // requisitado via XMLHttpRequest() para a captura dos dados da atividade
  var pagina = iLMparameters.iLM_PARAM_Assignment;
  var txtFile;
  var data = -1;
  //D console.log("integration-functions.js: getiLMContent(): iLMparameters.iLM_PARAM_TeacherAutoEval=" + iLMparameters.iLM_PARAM_TeacherAutoEval); //D
  //D console.log("integration-functions.js: getiLMContent(): iLMparameters.iLM_PARAM_Assignment=" + iLMparameters.iLM_PARAM_Assignment);
  if (iLMparameters.iLM_PARAM_Assignment == null) {
    console.log("integration-functions.js: getiLMContent(): NAO existe arquivo IHN para ser carregado (iLMparameters.iLM_PARAM_Assignment vazio), finalize");
    return;
    }
  if (iLMparameters.iLM_PARAM_TeacherAutoEval != null) {
    try {
      parent.getAutoEvalOriginalData(); // funcao definida pelo iTarefa que devolve o conteudo original do exercicio atual
    } catch (Error) {
      console.log("integration-functions.js: getiLMContent(): erro ao tentar executar funcao 'getAutoEvalOriginalData()'");
      } // se nao esta' em re-avaliacao => NAO esta' definida 'parent.getAutoEvalOriginalData()'
    // alert("integration-functions.js: actual exercise=" + data);
    teacherAutoEval(data);
    console.log("integration-functions.js: getiLMContent(): final (apos ler arquivo IHN)");
    return;
    }

  txtFile = new XMLHttpRequest(); // preparar coneccao HTTP para requisitar um arquivo IHN

  console.log("integration-functions.js: getiLMContent(): tenta pegar arquivo de " + pagina);

  // window.location : href = a URL inteira; pathname = ; hostname = apenas o nome do servidor
  txtFile.open("GET", pagina, true); // true=>asincrono - mas ambos estao resultando (arq. IHN nao passar teste XML...): XML Parsing Error: syntax error
  txtFile.send(); // so' pode fechar apos 3o passo
  txtFile.responseType="text"; // Evita advertencia: XML Parsing Error: syntax error

  txtFile.onreadystatechange = function () {
    if (txtFile.readyState === 4) { // Makes sure the document is ready to parse.
      if (txtFile.status === 200) { // Makes sure the file exists.
        // 3o passo: por ultimo chega aqui!
        var nDiscos0;
        nDiscos0 = nDiscos;
        allText = txtFile.responseText;
        texto = allText; // define global 'texto'
        // processar conteudo de INH
        decodificaArquivo(allText);
        }
      //else alert("Erro 2"); // 2o passo: passa depois aqui
      }
    //else alert("Erro 1"); // 1o passo: passa primeiro aqui
    } 
  console.log("integration-functions.js: getiLMContent(): final");
  } // function getiLMContent()


// Adicionamos a diretiva .ready(), para que quando a pagina HTML estiver carregada,
// seja verificado qual a visualizacao deve ser apresentada: se a area de construcao
// de atividade ou de resolucao. E no caso de ser resolucao, os dados da atividade
// precisam ser consultados, pelo metodo implementado acima, o getiLMContent()

// Pegar conteudo da ativida iMA
getiLMContent();

function sleep (milliseconds) {
  var startSleep = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - startSleep) > milliseconds) {
      break;
      }
    }
  }


// To be used with re-evaluation
function teacherAutoEval (data) {
  var nDiscos0;
  nDiscos0 = nDiscos;
  alert("integration-functions.js: teacherAutoEval(.): " + data);
  // processar conteudo de INH
  decodificaArquivo(data);
  }

console.log("integration-functions.js: final");