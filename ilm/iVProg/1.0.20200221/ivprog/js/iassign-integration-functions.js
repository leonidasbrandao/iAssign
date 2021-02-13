// Função para ler parâmetros informados pelo iTarefa via URL
// Apesar de não ser obrigatório, será muito útil para capturar os parâmetros
function getParameterByName (name, defaultReturn = null) {
  var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
  return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : defaultReturn;
}

// Criando um object com os parâmetros informados pelo iTarefa
// Observe que para cada parâmetro, é realizada a chamada do método getParameterByName, implementado acima
var iLMparameters = {
  iLM_PARAM_ServerToGetAnswerURL: getParameterByName("iLM_PARAM_ServerToGetAnswerURL"),
  iLM_PARAM_SendAnswer: getParameterByName("iLM_PARAM_SendAnswer"),
  iLM_PARAM_AssignmentURL: getParameterByName("iLM_PARAM_AssignmentURL"),
  iLM_PARAM_Assignment: getParameterByName("iLM_PARAM_Assignment"),
  iLM_PARAM_TeacherAutoEval: getParameterByName("iLM_PARAM_TeacherAutoEval"),
  lang: getParameterByName("lang", "pt")
};

// Set the lang parameter to the localStorage for easy access
// and no dependency to the global scope, avoind future 'strict mode' problems
//localStorage.setItem('ivprog.lang', iLMparameters.lang);

function removeCollapseValue (command) {
  if (command.collapsed) {
    delete command.collapsed;
  }
  if (command.type == 'iftrue') {
    if (command.commands_block)
      for (var i = 0; i < command.commands_block.length; i++) {
        removeCollapseValue(command.commands_block[i]);
      }
    if (command.commands_else)
      for (var i = 0; i < command.commands_else.length; i++) {
        removeCollapseValue(command.commands_else[i]);
      }
  } else if (command.type == 'repeatNtimes' 
            || command.type == 'whiletrue' 
            || command.type == 'dowhiletrue' ) {
    if (command.commands_block)
      for (var i = 0; i < command.commands_block.length; i++) {
        removeCollapseValue(command.commands_block[i]);
      }
  }
}

// Função chamada pelo iTarefa quando o professor finaliza a criação da atividade
// ou quando o aluno finaliza a resolução do exercício
// O retorno é um JSON com os dados do exercício ou da resolução
// Esse retorno será armazenado no banco de dados do Moodle, pelo iTarefa
function getAnswer () {
  // Remover o colapsar usado localmente:
  if (window.program_obj) {
    for (var i = 0; i < window.program_obj.functions.length; i++) {
      for (var j = 0; j < window.program_obj.functions[i].commands.length; j++) {
        removeCollapseValue(window.program_obj.functions[i].commands[j]);
      }
    }
  }
  // Se o parâmetro "iLM_PARAM_SendAnswer" for false,
  // então trata-se de resolução de atividade
  if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
    // Montar o retorno com a resposta do aluno
    var contentToSend = previousContent.split("\n::algorithm::")[0];
    contentToSend += '\n::algorithm::';

    if (settingsProgrammingTypes == "textual") {
      contentToSend +=  ivprogCore.CodeEditor.getCode();
    } else {
      contentToSend += JSON.stringify(window.program_obj, function(key, value) {
        if (key == 'dom_object') {
            return;
        }
        return value;
      });
    }

    contentToSend += '\n::logs::';
    contentToSend += getTrackingLogs();

    return contentToSend;

  } else {
    // Montar o retorno com a criação da atividade do professor
    var ret = ' { ' + prepareTestCases()
        + ',\n"settings_programming_type": \n' + JSON.stringify($('form[name="settings_programming_type"]').serializeArray())
        + ',\n"settings_data_types": \n' + JSON.stringify($('form[name="settings_data_types"]').serializeArray())
        + ',\n"settings_commands": \n' + JSON.stringify($('form[name="settings_commands"]').serializeArray())
        + ',\n"settings_functions": \n' + JSON.stringify($('form[name="settings_functions"]').serializeArray())
        + ',\n"settings_filter": \n' + JSON.stringify($('form[name="settings_filter"]').serializeArray())
        + ' } ';

    if ($("input[name='include_algo']").is(':checked')) {
      ret += '\n::algorithm::';
      ret += JSON.stringify(window.program_obj, function(key, value) {

          if (key == 'dom_object') {
              return;
          }
          return value;
      });
    }

    return ret;
  }
}

