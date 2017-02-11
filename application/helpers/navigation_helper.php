<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('navigations_main'))
{

        /**
         * 
         * @return type
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        function navigations_main()
        {
                return array(
                    'home'           =>
                    array(
                        'label' => 'Home',
                        'desc'  => 'Home Description',
                        'icon'  => 'home',
                    ),
                    //sub menu
                    'user_menus'     =>
                    array(
                        'label' => lang('index_heading'),
                        'icon'  => 'user',
                        'sub'   =>
                        array(
                            'users'       =>
                            array(
                                'label' => lang('index_heading'),
                                'desc'  => 'Users Description',
                                'seen'  => TRUE,
                            ),
                            'create-user' =>
                            array(
                                'label' => lang('create_user_heading'),
                                'desc'  => 'Create Users Description',
                                'seen'  => TRUE,
                            ),
                            'edit-group'  =>
                            array(
                                'label' => lang('edit_group_title'),
                                'desc'  => 'Edit Group Description',
                                'seen'  => FALSE,
                            ),
                            'deactivate'  =>
                            array(
                                'label' => lang('deactivate_heading'),
                                'desc'  => 'Deactivate User Description',
                                'seen'  => FALSE,
                            ),
                            'edit-user'   =>
                            array(
                                'label' => lang('edit_user_heading'),
                                'desc'  => 'Edit User Description',
                                'seen'  => FALSE,
                            ),
                        ),
                    ),
                    //sub menu
                    //---------STUDENT------------
                    'student_menu'   =>
                    array(
                        'label' => 'Students',
                        'icon'  => 'user-md',
                        'sub'   =>
                        array(
                            'students'       =>
                            array(
                                'label' => 'Student',
                                'desc'  => 'Student Description',
                                'seen'  => TRUE,
                            ),
                            'create-student' =>
                            array(
                                'label' => 'Add Student',
                                'desc'  => 'Add Student Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //---------END STUDENT--------
                    //sub menu
                    //---------START SUBJECTS-----
                    'subjects_menu'  =>
                    array(
                        'label' => 'Subjects',
                        'icon'  => 'book',
                        'sub'   =>
                        array(
                            'subjects'       =>
                            array(
                                'label' => 'Subject',
                                'desc'  => 'Subject Description',
                                'seen'  => TRUE,
                            ),
                            'create-subject' =>
                            array(
                                'label' => 'Add Subject',
                                'desc'  => 'Add Subject Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //---------END SUBJECTS-------
                    //sub menu
                    //--------START COURSE--------
                    'course_menu'    =>
                    array(
                        'label' => 'Courses',
                        'icon'  => 'list',
                        'sub'   =>
                        array(
                            'courses'       =>
                            array(
                                'label' => 'Course',
                                'desc'  => 'Course Description',
                                'seen'  => TRUE,
                            ),
                            'create-course' =>
                            array(
                                'label' => 'Create Course',
                                'desc'  => 'Create Course Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //--------END COURSE----------
                    //
                    //--------START EDUCATION--------
                    'education_menu' =>
                    array(
                        'label' => 'Educations',
                        'icon'  => 'pencil',
                        'sub'   =>
                        array(
                            'educations'       =>
                            array(
                                'label' => 'Education',
                                'desc'  => 'Education Description',
                                'seen'  => TRUE,
                            ),
                            'create-education' =>
                            array(
                                'label' => 'Create Education',
                                'desc'  => 'Create Education Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //--------END EDUCATION----------
                    //
                    //--------START ROOM--------
                    'room_menu'      =>
                    array(
                        'label' => 'Rooms',
                        'icon'  => 'lock',
                        'sub'   =>
                        array(
                            'rooms'       =>
                            array(
                                'label' => 'Room',
                                'desc'  => 'Room Description',
                                'seen'  => TRUE,
                            ),
                            'create-room' =>
                            array(
                                'label' => 'Create Room',
                                'desc'  => 'Create Room Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //--------END ROOM----------
                    //sub menu
                    //--------START SCHEDULE------
                    'schedule_menu'  =>
                    array(
                        'label' => 'Schedules',
                        'icon'  => 'calendar',
                        'sub'   =>
                        array(
                            'schedules'       =>
                            array(
                                'label' => 'Schedule',
                                'desc'  => 'Schedule Description',
                                'seen'  => TRUE,
                            ),
                            'create-schedule' =>
                            array(
                                'label' => 'Create Schedule',
                                'desc'  => 'Create Schedules Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //--------END SCHEDULE--------
                    //sub menu
                    'group_menu'     =>
                    array(
                        'label' => lang('index_groups_th'),
                        'icon'  => 'group',
                        'sub'   =>
                        array(
                            'groups'       =>
                            array(
                                'label' => lang('index_groups_th'),
                                'desc'  => lang('index_groups_th') . ' Description',
                                'seen'  => TRUE,
                            ),
                            'create-group' =>
                            array(
                                'label' => lang('create_group_heading'),
                                'desc'  => lang('create_group_heading') . ' Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                    //sub menu
                    'setting_menu'   =>
                    array(
                        'label' => 'Settings',
                        'icon'  => 'cogs',
                        'sub'   =>
                        array(
                            'language' =>
                            array(
                                'label' => lang('lang_label'),
                                'desc'  => 'Language Description',
                                'seen'  => TRUE,
                            ),
                            'database' =>
                            array(
                                'label' => 'Database',
                                'desc'  => 'Database Description',
                                'seen'  => TRUE,
                            ),
                            'log'      =>
                            array(
                                'label' => 'Error Logs',
                                'desc'  => 'Error Logsn Description',
                                'seen'  => TRUE,
                            ),
                        ),
                    ),
                );
        }

}


if (!function_exists('navigations_setting'))
{

        /**
         * 
         * @return type
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        function navigations_setting()
        {
                return array(
                    'language' =>
                    array(
                        'label' => lang('lang_label'),
                        'desc'  => 'Language Description',
                        'icon'  => 'file',
                    ),
                    'database' =>
                    array(
                        'label' => 'Database',
                        'desc'  => 'Database Description',
                        'icon'  => 'file',
                    ),
                    'log'      =>
                    array(
                        'label' => 'Error Logs',
                        'desc'  => 'Error Logsn Description',
                        'icon'  => 'exclamation-sign',
                    ),
                );
        }

}