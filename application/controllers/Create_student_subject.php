<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Create_student_subject extends CI_Capstone_Controller
{


        private $_session_name_;
        private $_sub_off__added_obj_from_session;
        private $_total_unit;

        function __construct()
        {
                parent::__construct();
                $this->load->library('form_validation');
                $this->load->helper(array('day', 'time', 'number', 'inflector'));
                $this->load->model(array('Student_model', 'Curriculum_subject_model', 'Subject_model', 'Requisites_model'));
                $this->form_validation->set_error_delimiters('<span class="help-inline">', '</span>');
                $this->breadcrumbs->unshift(2, lang('index_student_heading'), 'students');
                /**
                 * session name for curriculum_subjects

                 * to modify the session name, make sure also modify in MY_controller
                 * 
                 *   in constructor
                 */
                $this->_session_name_                   = 'curriculum_subjects__subject_offer_ids';
                $this->_sub_off__added_obj_from_session = array();
        }

        public function index()
        {
                $this->Student_model->set_informations($this->input->get('student-id'));
                $this->breadcrumbs->unshift(3, 'View Student [ ' . $this->student->school_id . ' ]', 'students/view?student-id=' . $this->student->id);
                $this->breadcrumbs->unshift(4, lang('add_student_subject_label'), 'create-student-subject?student-id=' . $this->student->id);


                $error_message = '';

                if ($this->input->post('submit'))
                {
                        if ($this->session->has_userdata($this->_session_name_))
                        {
                                $from_session = $this->session->userdata($this->_session_name_);
                                if ($from_session)
                                {
                                        $this->load->model('Students_subjects_model');

                                        /**
                                         * start the DB transaction
                                         */
                                        $this->db->trans_start();

                                        $all_inserted = TRUE;
                                        foreach ($from_session as $subj_offr_id)
                                        {
                                                $gen_id = $this->Students_subjects_model->insert(array(
                                                    'enrollment_id'    => $this->student->enrollment_id,
                                                    'subject_offer_id' => $subj_offr_id,
                                                    'created_user_id'  => $this->session->userdata('user_id')
                                                ));
                                                if ( ! $gen_id)
                                                {
                                                        $all_inserted = FALSE;
                                                        break;
                                                }
                                        }
                                        if ( ! $all_inserted)
                                        {
                                                /**
                                                 * rollback database
                                                 */
                                                $this->db->trans_rollback();
                                        }
                                        else
                                        {
                                                if ($this->db->trans_commit())
                                                {
                                                        $this->_reset_session();
                                                        $this->session->set_flashdata('message', 'all subjects added!');
                                                        // redirect(site_url('students/view?student-id=' . $s_id), 'refresh');
                                                }
                                        }
                                }
                                else
                                {
                                        $error_message = 'invalid';
                                }
                        }
                        else
                        {
                                $error_message = 'please select subject.';
                        }
                }

                $this->_form_view($error_message);
        }

        /**
         * this info will now include in submit form
         * 
         * 
         * 
         * @return array
         */
        private function _student_information()
        {
                $unit = 'unit';
                if ($this->_total_unit > 0)
                {
                        $unit = plural($unit);
                }
                $inputs['totalunit'] = array(
                    'name'     => 'xx',
                    'value'    => $this->_total_unit . ' ' . $unit,
                    'type'     => 'text',
                    'lang'     => 'added_subjects_total_units',
                    'disabled' => ''
                );

                $inputs['school_id'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->school_id,
                    'type'     => 'text',
                    'lang'     => 'index_student_school_id_th',
                    'disabled' => ''
                );

                $inputs['firstname'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->firstname,
                    'type'     => 'text',
                    'lang'     => 'create_student_firstname_label',
                    'disabled' => ''
                );

                $inputs['middlename'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->middlename,
                    'type'     => 'text',
                    'lang'     => 'create_student_middlename_label',
                    'disabled' => ''
                );

                $inputs['lastname'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->lastname,
                    'type'     => 'text',
                    'lang'     => 'create_student_lastname_label',
                    'disabled' => ''
                );

                $inputs['course'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->course_code,
                    'type'     => 'text',
                    'lang'     => 'student_course_code', //in student lang
                    'disabled' => ''
                );

                $inputs['level'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->level_place,
                    'type'     => 'text',
                    'lang'     => 'index_student_year_level_th',
                    'disabled' => ''
                );

                $inputs['enrollstatus'] = array(
                    'name'     => 'xx',
                    'value'    => $this->student->is_enrolled(TRUE),
                    'type'     => 'text',
                    'lang'     => 'student_enroll_statuse',
                    'disabled' => ''
                );

                return $inputs;
        }

        private function _form_view($error_message)
        {



                /**
                 * view the available subject from curriculum
                 */
                $this->_add_session_subjects();

                /**
                 * view all subject available to add
                 */
                $this->data['student_subject_curriculum_table'] = $this->_table_view('db');

                /**
                 * subject added to session
                 */
                $this->data['added_subject_table'] = $this->_table_view('session');

                if ($this->session->has_userdata($this->_session_name_))
                {
                        /**
                         * button for reset session
                         * will view this if has session on added subject
                         */
                        $this->data['reset_subject_offer_session'] = MY_Controller::_render('admin/_templates/button_view', array(
                                    'href'         => 'create-student-subject/reset-subject-offer-session?student-id=' . $this->student->id,
                                    'button_label' => 'reset subject offer session',
                                    'extra'        => array('class' => 'btn btn-success icon-edit'),
                                        ), TRUE);
                }

                /**
                 * generate information for curretn student
                 */
                $this->data['student_subject_form'] = $this->form_boostrap(
                        'create-student-subject?student-id=' . $this->student->id, $this->_student_information(), 'student_information', 'add_student_subject_label', 'user', NULL, TRUE, $error_message
                );

                $this->data['bootstrap'] = $this->_bootstrap();
                $this->_render('admin/create_student_subject', $this->data);
        }

        /**
         * unset session for subject offer 
         */
        public function reset_subject_offer_session()
        {
                $this->_reset_session();
                redirect('create-student-subject?student-id=' . $this->input->get('student-id'), 'refresh');
        }

        private function _reset_session()
        {
                if ($this->session->has_userdata($this->_session_name_))
                {
                        $this->session->unset_userdata($this->_session_name_);
                        $this->session->set_flashdata('message', 'reset done!');
                }
        }

        /**
         * unset one index/value in session for subject offer 
         */
        public function unset_value_subject_offer_session()
        {
                if ($this->session->has_userdata($this->_session_name_))
                {
                        $this->load->helper('session');
                        unset_value_userdata_array($this->_session_name_, $this->input->get('subject-offer-id'));
                        $this->session->set_flashdata('message', 'removed done!');
                }
                redirect('create-student-subject?student-id=' . $this->input->get('student-id'), 'refresh');
        }

        private function _table_view($type)
        {
                $cur_subj_obj      = NULL;
                $table_data        = array();
                $this->_total_unit = 0;

                switch ($type)
                {
                        case 'db':
                                $cur_subj_obj = $this->student->get_all_subject_available_to_enroll();
                                $lang_caption = 'available_subjects_to_enroll'; /* lang in in students_lang */
                                $header_col   = lang('add_student_subject_label');
                                break;
                        case 'session':
                                $cur_subj_obj = $this->_sub_off__added_obj_from_session;
                                $lang_caption = 'added_subjects_to_enroll'; /* lang in in students_lang */
                                $header_col   = lang('remove_subjects_to_enroll');
                                break;
                }

                if ($cur_subj_obj)
                {
                        foreach ($cur_subj_obj as $s)
                        {

                                if ( ! isset($s->curriculum_subject->curriculum_subject_id))
                                {
                                        // i made an issue for this
                                        //https://github.com/avenirer/CodeIgniter-MY_Model/issues/231
                                        //this is temporary,(if fixed will refactor)
                                        continue;
                                }

                                if ( ! isset($s->subject_line))
                                {
                                        continue;
                                }

                                if ($type == 'db')
                                {
                                        if ($this->_is_in_session($s->subject_offer_id))
                                        {
                                                $this->_sub_off__added_obj_from_session[] = $s; //add to added object
                                                continue; //skip, already in session
                                        }
                                }

                                $output = array(
                                    number_place($s->curriculum_subject->curriculum_subject_year_level) . ' Year',
                                    semesters($s->curriculum_subject->curriculum_subject_semester),
                                    anchor(//just a subject code with redirection, directly to curriculum with highlighten phrase
                                            //----------------------------------link
                                            'curriculums/view?curriculum-id=' .
                                            $s->curriculum_subject->curriculum_id .
                                            '&highlight=' .
                                            $s->subject->subject_code .
                                            '#' .
                                            dash($s->subject->subject_code),
                                            //----------------------------------user link view
                                                 $s->subject->subject_code,
                                            //----------------------------------attributes
                                                 array(
                                        'title' => $s->subject->subject_description//pop up subject description when hover mouse
                                            )
                                    ),
                                    $s->faculty->last_name . ', ' . $s->faculty->first_name
                                );

                                $line = array();
                                $inc  = 0;
                                foreach ($s->subject_line as $su_l)
                                {
                                        $inc ++;
                                        $schd = array(
                                            subject_offers_days($su_l),
                                            convert_24_to_12hrs($su_l->subject_offer_line_start),
                                            convert_24_to_12hrs($su_l->subject_offer_line_end),
                                            $su_l->room->room_number
                                        );
                                        $line = array_merge($line, $schd);
                                }
                                if ($inc === 1)
                                {
                                        $line = array_merge($line, array('--', '--', '--', '--'));
                                }
                                $btn_link = '';
                                switch ($type)
                                {
                                        case 'db': $btn_link = anchor(
                                                        site_url('create-student-subject?student-id=' . $this->student->id .
                                                                '&subject-offer-id=' . $s->subject_offer_id), lang('add_student_subject_label')
                                                );
                                                break;
                                        case 'session':
                                                $btn_link = anchor(
                                                        site_url('create-student-subject/unset-value-subject-offer-session?student-id=' . $this->student->id . '&subject-offer-id=' . $s->subject_offer_id), lang('remove_subjects_to_enroll')
                                                );
                                                break;
                                }
                                $line[]       = $s->curriculum_subject->curriculum_subject_units . ' unit';
                                $line         = array_merge($line, array($btn_link));
                                $table_data[] = array_merge($output, $line);

                                /**
                                 * sumation of unit
                                 */
                                $this->_total_unit += $s->curriculum_subject->curriculum_subject_units;
                        }
                }
                /*
                 * Table headers
                 */
                $header   = array(
//                    'semester',
//                    lang('index_subject_id_th'),
//                    lang('index_subject_offer_start_th'),
//                    lang('index_subject_offer_end_th'),
//                    lang('index_subject_offer_days_th'),
//                    lang('index_room_id_th'),
//                    lang('index_user_id_th'),

                    'year',
                    'semester',
                    'Subject',
                    'Faculty',
                    'Days1',
                    'Start1',
                    'End1',
                    'Room1',
                    'Days2',
                    'Start2',
                    'End2',
                    'Room2',
                    'unit'
                );
                $header[] = $header_col;

                return $this->table_bootstrap($header, $table_data, 'table_open_bordered', $lang_caption/* lang in in students_lang */, FALSE, TRUE);
        }

        /**
         * check if schedule already in session
         * 
         * @param type $subject_offer_id
         * @return boolean
         */
        private function _is_in_session($subject_offer_id)
        {
                if ( ! $this->session->has_userdata($this->_session_name_))
                {
                        return FALSE;
                }
                return (bool) in_array($subject_offer_id, $this->session->userdata($this->_session_name_));
        }

        private function _add_session_subjects()
        {
                /**
                 * skip is required id not found
                 */
                if ( ! $this->input->get('subject-offer-id'))
                {
                        return;
                }
                /**
                 * check the id
                 */
                $subject_offer_id = check_id_from_url('subject_offer_id', 'Subject_offer_model', 'subject-offer-id')->subject_offer_id;

                /**
                 * validate the subject if conflict with one value in session
                 */
                if ($this->_validate_subject($subject_offer_id))
                {
                        /**
                         * load the helper
                         */
                        $this->load->helper('session');
                        set_userdata_array($this->_session_name_, (string) $subject_offer_id, TRUE); //parameter TRUE to check unique valus on session array
                }
        }

        /**
         * this will check the comflict in adding schedule,
         * 
         * @param type $subject_id
         * @return boolean
         *  @author Lloric Garcia <emorickfighter@gmail.com>
         */
        private function _validate_subject($subject_id)
        {

                /**
                 * get all subject_offer_ids by SUBJECT_ID && current_semester && current_school_year
                 */
                return TRUE;
        }

        /**
         * 
         * @return array
         *  @author Lloric Garcia <emorickfighter@gmail.com>
         */
        private function _bootstrap()
        {
                /**
                 * for header
                 */
                $header = array(
                    'css' => array(
                        'css/bootstrap.min.css',
                        'css/bootstrap-responsive.min.css',
                        'css/colorpicker.css',
                        'css/datepicker.css',
                        'css/uniform.css',
                        'css/select2.css',
                        'css/matrix-style.css',
                        'css/matrix-media.css',
                        'css/bootstrap-wysihtml5.css',
                        'font-awesome/css/font-awesome.css" rel="stylesheet',
                        'http://fonts.googleapis.com/css?family=Open+Sans:400,700,800',
                    /**
                     * wizard
                     */
//                        'css/bootstrap.min.css',
//                        'css/bootstrap-responsive.min.css',
//                        'css/matrix-style.css',
//                        'css/matrix-media.css',
//                        'font-awesome/css/font-awesome.css',
//                        'http://fonts.googleapis.com/css?family=Open+Sans:400,700,800',
                    /**
                     * addition for form
                     */
//                        'css/colorpicker.css',
//                        'css/datepicker.css',
//                        'css/uniform.css',
//                        'css/select2.css',
//                        'css/bootstrap-wysihtml5.css',
                    ),
                    'js'  => array(
                    ),
                );
                /**
                 * for footer
                 */
                $footer = array(
                    'css' => array(
                    ),
                    'js'  => array(
                        'js/jquery.min.js',
                        'js/jquery.ui.custom.js',
                        'js/bootstrap.min.js',
                        'js/bootstrap-colorpicker.js',
                        'js/bootstrap-datepicker.js',
                        'js/jquery.toggle.buttons.js',
                        'js/masked.js',
                        'js/jquery.uniform.js',
                        'js/select2.min.js',
                        'js/matrix.js',
                        'js/matrix.form_common.js',
                        'js/wysihtml5-0.3.0.js',
                        'js/jquery.peity.min.js',
                        'js/bootstrap-wysihtml5.js',
                        /**
                         * wizard
                         * 
                         */
//                        'js/jquery.min.js',
//                        'js/jquery.ui.custom.js',
//                        'js/bootstrap.min.js',
//                        'js/jquery.validate.js',
//                        'js/jquery.wizard.js',
//                        'js/matrix.js',
                        /*
                         * for frontend validation
                         */
                        site_url('assets/framework/bootstrap/admin/matrixwizard.js'),
                    /**
                     * addition for form
                     */
//                        'js/bootstrap-colorpicker.js',
//                        'js/bootstrap-datepicker.js',
//                        'js/jquery.toggle.buttons.js',
//                        'js/masked.js',
//                        'js/jquery.uniform.js',
//                        'js/select2.min.js',
//                        'js/matrix.form_common.js',
//                        'js/wysihtml5-0.3.0.js',
//                        'js/jquery.peity.min.js',
//                        'js/bootstrap-wysihtml5.js',
                    ),
                );
                /**
                 * footer extra
                 */
                /**
                 * addition for form
                 */
//                $footer_extra = "<script>
//                        $('.textarea_editor').wysihtml5();
//                </script>";

                $footer_extra = "<script>
	$('.textarea_editor').wysihtml5();
</script>";
                return generate_link_script_tag($header, $footer, $footer_extra);
        }

}