function prepareTestCases () {
  var ret = ' \n "testcases" : [ '
  var test_cases_array = $('form[name="test_cases"]').serializeArray();
  console.log(test_cases_array);
  for (var i = 0; i < test_cases_array.length; i = i + 2) {
    ret += '\n{ ';
    ret += '\n "input": [';
    var inps = test_cases_array[i].value.match(/[^\r\n]+/g);
    if (inps) {
      for (var j = 0; j < inps.length; j++) {
        ret += '"' + inps[j] + '"';
        if ((j + 1) < inps.length) {
          ret += ', ';
        }
      }
    }
    ret += '], \n "output": [';
    var outs = test_cases_array[i+1].value.match(/[^\r\n]+/g);
    console.log(outs);
    if (outs) {
      for (var j = 0; j < outs.length; j++) {
        console.log("output -> ",outs[j]);
        ret += '"' + outs[j] + '"';
        if ((j + 1) < outs.length) {
          ret += ', ';
        }
      }
    }
    ret += ']';
    ret += '\n}'
    if ((i + 2) < test_cases_array.length) {
      ret += ',';
    }
  }
  ret += '\n] ';
  return ret;
}

// Função chamada pelo iTarefa para receber a nota do aluno na atividade
// O retorno é um valor entre 0.0 e 1.0
function getEvaluation () {
  if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
    // A chamada do método abaixo é obrigatória!
    // Observe que a chamada parte do iLM para o iTarefa
    //parent.getEvaluationCallback(window.studentGrade);

    var canRunAssessment = runCodeAssessment();
    if(canRunAssessment === -1) {
      parent.getEvaluationCallback(-1);
    }
  }
}

//var testCases = null
var settingsDataTypes = null;
var settingsCommands = null;
var settingsFunctions = null;
var settingsProgrammingTypes = null;
var settingsFilter = null;
var algorithm_in_ilm = null;
var previousContent = null;

// Função para que o iMA leia os dados da atividade fornecidos pelo iTarefa
function getiLMContent () {

  // O parâmetro "iLM_PARAM_Assignment" fornece o URL do endereço que deve ser
  // requisitado via AJAX para a captura dos dados da atividade
  $.get(iLMparameters.iLM_PARAM_Assignment, function (data) {
    //professor invocou a avaliação automática dos exercícios do bloco
    if (iLMparameters.iLM_PARAM_TeacherAutoEval != null) {
        teacherAutoEval(data);
        //não deve exibir nenhuma interface...
        return;
    } else if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
        // Aluno está trabalhando em alguma atividade:
        previousContent = data;
        prepareActivityToStudent(data);
    } else { // Professor está editando uma atividade:
        previousContent = data;
        prepareActivityToEdit(data);
    }

    window.block_render = false;
    renderAlgorithm();
  });
}

function prepareActivityToEdit (ilm_cont) {
  //var content = JSON.parse(ilm_cont.split('\n::algorithm::')[0]);
  // Ver arquivo js/util/iassignHelpers.js
  var content = ivprogCore.prepareActivityToStudentHelper(ilm_cont).getOrElse(null);
  if(!content) {
    showInvalidData();
    return;
  }
  var testCases = ivprogCore.getTestCases();

  settingsProgrammingTypes = content.settingsProgrammingType;
  settingsDataTypes = content.settingsDataTypes;
  settingsCommands = content.settingsCommands;
  settingsFunctions = content.settingsFunctions;
  settingsFilter = content.settingsFilter;

  for (var i = 0; i < testCases.length; i++) {
    addTestCase(testCases[i]);
  }

  if (content.algorithmInIlm != null) {
    algorithm_in_ilm = content.algorithmInIlm;
    $("input[name='include_algo']").prop('checked', true);
    includePreviousAlgorithm();
    renderAlgorithm();
  }

  ivprogTextualOrVisual();
  if (settingsFilter && settingsFilter[0]) {
    blockAllEditingOptions();
  }
}

