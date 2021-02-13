<?php

/**
 * Interface que deve ser implementada pelos tipos de iLM disponíveis.
 * Todos os métodos precisam ser implementados para permitir a correta manipulação dos iLM.
 * 
 * Atenção: esta classe (ilm_handle) é abstrata e aquelas que venham a implementá-la
 * devem seguir o seguinte padrão: utilizar o nome da classe concreta todo em minúsculo
 * bem como o nome de seu arquivo .php, que deve ter exatamente o mesmo nome da classe.
 * As classes concretas devem estar na pasta 'ilm_handlers'.
 * 
 * @author Igor Moreira Félix
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * 
 */

interface ilm_handle {

    /**
     * Método para implementar a importação de um iLM
     */
    public static function import_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs);
    
    /**
     * Método para o recebimento de um novo iLM
     */
    public static function new_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs);
    
    /**
     * Método para tratar a edição de um iLM
     */
    public static function edit_ilm($param, $itemid, $files_extract, $contextuser);
    
    /**
     * Método para excluir um iLM
     */
    public static function delete_ilm($ilm_id);
    
    /**
     * Método para expotar um iLM
     */
    public static function export_ilm($ilm_id);
    
    /**
     * Método para copiar e preparar uma nova versão do iLM
     */
    public static function copy_new_version_ilm($param, $files_extract);
    
    /**
     * Exibe o iLM
     */
    public static function view_ilm($ilmid, $from);
    
    /**
     * Mostra a atividade no iLM
     */
    public static function show_activity_in_ilm($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion);
    
    /**
     * Gera as tags HTML para a exibição do iLM no navegador do usuário
     */
    public static function build_ilm_tags($ilm_id, $options = array());
        
}


