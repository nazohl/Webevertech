<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Login_control extends MS_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->config->load('mail');
        $this->load->model('admin_model');
        $this->load->model('exam_model');
        $this->load->model('login_model');
    }

    public function index($message = '')
    {
        if ($_POST) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_email', 'Email Address', 'required|valid_email');
            $this->form_validation->set_rules('user_pass', 'Password', 'required|min_length[6]');
            if ($this->form_validation->run() != FALSE) {
                // Check athentication
                if ($this->login_model->login_check()) {
                    $this->load->model('system_model');
                    $this->system_model->set_system_info_to_session();
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'You logged in successfully!'
                            . '</div>';

                    $this->session->set_flashdata('message', $message);

                    if ($this->session->userdata('back_url'))
                    {
                        redirect($this->session->userdata('back_url'));
                    }

                    redirect('dashboard/'.$this->session->userdata('user_id'));
                } else {
                    $message = '<div class="alert alert-danger">User Email/Password is not correct!</div>';
                    $this->session->set_flashdata('message', $message);
                }
            }
        }

        if ($this->uri->segment('1') == 'dashboard' )
        {
            redirect('admin');
        }

        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['content'] = $this->load->view('form/login_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', '', TRUE);
        $this->load->view('home', $data);
    }

    public function admin_login($message = '')
    {
        if ($_POST) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_email', 'Email Address', 'required|valid_email');
            $this->form_validation->set_rules('user_pass', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('user_role', 'User Type', 'callback_user_role_check');
            if ($this->form_validation->run() != FALSE) {
                $info_user = $this->input->post('user_email');
                $info_pass = md5($this->input->post('user_pass'));
                $info_role = $this->input->post('user_role');

                // Check athentication
                if ($this->login_model->login_check($info_user, $info_pass, $info_role)) {
                    $this->load->model('system_model');
                    $this->system_model->set_system_info_to_session();
                    redirect('login_control/dashboard_control');
                } else {
                    $message = '<div class="alert alert-danger">User Email/Password is not correct!</div>';
                }
            }
        }
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
        $data['message'] = $message;
        $data['content'] = $this->load->view('admin/admin_login_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', '', TRUE);
        $this->load->view('home', $data);
    }

    public function dashboard_control($user_id = 0, $role_id = 0, $message = '')
    {
        // echo "<pre>"; print_r("HEMELE"); echo "</pre>"; exit();
        if ($user_id == 0) {
            $user_id = $this->session->userdata('user_id');
        }
        if ($user_id != $this->session->userdata('user_id')) {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Please loged in to view this page.'
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/login_control'));
        }
        if ($role_id == 0) {
            $role_id = $this->session->userdata('user_role_id');
        }
        switch ($role_id) {
            case 1: //SUPER ADMIN
                $this->super_admin_dashboard($this->session->userdata('user_id'), $message);
                break;
            case 2: //admin
                $this->admin_dashboard($this->session->userdata('user_id'), $message);
                break;
            case 3:  //Moderator
                $this->moderator_dashboard($this->session->userdata('user_id'), $message);
                break;
            case 4: //Teacher
                $this->teacher_dashboard($this->session->userdata('user_id'), $message);
                break;
            case 5:  //Student
                $this->student_dashboard($this->session->userdata('user_id'), $message);
                break;
            case 0:// no break
            default:
                break;
        }
    }

    public function super_admin_dashboard($id = '0', $message = '')
    {
        if ($id == 0) {
            $this->index();
        }
        $data = array();
        $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['extra_head'] = $this->load->view('plugin_scripts/graph_n_chart', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['content'] = $this->load->view('admin/dashboard', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    // public function super_admin_dashboard($id = '0', $message = '')
    // {
    //     if ($id == 0) {
    //         $this->index();
    //     }
    //     $data = array();
    //     $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
    //     $data['header'] = $this->load->view('header/admin_head', '', TRUE);
    //     $data['extra_head'] = $this->load->view('plugin_scripts/graph_n_chart', '', TRUE);
    //     $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
    //     $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
    //     $data['message'] = $message;
    //     // $data['unread_messages'] = $this->login_model->get_unread_messages();
    //     // $data['new_exams'] = $this->login_model->get_new_exams();
    //     // $data['exam_taken'] = $this->login_model->new_exams_taken();
    //     // $data['new_user'] = $this->login_model->get_new_users();

    //     $data['total_admin'] = $this->login_model->get_total_admin();
    //     $data['total_moderator'] = $this->login_model->get_total_moderator();
    //     $data['total_teacher'] = $this->login_model->get_total_teacher();
    //     $data['total_student'] = $this->login_model->get_total_studnet();

    //     $data['content'] = $this->load->view('admin/dashboard', $data, TRUE);
    //     $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
    //     $this->load->view('dashboard', $data);
    // }

    public function admin_dashboard($id = '0', $message = '')
    {
        if ($id == 0) {
            $this->index();
        }
        $data = array();
        $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['extra_head'] = $this->load->view('plugin_scripts/graph_n_chart', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        // $data['unread_messages'] = $this->login_model->get_unread_messages();
        // $data['new_exams'] = $this->login_model->get_new_exams();
        // $data['exam_taken'] = $this->login_model->new_exams_taken();
        // $data['new_user'] = $this->login_model->get_new_users();

        $data['total_admin'] = $this->login_model->get_total_admin();
        $data['total_moderator'] = $this->login_model->get_total_moderator();
        $data['total_teacher'] = $this->login_model->get_total_teacher();
        $data['total_student'] = $this->login_model->get_total_studnet();

        $data['content'] = $this->load->view('admin/dashboard', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function moderator_dashboard($id = '0', $message = '')
    {
        if ($id == 0) {
            $this->index();
        }
        $data = array();
        $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['extra_head'] = $this->load->view('plugin_scripts/graph_n_chart', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        // $data['unread_messages'] = $this->login_model->get_unread_messages();
        // $data['new_exams'] = $this->login_model->get_new_exams();
        // $data['exam_taken'] = $this->login_model->new_exams_taken();
        // $data['new_user'] = $this->login_model->get_new_users();

        $data['total_admin'] = $this->login_model->get_total_admin();
        $data['total_moderator'] = $this->login_model->get_total_moderator();
        $data['total_teacher'] = $this->login_model->get_total_teacher();
        $data['total_student'] = $this->login_model->get_total_studnet();

        $data['content'] = $this->load->view('admin/dashboard', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function teacher_dashboard($id = '0', $message = '')
    {
        if ($id == 0) {
            $this->index();
        }
        $data = array();
        $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['title'] = 'Tracher Dashboard';
        $data['total_exam'] = $this->login_model->get_total_exam_by_user_id($this->session->userdata['user_id']);
        $data['exam_taken_new'] = $this->login_model->new_exams_taken();
        $data['exam_taken'] = $this->login_model->exams_taken();
        $data['message'] = $message;
        $data['content'] = $this->load->view('teacher_dashboard', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function student_dashboard($id = '0', $message = '')
    {
     //   echo "<pre/>"; print_r('done'); exit();
        if ($id == 0) {
            $this->index();
        }
        $data = array();
        $data['class'] = 00; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['title'] = 'Student Dashboard';
        $data['message'] = $message;
        $data['results'] = $this->exam_model->get_my_results($id);
        $data['content'] = $this->load->view('student_dashboard', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', '', TRUE);
        $this->load->view('dashboard', $data);
    }

    public function register($message = '')
    {
        if ($_POST) {
            if ($this->input->post('token') == $this->session->userdata('token')) {
                exit('Can\'t re-submit the form');
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_name', 'Name', 'required');
            $this->form_validation->set_rules('user_email', 'Email Address', 'required|valid_email');
            $this->form_validation->set_rules('user_pass', 'Password', 'required|min_length[6]|matches[user_passcf]');
            $this->form_validation->set_rules('user_passcf', 'Confirm Password', 'required|min_length[6]');
            if ($this->form_validation->run() != FALSE) {
                date_default_timezone_set($this->session->userdata['time_zone']);
                $info = array();
                $info['user_name'] = $this->input->post('user_name');
                $info['user_email'] = $this->input->post('user_email');
                $info['user_phone'] = $this->input->post('user_phone');
                $info['user_role_id'] = ($this->input->post('user_role'))?$this->input->post('user_role'):5;
                $info['user_pass'] = md5($this->input->post('user_pass'));
                $info['user_from'] = date('Y-m-d H:i:s');

                // Check athentication
                if ($this->login_model->register($info)) {
                    $mysecret = 'galua.mugda';
                    $key = sha1($mysecret . $info['user_email'] . $this->session->userdata['brand_name']);

                    $from = $this->session->userdata['support_email'];
                    $to = $info['user_email'];
                    $suject = 'Thank you for register with ' . $this->session->userdata['brand_name'];
                    $message_body = 'Click the link below to activate your account.<br/> '
                            . anchor(base_url('index.php/login_control/activate/') . '?user=' . $info['user_email'] . '&key=' . $key, 'Activation Link');

                    $this->load->library('email', $this->config->item('email_config'));
                    $this->email->set_newline("\r\n");
                    $this->email->from($from);
                    $this->email->to($to);
                    $this->email->subject($suject);
                    $this->email->message($message_body);
                    $this->email->send();
                    $this->session->set_userdata('token', $this->input->post('token'));
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'You Registered Successfully! Check your inbox for activation link.'
                            . '</div>';
                    if (count($_POST) > 0) {
                        $_POST = array();
                    }
                    $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));

                } else {
                    $message= '<div class="alert alert-danger alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . '"' . $info['user_email'] . '" is already used by another account. Try another email.</div>';
                }
            }
        }

        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['message'] = $message;
        $data['content'] = $this->load->view('form/register_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', '', TRUE);
        $this->load->view('home', $data);
    }

    public function register_teacher()
    {
        if ($_POST) {
            if ($this->input->post('token') == $this->session->userdata('token')) {
                exit('Can\'t re-submit the form');
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user_name', 'Name', 'required');
            $this->form_validation->set_rules('user_email', 'Email Address', 'required|valid_email');
            $this->form_validation->set_rules('paypal_id', 'PayPal email Address', 'required|valid_email');
            $this->form_validation->set_rules('user_pass', 'Password', 'required|min_length[6]|matches[user_passcf]');
            $this->form_validation->set_rules('user_passcf', 'Confirm Password', 'required|min_length[6]');
            if ($this->form_validation->run() != FALSE) {
                date_default_timezone_set($this->session->userdata['time_zone']);
                $info = array();
                $info['user_name'] = $this->input->post('user_name');
                $info['user_email'] = $this->input->post('user_email');
                $info['paypal_id'] = $this->input->post('paypal_id');
                $info['user_phone'] = $this->input->post('user_phone');
                $info['user_role_id'] = 4;
                $info['user_pass'] = md5($this->input->post('user_pass'));
                $info['user_from'] = date('Y-m-d H:i:s');

                // Check athentication
                if ($this->login_model->register_teacher($info)) {
                    $mysecret = 'galua.mugda';
                    $key = sha1($mysecret . $info['user_email'] . $this->session->userdata['brand_name']);

                    $from = $this->session->userdata['support_email'];
                    $to = $info['user_email'];
                    $suject = 'Thank you for register with ' . $this->session->userdata['brand_name'];
                    $message_body = 'Click the link below to activate your account.<br/> '
                            . anchor(base_url('index.php/login_control/activate/') . '?user=' . $info['user_email'] . '&key=' . $key, 'Activation Link');

                    $this->load->library('email', $this->config->item('email_config'));
                    $this->email->set_newline("\r\n");
                    $this->email->from($from);
                    $this->email->to($to);
                    $this->email->subject($suject);
                    $this->email->message($message_body);
                    $this->email->send();
                    $this->session->set_userdata('token', $this->input->post('token'));
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'You Registered Successfully! Check your inbox for activation link.'
                            . '</div>';
                    if (count($_POST) > 0) {
                        $_POST = array();
                    }
                    $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));

                } else {
                    $message= '<div class="alert alert-danger alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . '"' . $info['user_email'] . '" is already used by another account. Try another email.</div>';
                }
            }
        }

        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['content'] = $this->load->view('form/register_teacher_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', '', TRUE);
        $this->load->view('home', $data);
    }

    public function activate()
    {
        $mysecret = 'galua.mugda';
        if (sha1($mysecret . $this->input->get('user') . $this->session->userdata['brand_name']) == $this->input->get('key')) {
            if ($this->login_model->activate_my_account($this->input->get('user'))) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Activation successfull! Please login.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));
            } else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));
            }
        } else {
            exit('Invalid key');
        }
    }

    public function password_recovery_form($message = '')
    {
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['message'] = $message;
        $data['content'] = $this->load->view('form/forgot_password', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', '', TRUE);
        $this->load->view('home', $data);
    }

    public function forgot_password()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $this->password_recovery_form();
        } else {
            $email = $this->input->post('email');
            $this->load->model('user_model');
            if ($this->user_model->check_user_exist($email)) {
                $mysecret = 'galua.mugda';
                $key = sha1($mysecret . $email);
                $from = $this->session->userdata['support_email'];
                $to = $email;
                $suject = 'Password reset request ';
                $message_body = 'Click the link below to reset your password .<br/> '
                        . anchor(base_url('index.php/login_control/revovery/') . '?user=' . $email . '&key=' . $key, 'Password reset link');

                $this->load->library('email', $this->config->item('email_config'));
                $this->email->set_newline("\r\n");
                $this->email->from($from);
                $this->email->to($to);
                $this->email->subject($suject);
                $this->email->message($message_body);
                if ($this->email->send()) {
                    $_POST = array();
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'Password reset link sent to your email address! Check your inbox.'
                            . '</div>';
                    $this->session->set_flashdata('message', $message);
                    redirect(base_url('index.php/login_control'));
                } else {
                    $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                    $this->password_recovery_form($message);
                }
            } else {
                $message = '<div class="alert alert-danger">User not exist!</div>';
                $this->password_recovery_form($message);
            }
        }
    }

    public function revovery()
    {
        $mysecret = 'galua.mugda';
        $key = sha1($mysecret . $this->input->get('user'));
        if ($key == $this->input->get('key')) {
            $data = array();
            $data['header'] = $this->load->view('header/head', '', TRUE);
            $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
            $data['key'] = $key;
            $data['mail'] = $this->input->get('user');
            $data['content'] = $this->load->view('form/password_revovery', $data, TRUE);
            $data['footer'] = $this->load->view('footer/footer', '', TRUE);
            $this->load->view('home', $data);
        } else {
            exit('Invalid Key!!!');
        }
    }

    public function reset_password()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_pass', 'Password', 'required|min_length[6]|matches[user_passcf]');
        $this->form_validation->set_rules('user_passcf', 'Confirm Password', 'required|min_length[6]');
        if ($this->form_validation->run() == FALSE) {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
            $this->password_recovery_form($message);
        } else {
            $mysecret = 'galua.mugda';
            $key = sha1($mysecret . $this->input->post('mail'));
            if ($key == $this->input->post('key')) {
                $this->load->model('user_model');
                if ($this->user_model->reset_password($this->input->post('mail'))) {
                    $from = $this->session->userdata['support_email'];
                    $to = $this->input->post('mail');
                    $suject = $this->session->userdata['brand_name'] . ' password change confirmation!';
                    $message_body = 'You\'ve successfully changed your ' . $this->session->userdata['brand_name'] . ' password.'
                            . '.<br/> If you didn\'t do it. Deactivate your account'
                            . anchor(base_url('index.php/login_control/report_password_reset/') . '?user=' . $email . '&key=' . $key, ' from here ') . '. and Contact with administrator immediately';

                    $this->load->library('email', $this->config->item('email_config'));
                    $this->email->set_newline("\r\n");
                    $this->email->from($from);
                    $this->email->to($to);
                    $this->email->subject($suject);
                    $this->email->message($message_body);
                    $this->email->send();
                    $_POST = array();
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'Password reset successfully!.'
                            . '</div>';
                    $this->session->set_flashdata('message', $message);
                    redirect(base_url('index.php/login_control'));
                } else {
                    $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                    $this->password_recovery_form($message);
                }
            } else {
                exit('Invalid Key!!!');
            }
        }
    }

    public function report_password_reset()
    {
        $mysecret = 'galua.mugda';
        $key = sha1($mysecret . $this->input->get('user'));
        if ($key == $this->input->post('key')) {
            $this->load->model('user_model');
            if ($this->user_model->report_password_reset($this->input->get('user'))) {
                $message = '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Deactivated successfully! You account is no more accessible. Please contact with administrator.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));
            } else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/login_control'));
            }
        } else {
            exit('Invalid Key!!!');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function user_role_check($val)
    {
        //Callback Function for form validation
        if ($val == 0) {
            $this->form_validation->set_message('user_role_check', 'Select User Type.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