function parsePreviousAlgorithm () {
  window.program_obj.functions = JSON.parse(algorithm_in_ilm).functions;
  window.program_obj.globals = JSON.parse(algorithm_in_ilm).globals;
}

function includePreviousAlgorithm () {
  if (settingsProgrammingTypes == "textual") {
    return;
  }

  parsePreviousAlgorithm();

  window.watchW.watch(window.program_obj.globals, function(){
    if (window.insertContext) {
      setTimeout(function(){ renderAlgorithm(); }, 300);
      window.insertContext = false;
    } else {
      renderAlgorithm();
    }
  }, 1);

  for (var i = 0; i < window.program_obj.functions.length; i ++) {
    window.watchW.watch(window.program_obj.functions[i].parameters_list, function(){
      if (window.insertContext) {
        setTimeout(function(){ renderAlgorithm(); }, 300);
        window.insertContext = false;
      } else {
        renderAlgorithm();
      }
    }, 1);

    window.watchW.watch(window.program_obj.functions[i].variables_list, function(){
      if (window.insertContext) {
        setTimeout(function(){ renderAlgorithm(); }, 300);
        window.insertContext = false;
      } else {
        renderAlgorithm();
      }
    }, 1);

    if (window.program_obj.functions[i].is_main) {
        window.program_obj.functions[i].name = LocalizedStrings.getUI("start");
    }
  }

  window.watchW.watch(window.program_obj.functions, function(){
    if (window.insertContext) {
      setTimeout(function(){ renderAlgorithm(); }, 300);
      window.insertContext = false;
    } else {
      renderAlgorithm();
    }
  }, 1);
}

function prepareActivityToStudent (ilm_cont) {
    // Ver arquivo js/util/iassignHelpers.js
    var content = ivprogCore.prepareActivityToStudentHelper(ilm_cont).getOrElse(null);
    if(!content) {
      showInvalidData();
      return;
    }
    // Casos de testes agora são delegados ao tratamento apropriado pela função acima
    // var testCases = content.testcases;
    settingsProgrammingTypes = content.settingsProgrammingType;
    settingsDataTypes = content.settingsDataTypes;
    settingsCommands = content.settingsCommands;
    settingsFunctions = content.settingsFunctions;
    settingsFilter = content.settingsFilter;

    if (content.algorithmInIlm != null) {
        algorithm_in_ilm = content.algorithmInIlm;
        includePreviousAlgorithm();
    }
    $('.assessment_button').removeClass('disabled');
    renderAlgorithm();

    ivprogTextualOrVisual();
    if (settingsFilter && settingsFilter[0]) {
      blockAllEditingOptions();
    }
}

