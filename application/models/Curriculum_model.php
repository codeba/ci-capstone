<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Curriculum_model extends MY_Model
{

        public function __construct()
        {
                $this->table       = 'curriculums';
                $this->primary_key = 'curriculum_id';

                $this->before_create[] = '_add_created_by';
                $this->before_update[] = '_add_updated_by';

                $this->_relations();
                $this->_form();
                $this->_config();

                parent::__construct();
        }

        protected function _add_created_by($data)
        {
                $this->load->helper('mymodel');
                $data                    = remove_empty_before_write($data);
                $data['created_user_id'] = $this->ion_auth->get_user_id(); //add user_id
                return $data;
        }

        protected function _add_updated_by($data)
        {
                $this->load->helper('mymodel');
                $data                    = remove_empty_before_write($data);
                $data['updated_user_id'] = $this->ion_auth->get_user_id(); //add user_id
                return $data;
        }

        private function _config()
        {
                $this->timestamps        = TRUE; //(bool) $this->config->item('my_model_timestamps');
                $this->return_as         = 'object'; //$this->config->item('my_model_return_as');
                $this->timestamps_format = 'timestamp'; //$this->config->item('my_model_timestamps_format');


                $this->cache_driver              = 'file'; //$this->config->item('my_model_cache_driver');
                $this->cache_prefix              = 'cicapstone'; //$this->config->item('my_model_cache_prefix');
                /**
                 * some of field is not required, so remove it in array when no value, in inside the *->from_form()->insert() in core MY_Model,
                 */
                $this->remove_empty_before_write = TRUE; //(bool) $this->config->item('my_model_remove_empty_before_write');
                $this->delete_cache_on_save      = TRUE; //(bool) $this->config->item('my_model_delete_cache_on_save');
        }

        private function _relations()
        {
                $this->has_one['user_created'] = array(
                    'foreign_model' => 'User_model',
                    'foreign_table' => 'users',
                    'foreign_key'   => 'id',
                    'local_key'     => 'created_user_id'
                );
                $this->has_one['user_updated'] = array(
                    'foreign_model' => 'User_model',
                    'foreign_table' => 'users',
                    'foreign_key'   => 'id',
                    'local_key'     => 'updated_user_id'
                );
                $this->has_one['course']       = array(
                    'foreign_model' => 'Course_model',
                    'foreign_table' => 'courses',
                    'foreign_key'   => 'course_id',
                    'local_key'     => 'course_id'
                );
                /**
                 * seperated table
                 */
                $this->has_many['requisites']  = array(
                    'foreign_model' => 'Requisites_model',
                    'foreign_table' => 'requisites',
                    'foreign_key'   => 'curriculum_subject_id',
                    'local_key'     => 'curriculum_subject_id'
                );
        }

        private function _form()
        {

                $this->rules = array(
                    'insert' => $this->_insert(),
                    'update' => $this->_update()
                );
        }

        private function _common()
        {
                return array();
        }

        private function _insert()
        {
                return array(
                    'curriculum_description'           => array(
                        'label' => lang('curriculumn_description'),
                        'field' => 'desc',
                        'rules' => 'trim|required|min_length[2]|max_length[100]'
                    ),
                    'curriculum_effective_school_year' => array(
                        'label' => lang('curriculumn_effective_year'),
                        'field' => 'year',
                        'rules' => 'trim|required|exact_length[9]'
                    ),
                    'curriculum_status'                => array(
                        'label' => lang('curriculumn_status'),
                        'field' => 'status',
                        'rules' => 'trim'
                    ),
                    'course_id'                        => array(
                        'label' => lang('curriculumn_course'),
                        'field' => 'course',
                        'rules' => 'trim|is_natural_no_zero|required'
                    ),
                );
        }

        private function _update()
        {
                return array();
        }

        public function button_link($curriculum_id, $subject_code, $subject_description)
        {
                $this->load->helper('inflector');
                return table_row_button_link(//just a subject_code with redirection, directly to curriculum with highlighten phrase
                        //----------------------------------link
                        'curriculums/view?curriculum-id=' .
                        $curriculum_id .
                        '&highlight=' .
                        $subject_code .
                        '#' .
                        dash($subject_code),
                        //----------------------------------user link view
                             $subject_code,
                        //additonal for table_row_button_link()
                             NULL,
                        //----------------------------------attributes
                             array(
                    'title' => $subject_description, //pop up subject description when hover mouse
                    'class' => "tip-bottom"
                        )
                );
        }

        /**
         * this will be use in
         * adding/update curriculum_id in enrollment, 
         * 
         * @param int $_course_id_
         * @return boolean
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function get_active_currilumn_by_course_id($_course_id_ = NULL)
        {
                if (is_null($_course_id_))
                {
                        show_error('no corriculum provided.');
                }
                /**
                 * get_all to check also if only one active in curriculum by course_id
                 */
                $curriculum_obj = $this->where(array(
                            'course_id'         => $_course_id_,
                            'curriculum_status' => TRUE//only needed is the active
                        ))->get_all();

                if ($curriculum_obj)
                {
                        if (count($curriculum_obj) > 1)
                        {
                                /**
                                 * the result is more than one, 
                                 */
                                $this->session->set_flashdata('message', bootstrap_error('Curriculum is more than 1 active.'));
                                return FALSE;
                        }
                        /**
                         * convert in single row, because we used get_all() (more than one rows)
                         * 
                         * then get the curriculum_id
                         */
                        return $curriculum_obj[0]->curriculum_id; //expected only one result [no need advance loop] 
                }
                /**
                 * no curriculum found
                 */
                $this->session->set_flashdata('message', bootstrap_error('No curriculumn found'));
                return FALSE;
        }

}
