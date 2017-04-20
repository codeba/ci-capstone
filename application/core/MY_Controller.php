<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

        function __construct()
        {
                parent::__construct();

//                $this->load->dbutil();
//                $DB_NAME = 'ci_capstone';
//                if (!$this->dbutil->database_exists($DB_NAME))
//                {
//                        $this->dbforge->create_database($DB_NAME);
//                }

                if ($this->migration->current())
                {
                        $this->delete_all_query_cache();
                }

                /**
                 * there is a back button, 
                 * still reach this, so ignore this
                 */
                if ($this->session->has_userdata('user_id'))
                {

                        /**
                         * we set here , must before check login or before calling a trigger for a name of event
                         */
                        $this->ion_auth->set_hook(
                                'logged_in', 'check_log_multiple_user', $this/* $this because the class already extended */, 'check_if_multiple_logged_in_one_user', array()
                        );
                }
                /**
                 * update enrollment status to FALSE in ALL not current semester and school_year
                 */
                $this->Enrollment_model->unenroll_all_past_term();
        }

        public function just_notigy_user_remember()
        {
                /**
                 * just a temporary
                 */
                $this->session->set_flashdata('message', bootstrap_success('User Exntended Login!!'));
        }

        /**
         * 
         * @param type $view
         * @param type $data
         * @param type $returnhtml
         * @return type
         * @author ion_auth
         */
        public function render($view, $data = null, $returnhtml = false)
        {//I think this makes more sense
                //$this->viewdata = (empty($data)) ? $this->data : $data;
                $view_html = $this->load->view($view, $data, $returnhtml);

                if ($returnhtml)
                {
                        return $view_html; //This will return html on 3rd argument being true
                }
        }


        /**
         * @return string | all user_group of current logged user
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function current_group_string($type = 'description')
        {
                $return = '';
                foreach ($this->ion_auth->get_users_groups()->result() as $g)
                {
                        $return .= $g->$type . '|';
                }
                return trim($return, '|');
        }

        /**
         * delete all query cache by using one of model, cant statically call MY_Model so i did this 
         *       
         * using this with ion_auth update/insert/
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>  
         */
        public function delete_all_query_cache()
        {
                $this->load->model('User_model');
                $this->User_model->delete_cache();
        }

}

class CI_Capstone_Controller extends MY_Controller
{

        function __construct()
        {
                parent::__construct();
        }

        /**
         * render views at one call
         * 
         * @param view $content current view page to be render
         * @param data $data data to be render also in current view 
         * @return null if content is missing
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function render($content, $data = NULL, $returnhtml = FALSE)
        {
                if ( ! $content)
                {
                        return NULL;
                }
                $data['user_info']   = $this->session->userdata('user_fullname') .
                        ' [' . $this->session->userdata('user_groups_descriptions') . ']';
                $data['search_form'] = parent::render('admin/_templates/search', $data, TRUE);
                $data['navigations'] = navigations_main();

                $template['header']  = parent::render('admin/_templates/header', $data, TRUE);
                $template['content'] = parent::render($content, $data, TRUE);
                $template['footer']  = parent::render('admin/_templates/footer', $data, TRUE);

                parent::render('template', $template, $returnhtml);
        }

        /**
         * 
         * @param array $header header
         * @param array $table_data_rows rows
         * @param string $table_config table bootstrap
         * @param string $caption_lang caption
         * @param string $pagination | must generated html pagination | default = FALSE
         * @param bool $return_html either return html or not
         * @return string | generated html table with header/data/table-type/pagination depend on parameters
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function table_bootstrap($header, $table_data_rows, $table_config_or_template, $header_lang, $pagination = FALSE, $return_html = FALSE, $caption = NULL, $bootsrap = TRUE)
        {

                $this->config->load('admin/table');
                $this->load->library('table');

                $temp_template = NULL;
                if (is_array($table_config_or_template))
                {
                        /**
                         * if array so its template
                         */
                        $temp_template = $table_config_or_template;
                }
                else
                {
                        /**
                         * just table open
                         */
                        $temp_template['table_open'] = $this->config->item($table_config_or_template);
                }
                $this->table->set_template($temp_template);

                $this->table->set_heading($header);
                $this->table->set_caption($caption);

                $_data['header_lang']      = $header_lang;
                $_data['table_data']       = $this->table->generate($table_data_rows);
                $_data['pagination']       = $pagination;
                $_data['bootstrap_output'] = $bootsrap;
                $generated_html_table      = parent::render('admin/_templates/table', $_data, $return_html);
                if ($return_html)
                {
                        return $generated_html_table;
                }
        }

        /**
         * 
         * @param string $_action
         * @param array $_inputs
         * @param string $_lang_header
         * @param string $_lang_button
         * @param string $_icon
         * @param array $_hidden_inputs
         * @param bool $return_html
         * @return string
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function form_boostrap($_action, $_inputs, $_lang_header, $_lang_button, $_icon, $_hidden_inputs = NULL, $return_html = FALSE, $_error = FALSE)
        {
                $_data['inputs']        = $_inputs;
                $_data['action']        = $_action;
                $_data['lang_header']   = $_lang_header;
                $_data['lang_button']   = $_lang_button;
                $_data['icon']          = $_icon;
                $_data['hidden_inputs'] = $_hidden_inputs;
                $_data['error']         = $_error;

                $generated_html_form = parent::render('admin/_templates/form', $_data, $return_html);
                if ($return_html)
                {
                        return $generated_html_form;
                }
        }

        /**
         * this will call using 
         * $this->trigger_events(array(_____ , 'post_update_user_successful')); line 1664 :Ion_auth_model.php
         * in success login
         * 
         * ,this is set hook in constructor in edit_user controller
         * 
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         * 
         */
        public function add_update_at_data_user_column($table, $user_id)
        {
                /**
                 * not using core My_Model, because, we needed is updated_at to be update
                 */
                /**
                 * for very specific, we use table set in ion auth config,
                 * see on set hook in edit_user controller
                 */
                return (bool) $this->db->update($table, array(
                            'updated_at' => time()
                                ), array('id' => $user_id));
        }

        /**
         * 
         * checking if one account log in another machine
         * ,this is set hook in constructor in MY_Controller
         * 
         * then will call this when trigger the name 'logged_in
         * 
         * 
         * this idea is came from https://github.com/benedmunds/CodeIgniter-Ion-Auth/issues/947
         * 
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function check_if_multiple_logged_in_one_user()
        {
                $user_current_session_id = $this->session->userdata('user_current_session_id');
                $session_id              = $this->User_model->get($this->ion_auth->get_user_id())->session_id;

                if ($session_id != $user_current_session_id)
                {
                        $message = 'another_logged_in_user_in_this_account';

                        redirect('auth/logout/' . $message, 'refresh');
                }
        }

}