// Função para organizar se para criação, visualização ou resolução de atividade
function prepareEnvironment () {

  $('.div_to_body').click(function(e) {
    // trackingMatrix.push(adCoords(e, 1));
    ivprogCore.registerClick(e.pageX, e.pageY, e.target.classList['value']);
  });

  // Se iLM_PARAM_SendAnswer for false, então trata-se de resolução de atividade,
  // portanto, a "DIV" de resolução é liberada
  if (iLMparameters.iLM_PARAM_SendAnswer == 'false') {
    //$('.resolucao').css("display","block");
    getiLMContent();

    // $('.div_to_body').mousemove(function(e) {
    //     trackingMatrix.push(adCoords(e, 0));
    // });

    // $('.div_to_body').click(function(e) {
    //   // trackingMatrix.push(adCoords(e, 1));
    //   ivprogCore.registerClick(e.pageX, e.pageY, e.target.classList['value']);
    // });
  } else if (iLMparameters.iLM_PARAM_Assignment) {
    // Caso não esteja em modo de resolução de atividade, a visualização no momento
    // é para a elaboração de atividade:
    //$('.elaboracao').css("display","block");

    // Se possuir o parâmetro iLMparameters.iLM_PARAM_Assignment, o professor
    // está editando uma atividade:
    getiLMContent();
  } else {
    renderAlgorithm();
  }

  if ((iLMparameters.iLM_PARAM_AssignmentURL == "true") && (iLMparameters.iLM_PARAM_SendAnswer == "true")) {
    prepareActivityCreation();
  }
}

function blockAllEditingOptions () {

  if ((iLMparameters.iLM_PARAM_AssignmentURL == "true") && (iLMparameters.iLM_PARAM_SendAnswer == "true")) {
    return;
  }

  $('.add_global_button').addClass('disabled');
  $('.move_function').addClass('disabled');
  $('.add_function_button').addClass('disabled');
  $('.add_var_button_function .ui.icon.button.purple').addClass('disabled');
  $('.add_var_button_function').addClass('disabled');
  $('.menu_commands').addClass('disabled');

  $('.global_type').addClass('disabled');
  $('.editing_name_var').addClass('disabled');
  $('.span_value_variable').addClass('disabled');

  $('.remove_global').addClass('disabled');
  $('.ui.icon.ellipsis.vertical.inverted').addClass('disabled');

  $('.alternate_constant').addClass('disabled');
  $('.remove_variable').addClass('disabled');

  $('.add_global_matrix_column').addClass('disabled');
  $('.remove_global_matrix_column').addClass('disabled');

  $('.add_global_matrix_line').addClass('disabled');
  $('.remove_global_matrix_line').addClass('disabled');

  $('.add_global_vector_column').addClass('disabled');
  $('.remove_global_vector_column').addClass('disabled');

  $('.add_expression').addClass('disabled');
  $('.add_parentheses').addClass('disabled');

  $('.remove_function_button').addClass('disabled');
  $('.button_remove_command').addClass('disabled');

  $('.command_drag').addClass('disabled');
  $('.simple_add').addClass('disabled');

  $('.add_parameter_button').addClass('disabled');
  $('.parameter_div_edit').addClass('disabled');
  $('.function_name_div_updated').addClass('disabled');
  $('.value_rendered').addClass('disabled');
  $('.var_name').addClass('disabled');
  $('.variable_rendered').addClass('disabled');

  $('.dropdown').addClass('disabled');
  $('.remove_parameter').addClass('disabled');

  $('.ui.dropdown.global_type.disabled').css('opacity', '1');
  $('.ui.dropdown.variable_type.disabled').css('opacity', '1');
  $('.ui.dropdown.function_return.disabled').css('opacity', '1');
  $('.ui.dropdown.parameter_type.disabled').css('opacity', '1');


  ivprogCore.CodeEditor.disable(true);
}

function ivprogTextualOrVisual () {

  if (settingsProgrammingTypes) {
    if (settingsProgrammingTypes == "textual") {
      $('.ivprog_visual_panel').css('display', 'none');
      $('.ivprog_textual_panel').css('display', 'block');
      $('.ivprog_textual_panel').removeClass('loading');

      $('.visual_coding_button').removeClass('active');
      $('.textual_coding_button').addClass('active');
      $('.visual_coding_button').addClass('disabled');

      let textual_code = algorithm_in_ilm;
      if(!textual_code) {
        textual_code = ivprogCore.LocalizedStrings.getUI("initial_program_code");
        textual_code = textual_code.replace(/\\n/g,"\n");
        textual_code = textual_code.replace(/\\t/g,"\t");
      }
      
      ivprogCore.CodeEditor.setCode(textual_code);
      ivprogCore.CodeEditor.disable(false);
    }
    if (settingsProgrammingTypes == "visual") {

    }
  }
}

function iassingIntegration () {

  // Disable by default...
  $('.assessment_button').addClass('disabled');

  prepareEnvironment();
  if (inIframe()) {
    orderIcons();
    orderWidth();
  }
}

// Função para preparar a interface para o professor criar atividade:
function prepareActivityCreation () {

  var menuTab = $('<div class="ui top attached tabular menu">'
        + '<a class="item active" data-tab="testcases">' + LocalizedStrings.getUI('text_teacher_test_case') + '</a>'
        + '<a class="item" data-tab="algorithm">' + LocalizedStrings.getUI('text_teacher_algorithm') + '</a>'
        + '<a class="item" data-tab="settings">' + LocalizedStrings.getUI('text_teacher_config') + '</a>'
        + '</div>'
        + '<div class="ui bottom attached tab segment active tab_test_cases" data-tab="testcases"></div>'
        + '<div class="ui bottom attached tab segment tab_algorithm" data-tab="algorithm"></div>'
        + '<div class="ui bottom attached tab segment tab_settings" data-tab="settings"></div>');

  menuTab.insertBefore('.add_accordion');
  $('.tabular.menu .item').tab();

  $('.main_title').remove();
  $('.ui.accordion').addClass('styled');

  $('<div class="content_margin"></div>').insertBefore($('.add_accordion').find('.content').find('.div_to_body'));

  $('<div class="ui checkbox"><input type="checkbox" name="include_algo" class="include_algo" tabindex="0" class="hidden"><label>'+LocalizedStrings.getUI('text_teacher_algorithm_include')+'</label></div>').insertAfter('.content_margin');

  var cases_test_div = $('<div></div>');

  $('.tab_test_cases').append(cases_test_div);

  var config_div = $('<div></div>');

  $('.tab_settings').append(config_div);

  $('.ui.checkbox').checkbox();

  $('.tab_algorithm').append($('.add_accordion'));

  prepareTableSettings(config_div);

  prepareTableTestCases(cases_test_div);

  if (inIframe()) {
      $('.ui.styled.accordion').css('width', '96%');
  }
}

function prepareTableTestCases (div_el) {

  var table_el = '<form name="test_cases"><table class="ui blue table"><thead><tr><th width="30px">#</th><th>'+LocalizedStrings.getUI('text_teacher_test_case_input')+'</th><th>'+LocalizedStrings.getUI('text_teacher_test_case_output')+'</th><th width="80px">'+LocalizedStrings.getUI('text_teacher_test_case_actions')+'</th></tr></thead>'
    + '<tbody class="content_cases"></tbody></table></form>';

  div_el.append(table_el);

  var table_buttons = '<table class="table_buttons"><tr><td>'
    + '<button class="ui teal labeled icon button button_add_case"><i class="plus icon"></i>'+LocalizedStrings.getUI('text_teacher_test_case_add')+'</button>'
    + '</td><td class="right_align">'
    + '<button class="ui orange labeled icon button button_generate_outputs"><i class="sign-in icon"></i>'+LocalizedStrings.getUI('text_teacher_generate_outputs')+'</button>'
    + '</td></tr></table>';

  div_el.append(table_buttons);

  div_el.append($('<div class="ui basic modal"><div class="content"><p>Olá</p></div><div class="actions"><div class="ui green ok inverted button">Fechar</div></div></div>'));

  $('.button_add_case').on('click', function(e) {
    addTestCase();
  });
  $('.button_generate_outputs').on('click', function(e) {
    generateOutputs();
  });
}

function showAlert (msg) {
  $('.ui.basic.modal .content').html('<h3>'+msg+'</h3>');
  $('.ui.basic.modal').modal('show');
}

function generateOutputs () {
  if (window.program_obj.functions.length == 1 && window.program_obj.functions[0].commands.length == 0) {
    showAlert(LocalizedStrings.getUI('text_teacher_generate_outputs_algorithm'));
    return;
  }
  // código:
  var code_teacher = window.generator();
  // array com as entradas já inseridas:
  var test_cases = JSON.parse(prepareTestCases().replace('"testcases" :', ''));
  ivprogCore.autoGenerateTestCaseOutput(code_teacher, test_cases).catch(function (error) {
    showAlert("Houve um erro durante a execução do seu programa: "+error.message);
  });

}

function outputGenerated (test_cases) {
  var fields = $('.text_area_output');
  /*for (var i = 0; i < test_cases.length; i++) {
    $(fields[i]).val('');
    for (var j = 0; j < test_cases[i].output.length; j++) {
      $(fields[i]).val($(fields[i]).val() + test_cases[i].output[j]);
      if (j < test_cases[i].output.length - 1) {
        $(fields[i]).val($(fields[i]).val() + '\n');
      }
    }
    $(fields[i]).attr('rows', test_cases[i].output.length);
  }*/
  animateOutput(fields, test_cases, 0);


}

function animateOutput (list, test_cases, index) {
  if (list.length == index) return;
  $(list[index]).val('');
  for (var j = 0; j < test_cases[index].output.length; j++) {
    console.log(test_cases[index].output[j].charCodeAt(0));
    $(list[index]).val($(list[index]).val() + test_cases[index].output[j]);
    if (j < test_cases[index].output.length - 1) {
      $(list[index]).val($(list[index]).val() + '\n');
    }
  }
  $(list[index]).attr('rows', test_cases[index].output.length);

  $(list[index]).effect('highlight', null, 50, function() {
    animateOutput(list, test_cases, index + 1);
  });
}

var hist = false;

function addTestCase (test_case = null) {
  var new_row = null;
  if (test_case) {
    var text_row = '';

    text_row += '<tr><td class="counter"></td><td class="expandingArea"><textarea rows="'+test_case.input.length+'" name="input" class="text_area_input">';

    for (var i = 0; i < test_case.input.length; i ++) {
      text_row += test_case.input[i];
      if ((i + 1) < test_case.input.length) {
        text_row += '\n';
      }
    }

    text_row += '</textarea></td><td class="expandingArea"><textarea rows="'+test_case.output.length+'" name="output" class="text_area_output">';

    for (var i = 0; i < test_case.output.length; i ++) {
      text_row += test_case.output[i];
      if ((i + 1) < test_case.output.length) {
        text_row += '\n';
      }
    }

    text_row += '</textarea></td><td class="btn_actions"><div class="ui button_remove_case"><i class="red icon times large"></i></div></td></tr>';

    new_row = $(text_row);
  } else {
      new_row = $('<tr><td class="counter"></td><td class="expandingArea"><textarea rows="1" name="input" class="text_area_input"></textarea></td><td class="expandingArea"><textarea rows="1" name="output" class="text_area_output"></textarea></td><td class="btn_actions"><div class="ui button_remove_case"><i class="red icon times large"></i></div></td></tr>');
  }
  $('.content_cases').append(new_row);

  new_row.find('.button_remove_case').click(function(e) {
      new_row.remove();
      updateTestCaseCounter();
  });

  new_row.find('textarea').on('input', function(e) {
      var lines = $(this).val().split('\n').length;
      $(this).attr('rows', lines);
  });

  updateTestCaseCounter();

  $('.text_area_output').keydown(function(e) {
    var code = e.keyCode || e.which;
    if (code == 9 && $(this).closest("tr").is(":last-child")) {
      hist = true;
      addTestCase();
    }
  });
  if (test_case == null) {
    if (!hist) {
      $( ".content_cases tr:last" ).find('.text_area_input').focus();
    } else {
      hist = false;
    }
  }
}

function updateTestCaseCounter () {
    var i = 1;
    $( ".content_cases" ).find('tr').each(function() {
      $( this ).find('.counter').text(i);
      ++i;
    });
}


function prepareTableSettings (div_el) {

  div_el.append('<div class="ui segment settings_topic"><h3 class="ui header"><i class="window maximize outline icon"></i><div class="content">'+LocalizedStrings.getUI('text_config_programming')+'</div></h3>'
    +'<div class="content content_segment_settings"><form name="settings_programming_type"><div class="ui stackable five column grid">'
    +'<div class="column"><div class="ui radio"><input type="radio" name="programming_type" id="programming_textual" value="textual" tabindex="0" class="hidden small"><label for="programming_textual">'+LocalizedStrings.getUI('text_config_programming_textual')+'</label></div></div>'
    +'<div class="column"><div class="ui radio"><input type="radio" name="programming_type" id="programming_visual" value="visual" checked tabindex="0" class="hidden small"><label for="programming_visual">'+LocalizedStrings.getUI('text_config_programming_visual')+'</label></div></div>'
    +'</div></form></div></div>');

  div_el.append('<div class="ui segment settings_topic"><h3 class="ui header"><i class="qrcode icon"></i><div class="content">'+LocalizedStrings.getUI('text_teacher_data_types')+'</div></h3>'
    +'<div class="content content_segment_settings"><form name="settings_data_types"><div class="ui stackable five column grid">'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="integer_data_type" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('type_integer')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="real_data_type" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('type_real')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="text_data_type" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('type_text')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="boolean_data_type" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('type_boolean')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="void_data_type" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('type_void')+'</label></div></div>'
    +'</div></form></div></div>');

  div_el.append('<div class="ui segment settings_topic"><h3 class="ui header"><i class="code icon"></i><div class="content">'+LocalizedStrings.getUI('text_teacher_commands')+'</div></h3>'
    +'<div class="content content_segment_settings"><form name="settings_commands"><div class="ui stackable three column grid">'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_read" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_read_var')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_write" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_write_var')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_comment" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_comment')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_attribution" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_attribution')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_functioncall" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_functioncall')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_iftrue" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_iftrue')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_repeatNtimes" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_repeatNtimes')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_while" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_whiletrue')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_dowhile" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_dowhiletrue')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="commands_switch" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_switch')+'</label></div></div>'
    +'</div></form></div></div>');

  div_el.append('<div class="ui segment settings_topic"><h3 class="ui header"><i class="terminal icon"></i><div class="content">'+LocalizedStrings.getUI('text_teacher_functions')+'</div></h3>'
    +'<div class="content content_segment_settings"><form name="settings_functions"><div class="ui stackable one column grid">'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="functions_creation" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_teacher_create_functions')+'</label></div></div>'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="functions_move" checked tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_teacher_create_movement_functions')+'</label></div></div>'
    +'</div></form></div></div>');

  div_el.append('<div class="ui segment settings_topic"><h3 class="ui header"><i class="filter icon"></i><div class="content">'+LocalizedStrings.getUI('text_teacher_filter')+'</div><i class="circular inverted teal question icon"></i></h3>'
    +'<div class="content content_segment_settings"><form name="settings_filter"><div class="ui stackable one column grid">'
    +'<div class="column"><div class="ui checkbox"><input type="checkbox" name="filter_active" tabindex="0" class="hidden small"><label>'+LocalizedStrings.getUI('text_teacher_filter_active')+'</label></div></div>'
    +'</div></form></div></div>');

  $('.circular.inverted.teal.question.icon').popup({
    content : LocalizedStrings.getUI("text_teacher_filter_help"),
    delay: {
      show: 750,
      hide: 0
    }
  });

  $('.ui.checkbox').checkbox();

}

function getTrackingLogs () {
  return ivprogCore.getLogsAsString();
  // var ret = "";
  // for (var i = 0; i < trackingMatrix.length; ++i) {
  //   ret += "\n" + trackingMatrix[i][0] + "," + trackingMatrix[i][1] + "," + trackingMatrix[i][2];
  //   if (trackingMatrix[i][3] === 1) {
  //     ret += ',' + trackingMatrix[i][3] + ',"' + trackingMatrix[i][4] + '"';
  //   }
  // }
  // return ret;
}

// Tracking mouse movements
// var trackingMatrix = [];

/* function adCoords(e, code){
  var x = e.pageX;
  var y = e.pageY;
  if (code === 1) {
    return [new Date().getTime(), x, y, code, e.target.classList['value']];
  } else {
    return [x, y, code];
  }
} */

// $( document ).ready(function() {

//     if (inIframe()) {
//         orderIcons();
//         orderWidth();
//     }
//     renderAlgorithm();
// });

function orderWidth() {
  $('.ui.raised.container.segment.div_to_body').css('width', '100%');
  $('.ui.one.column.container.segment.ivprog_visual_panel').css('width', '100%');
}

function orderIcons() {
  $('.ui.one.column.doubling.stackable.grid.container').css('display', 'none');
  $('.only_in_frame').css('display', 'block');
}


function inIframe () {
  try {
    return window.self !== window.top;
  } catch (e) {
    return true;
  }
}


function full_screen() {
  // check if user allows full screen of elements. This can be enabled or disabled in browser config. By default its enabled.
  //its also used to check if browser supports full screen api.
  if("fullscreenEnabled" in document || "webkitFullscreenEnabled" in document || "mozFullScreenEnabled" in document || "msFullscreenEnabled" in document) {
    if(document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled) {
      var element = document.getElementById("ui_main_div");
      //requestFullscreen is used to display an element in full screen mode.
      if("requestFullscreen" in element) {
        element.requestFullscreen();
      }
      else if ("webkitRequestFullscreen" in element) {
        element.webkitRequestFullscreen();
      }
      else if ("mozRequestFullScreen" in element) {
        element.mozRequestFullScreen();
      }
      else if ("msRequestFullscreen" in element) {
        element.msRequestFullscreen();
      }
    }
  } else {
    $('.expand_button').addClass('disabled');
  }
}

function getAutoEvalOriginalData () {
  return parent.getAutoEvalOriginalData();
}

function teacherAutoEval (data) {
  previousContent = data;
  // Ver arquivo js/util/iassignHelpers.js
  var content = ivprogCore.prepareActivityToStudentHelper(data).getOrElse(null);
  if(!content) {
    showInvalidData();
    return;
  }
  // Casos de testes agora são delegados ao tratamento apropriado pela função acima
  // var testCases = content.testcases;
  settingsProgrammingTypes = content.settingsProgrammingType;
  settingsDataTypes = content.settingsDataTypes;
  settingsCommands = content.settingsCommands;
  settingsFunctions = content.settingsFunctions;
  settingsFilter = content.settingsFilter;

  if (content.algorithmInIlm != null) {
    algorithm_in_ilm = content.algorithmInIlm;
    parsePreviousAlgorithm();
    var originalData = getAutoEvalOriginalData();
    ivprogCore.autoEval(originalData, parent.postResultAutoEval);
  }

  ivprogTextualOrVisual();
  if (settingsFilter && settingsFilter[0]) {

    blockAllEditingOptions(); 
  }
}

function displayGrade(grade) {
  alert(grade);
}

function showInvalidData () {
  $('.ui.height_100.add_accordion').dimmer({
    closable: false
  });
  $('.dimmer_content_message h3').html(LocalizedStrings.getUI('text_message_error_activity_file'));
  $('.dimmer_content_message button').text(LocalizedStrings.getUI('text_message_error_activity_reload'));
  $('.dimmer_content_message').css('display', 'block');
  $('.ui.height_100.add_accordion').dimmer('add content', '.dimmer_content_message');
  $('.ui.height_100.add_accordion').dimmer('show');
}
