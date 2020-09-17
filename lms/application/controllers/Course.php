<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Course extends MS_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('course_model');
        $this->load->model('exam_model');
        $this->load->model('admin_model');
        
    }

    public function index($message = '')
    {
        $data = array();
        $data['share'] = true;
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
        $data['categories'] = $this->exam_model->get_categories();
        $data['message'] = $message;
        if (!$this->session->userdata('log')) {
            $data['modal'] = $this->load->view('modals/login_n_register', $data, TRUE);
        }

        $data['courses'] = $this->course_model->get_all_courses();
        $data['content'] = $this->load->view('content/view_course_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        // echo "<pre/>"; print_r($data['courses']); exit();
        $this->load->view('home', $data);
    }
    
    public function update_user_course_status() {
        $userId = $this->session->userdata('user_id');
        $data = array();
        $data['user_id'] = $userId;
        $data['video_id'] = $this->input->post('video_id');
        $data['course_id'] = $this->input->post('course_id');
        $data['section_id'] = $this->input->post('section_id');
        echo $this->course_model->update_user_course_status($data);
        exit;
    }
    
    public function isLogin() {
        $userId = $this->session->userdata('user_id');
        if(empty($userId)) {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>Please login to view courses.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/login_control'));
        }
    }
    
    public function view_link($link,$video_id) {
       $this->isLogin(); 
       $key = "JumriTaliya";
       $token = $video_id."#".$key.'#'.session_id();
       #$token = $key;
       $this->load->library('encryption');
       $this->encryption->initialize(array(
                'driver' => 'mcrypt',
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => $token
       ));
       $encrypt_link = $this->encryption->decrypt(base64_decode($link));
       $file_pointer = fopen($encrypt_link, "rb");
       $data = fread($file_pointer, $this->remote_filesize($encrypt_link));
       header('Access-Control-Allow-Origin: *');  
       #header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
       #header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

       echo $data;
       exit;
    }
    
    function remote_filesize($url) {
        static $regex = '/^Content-Length: *+\K\d++$/im';
        if (!$fp = @fopen($url, 'rb')) {
            return false;
        }
        if (
            isset($http_response_header) &&
            preg_match($regex, implode("\n", $http_response_header), $matches)
        ) {
            return (int)$matches[0];
        }
        #header('Content-Length: ' . strlen(stream_get_contents($fp)));
        return strlen(stream_get_contents($fp));
   }


    public function course_summary($id, $message = '')
    {
        $this->isLogin();
        
        $this->load->library('encryption');
        $data = array();
        $data['share'] = true;
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['message'] = $message;
        $data['course'] = $this->course_model->get_course_by_id($id);
        $data['sections'] = $this->course_model->get_sections($id);
        $data['content'] = $this->load->view('content/view_course_summary', $data, TRUE);
        
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function view_all_courses($message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }

        $userId = $this->session->userdata('user_id');
        $data = array();
        $data['class'] = 91; // class control value left digit for main manu rigt digit for submenu
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', '', TRUE);
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['categories'] = $this->exam_model->get_subcategories();
        if ($this->session->userdata('user_role_id') < 4) {
            $data['courses'] = $this->course_model->get_all_courses();
            $data['content'] = $this->load->view('content/view_all_courses', $data, TRUE);
        } else {
            $data['courses'] = $this->course_model->get_user_courses($userId);
            $data['content'] = $this->load->view('content/view_user_courses', $data, TRUE);
        }
        $data['footer'] = $this->load->view('footer/admin_footer', '', TRUE);
        $this->load->view('dashboard', $data);
    }

    public function view_course_by_category($cat_id)
    {
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['courses'] = $this->course_model->get_courses_by_category($cat_id);
        $data['categories'] = $this->exam_model->get_categories();
        $data['category_name'] = $this->db->get_where('sub_categories', array('id' => $cat_id))->row()->sub_cat_name;
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
        $data['content'] = $this->load->view('content/view_course_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function courses_type($type)
    {
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['categories'] = $this->exam_model->get_categories();
      //    $data['mock_count'] = $this->exam_model->mock_count($data['categories']);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
            $data['courses'] = $this->course_model->get_courses_by_price($type);
        if($type === 'free'){
            $data['category_name'] = 'Free';
        }else if($type === 'paid'){
            $data['category_name'] = 'Paid';
        }else{
            redirect(base_url('index.php/course'));
        }
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $data['content'] = $this->load->view('content/view_course_list', $data, TRUE);
        $this->load->view('home', $data);

    }

    public function create_course($message = '', $cat_id = '')
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $userId = $this->session->userdata('user_id');
        $data = array();
        $data['class'] = 92; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['cat_id'] = $cat_id;
        $data['categories'] = $this->exam_model->get_categories();
        $data['content'] = $this->load->view('form/course_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }
    
    public function create_scorm($message = '', $cat_id = '') {
        
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $userId = $this->session->userdata('user_id');
        $data = array();
        $data['class'] = 93; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['cat_id'] = $cat_id;
        $data['categories'] = $this->exam_model->get_categories();
        $data['content'] = $this->load->view('form/scorm_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function save_scorm($message = '') {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('category', 'Sub Category', 'required|integer');
        if ($this->form_validation->run() == FALSE) {
            redirect(base_url('index.php/course/create_scorm'));
        } else {
            ############ Process ZIP FILES ONLY ############
            #https://stackoverflow.com/questions/11905355/php-read-all-text-files-in-a-zip-and-save-it-to-a-string-variable
            if ($_FILES['scorm_file']['name'] && $_FILES['scorm_file']['type']== "application/x-zip-compressed") {
                $uploads_dir = './uploads';
                $tmp_name = $_FILES["scorm_file"]["tmp_name"];
                $time = time();
                $file_name = $time.".zip";
                $file_upload = "$uploads_dir/$file_name";
                $success = 0;
                if(move_uploaded_file($tmp_name, $file_upload)) {
                    $zipfile = pathinfo(realpath($file_upload), PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.$file_name;
                    $zip = zip_open($zipfile);
                    $ziparc = new ZipArchive;
                    if ($zip) {
                        while ($zip_entry = zip_read($zip)) {
                          $file = zip_entry_name($zip_entry);
                          #echo "Name: " . $file . "<br />";
                          if (strpos($file,'module.xml') !== false) {
                            if ($ziparc->open($zipfile) === TRUE) {
                              //$coursexml =  new SimpleXMLElement($ziparc->getFromName($file));
                              $coursexml =  simplexml_load_string($ziparc->getFromName($file));
                              // ARRAY IS FOUND;;;;;
                              $info=array();
                              $jsonobj = json_encode($coursexml);
                              $sourcedata = json_decode($jsonobj,TRUE);
                              $info['course_title'] = $sourcedata['@attributes']['title'];
                              $info['category'] = $this->input->post('category', TRUE);
                              $course_id = $this->course_model->add_course_title_by_scorm($info);
                              $cnt = 1;
                              foreach($sourcedata['page'] as $val) {
                                $sections = array();
                                $sections['section_name'] = addslashes(htmlentities($val['@attributes']['displayText']));
                                $sections['section_title'] = addslashes(htmlentities($val['@attributes']['keywords']));
                                $sections['youtube_link'] = $val['@attributes']['link'];
                                $sections['course_id'] = $course_id;
                                $sections['order_id'] = $cnt;
                                $this->course_model->save_course_sections_by_scorm($sections);
                                $cnt++;
                              }
                              $ziparc->close();
                              $success = 1;
                            } else {
                              echo 'failed';
                            }
                          }
                        }
                    }
                }
                if($success) {
                    $message = "Scorm Uploaded Successfull !!";
                    $this->session->set_flashdata('message', $message);
                    redirect(base_url('index.php/course/edit_course_detail/'.$course_id));
                }
            } else {
                $message = "Please upload ZIP file format";
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/course/create_scorm'));
            }
            
            
        }
    }
    public function save_course($message = '')
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('category', 'Sub Category', 'required|integer');
        $this->form_validation->set_rules('course_title', 'Course Title', 'required');
        $this->form_validation->set_rules('course_intro', 'Course Introduction', 'required');
        $this->form_validation->set_rules('course_description', 'Course Description', 'required');
        $this->form_validation->set_rules('course_requirement', 'Course Requirements', 'required');
        $this->form_validation->set_rules('target_audience', 'Course Audience', 'required');
        $this->form_validation->set_rules('what_i_get', 'What I Get', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
            redirect(base_url('index.php/course/create_course'));
            // $this->create_course();
        } else {
            $title_id = $this->course_model->add_course_title();

            if ($_FILES['feature_image']['name']) {
                $uploads_dir = './course-images';
                $tmp_name = $_FILES["feature_image"]["tmp_name"];
                move_uploaded_file($tmp_name, "$uploads_dir/$title_id.png");
            }

            if ($title_id) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Course created successfully!.'
                        . '</div>';
                $course_title = $this->input->post('course_title');
                $this->ctreat_course_sections($title_id, $course_title, $message);
            } else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                $this->ctreat_course($message);
            }
        }
    }

    public function ctreat_course_sections($course_id = 0, $course_title = 'Create Sections', $message = '')
    {
            // echo "<pre/>"; print_r(func_get_args()); exit();
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $data = array();
        $data['class'] = 92; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['course_title'] = $course_title;
        $data['course_id'] = $course_id;
        $data['content'] = $this->load->view('form/section_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function add_section($course_id = '', $message = '')
    {//        exit($course_id);
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $data = array();
        $data['class'] = 91; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        // $data['sections'] = $this->course_model->get_sections($course_id);
        $data['course_id'] = $course_id;
        $data['course_title'] = $this->db->get_where('courses', array('course_id' => $course_id))->row()->course_title;
        $data['content'] = $this->load->view('form/section_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function save_sections()
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('section[0]', 'Section Title 1', 'required');
        $this->form_validation->set_rules('course_id', 'Course Id', 'required|integer');
        if ($this->form_validation->run() != FALSE)
        {
            if ($this->course_model->save_course_sections()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Section created successfully!.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/course/course_detail/'.$this->input->post('course_id')));
            }else{
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
            }
        }

        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/course/ctreat_course_sections/'.$this->input->post('course_id')));
    }
    public function add_exam($course_id = '', $section_id = '', $message = '') {
        
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4) {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        
        if($course_id == '' || !$course_id) {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>Select a course to add content.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/view_all_courses'));
        }
        
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('section', 'Select Section', 'required|integer');
            $this->form_validation->set_rules('exam', 'Select Exam', 'required');
            
            // $course_id = $this->input->post('course_id');
            // $section_id = $this->input->post('section');
            $exam_id = $this->input->post('exam');
            if ($this->form_validation->run() == FALSE) {
                $this->add_content($course_id, $section_id);
            } else {
                $is_inserted = $this->course_model->add_section_exam();
            }
            if($is_inserted) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Section Exam Mapped successfully!.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                if ($this->input->post('done')) {
                    redirect(base_url('index.php/course/course_detail/' . $course_id));
                } else {
                    redirect(base_url('index.php/course/add_exam/' . $course_id));
                }
            }
        }
        
        $data = array();
        $data['class'] = 92; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;

        $data['sections'] = $this->course_model->get_sections($course_id);
        $data['exams'] = $this->exam_model->get_course_exam($course_id);
        $data['section_id'] = $section_id;

        $data['course_id'] = $course_id;
        $data['course_title'] = $this->db->get_where('courses', array('course_id' => $course_id))->row()->course_title;
        $data['content'] = $this->load->view('form/add_course_section_exam', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
        
    }
    public function add_content($course_id = '', $section_id = '', $message = '')
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        if($course_id == '' || !$course_id)
        {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>Select a course to add content.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/view_all_courses'));
        }
        
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('section', 'Select Section', 'required|integer');
            $this->form_validation->set_rules('video_title', 'Video Title', 'required');

            $course_id = $this->input->post('course_id');
            $section_id = $this->input->post('section');

            if ($this->form_validation->run() == FALSE) {
                $this->add_content($course_id, $section_id);
            } else {
                
                $form_info = array();
                if (isset($_FILES['media']['name'])) {
                    $path_parts = pathinfo($_FILES["media"]["name"]);
                    $extension = $path_parts['extension'];

                    $directory = $course_id;
                    if (!is_dir('course_videos/'.$directory)) {
                        mkdir('./course_videos/' . $directory, 0777, TRUE);
                        $myFile = "./course_videos/".$directory."/index.html";
                        $fh = fopen($myFile, 'w');
                        $stringData = "<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>";
                        fwrite($fh, $stringData);
                    }
                    if($this->input->post('media_type') == 'file')
                    {
                        if(preg_match('/video\/*/',$_FILES['media']['type'])) {
                            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>ERROR! To upload video file please choose content type video.</div>';
                            $this->session->set_flashdata('message', $message);
                            redirect(base_url('index.php/course/add_content/'.$course_id.'/'.$section_id));
                        }
                        //$_SERVER['DOCUMENT_ROOT'].'/lmsefront
                        $dest = './course_videos/'.$directory.'/'.$_FILES["media"]['name'];
                        if(move_uploaded_file($_FILES["media"]['tmp_name'], $dest)){
                            $video_id = $this->course_model->add_course_video($_FILES["media"]["name"], $_FILES["media"]["size"]);
                        }
                        
                    }
                    else
                    {
                        $config['upload_path'] = './course_videos/'.$directory.'/';
                        $config['allowed_types'] = 'mp4|flv|avi|mpeg|ogg|webm';
                        $config['file_name'] = $section_id.'_'.$this->input->post('video_title').'.'.$extension;
                        $config['overwrite'] = TRUE;
                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload('media')) {
                            $error = array('error' => $this->upload->display_errors('<div class="alert alert-danger">', '</div>'));
                            $this->session->set_flashdata('message', $error['error']);
                            redirect(base_url('index.php/course/add_content/'.$course_id.'/'.$section_id));
                        } else {
                            $upload_data = $this->upload->data();
                            $video_id = $this->course_model->add_course_video($upload_data['file_name'], $_FILES["media"]["size"]);
                        }
                    }
                } /*else if($this->input->post('media_type') == 'exam') {
                        $is_inserted = $this->course_model->add_section_exam();
                } */else {
                        if($this->input->post('media_type') == 'live') {
                            $video_id = $this->course_model->add_course_video($this->input->post('datetimeinput') );
                        } else {
                            $video_id = $this->course_model->add_course_video();
                        }
                }

                if ($video_id) {
                    $message = '<div class="alert alert-success alert-dismissable">'
                            . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                            . 'Content uploaded successfully!.'
                            . '</div>';
                    $this->session->set_flashdata('message', $message);
                    if ($this->input->post('done')) {
                        redirect(base_url('index.php/course/course_detail/'.$course_id));
                    } else {
                        redirect(base_url('index.php/course/add_content/'.$course_id.'/'.$section_id));
                    }
                } else {
                    $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                    $this->session->set_flashdata('message', $message);
                    redirect(base_url('index.php/course/add_content/'.$course_id.'/'.$section_id));
                }
            }
        }

        $data = array();
        $data['class'] = 92; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;

        $data['sections'] = $this->course_model->get_sections($course_id);
        $data['exams'] = $this->exam_model->get_course_exam($course_id);
        $data['section_id'] = $section_id;
        

        $data['course_id'] = $course_id;
        $data['course_title'] = $this->db->get_where('courses', array('course_id' => $course_id))->row()->course_title;
        $data['content'] = $this->load->view('form/add_course_content', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        
        $this->load->view('dashboard', $data);

    }

    public function upload_course_videos($message = '')
    {
        // echo "<pre/>"; print_r($_FILES); echo "<pre/>"; print_r($_POST); exit();
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('section', 'Select Section', 'required|integer');
        $this->form_validation->set_rules('video_title', 'Video Title', 'required');
        // $this->form_validation->set_rules('free', 'free');
        if ($this->form_validation->run() == FALSE) {
            $this->add_content($this->input->post('course_id'));
        } else {
            $form_info = array();
            if ($_FILES['media']['name']) {
                $path_parts = pathinfo($_FILES["media"]["name"]);
                $extension = $path_parts['extension'];

                $directory = $this->input->post('course_id');
                if (!is_dir('course_videos/'.$directory)) {
                    mkdir('./course_videos/' . $directory, 0777, TRUE);
                }
                $config['upload_path'] = './course_videos/'.$directory.'/';
                $config['allowed_types'] = 'mp4|flv|avi|mpeg|ogg|webm';
                $config['file_name'] = $this->input->post('section').'_'.$this->input->post('video_title').'.'.$extension;
                $config['overwrite'] = TRUE;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('media')) {
                    $error = array('error' => $this->upload->display_errors('<div class="alert alert-danger">', '</div>'));
                    $this->session->set_flashdata('message',$error['error']);
                    redirect(base_url('index.php/course/add_content/'.$this->input->post('course_id')));
                } else {
                    $upload_data = $this->upload->data();
                    $video_id = $this->course_model->add_course_video($upload_data['file_name']);
                }
            }else{
                $video_id = $this->course_model->add_course_video();
            }

            if ($video_id) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Video added successfully!.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                if ($this->input->post('done')) {
                    redirect(base_url('index.php/course/course_detail/'.$this->input->post('course_id')));
                } else {
                    redirect(base_url('index.php/course/add_content/'.$this->input->post('course_id')));
                }
            } else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/course/add_content/'.$this->input->post('course_id')));
            }
        }
    }

    public function course_detail($id, $message = '')
    {
        if (!is_numeric($id)) {
            show_404();
        }
        $data = array();
        $data['class'] = 91;   // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['courses'] = $this->course_model->get_course_detail($id);
        $data['sections'] = $this->course_model->get_sections_exam($id);
        
        $data['content'] = $this->load->view('content/course_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $data['extra_footer'] = $this->load->view('plugin_scripts/drag-n-drop','', TRUE);
        $this->load->view('dashboard', $data);
    }

    public function section_detail($id, $message = '')
    {
        if (!is_numeric($id)) show_404();

        $data = array();
        $data['class'] = 91;   // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        //        $data['courses'] = $this->course_model->get_course_detail($id);
        $data['section'] = $this->course_model->get_section_detail($id);
        $data['videos'] = $this->course_model->get_section_videos($id, $data['section']->course_id);
        $data['content'] = $this->load->view('content/section_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $data['extra_footer'] = $this->load->view('plugin_scripts/drag-n-drop','', TRUE);
         //echo "<pre/>"; print_r($data['videos']); exit();
        $this->load->view('dashboard', $data);
    }


    public function edit_course_detail($id, $message = '')
    {
        if (!is_numeric($id))            show_404();

        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $data = array();
        $data['class'] = 91;   // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['courses'] = $this->course_model->get_course_detail($id);
        $data['content'] = $this->load->view('form/edit_course_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function update_course($id, $message = '')
    {
        if (!is_numeric($id))  show_404();
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('category', 'Category', 'required|integer');
        $this->form_validation->set_rules('course_title', 'Course Title', 'required');
        $this->form_validation->set_rules('course_intro', 'Course Introduction', 'required');
        $this->form_validation->set_rules('course_description', 'Course Description', 'required');
        $this->form_validation->set_rules('course_requirement', 'Course Requirements', 'required');
        $this->form_validation->set_rules('target_audience', 'Course Audience', 'required');
        $this->form_validation->set_rules('what_i_get', 'What I Get', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        if ($this->form_validation->run() == FALSE)
            redirect(base_url('index.php/course/edit_course_detail/'.$id));

        if ($_FILES['feature_image']['name'])
        {
            $uploads_dir = './course-images';
            $tmp_name = $_FILES["feature_image"]["tmp_name"];
            move_uploaded_file($tmp_name, "$uploads_dir/$id.png");
        }

        $this->course_model->update_course_title($id);

        $message = '<div class="alert alert-success alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'Course updated successfully!.'
                . '</div>';
            $this->session->set_flashdata('message',$message);
            redirect(base_url('index.php/course/view_all_courses'));
    }

    function delete_course($id)
    {
        if (!is_numeric($id)) show_404();
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $have_sections = $this->db->get_where('course_sections', array('course_id' => $id))->result();

        if (!empty($have_sections)) {
            //echo "<pre/>"; print_r($have_video); exit();
            $message = '<div class="alert alert-danger">This course has sections. Please delete all sections on the course and try again.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_detail/'.$have_sections[0]->course_id));
        }else{
            $course_id = $this->db->get_where('courses', array('course_id' => $id))->row()->course_id;
            $this->db->where('course_id', $id);
            $this->db->delete('courses');
            if ($this->db->affected_rows() == 1) {
                unlink('./course-images/'.$id.'.png');

                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Course deleted successfully!.'
                        . '</div>';
            }else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
            }
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/view_all_courses'));
        }
    }

    function delete_video($id)
    {
        if (!is_numeric($id)) show_404();

        $user_id = $this->session->userdata('user_id');
        $user_role_id = $this->session->userdata('user_role_id');
        $video = $this->db->where('video_id', $id)->get('course_videos')->row();

        $author = $this->db->where('course_id', $video->course_id)->get('courses')->row()->created_by;
        if ($author != $user_id && $user_role_id > 2) {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'You are not Authorised person to do this!'
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/section_detail/'.$video->section_id));
        }

        $this->db->where('video_id', $id)->delete('course_videos');

        if (unlink('course_videos/'.$video->course_id.'/'.$video->video_link)) {
            $message = '<div class="alert alert-success alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'The video has deleted successfully.'
                    . '</div>';
        } else {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/course/section_detail/'.$video->section_id));
    }

    public function save_order()
    {
         $order = $_POST['ID'];
         $k  = 1;

         $str = implode(",", $order);
        // echo "<pre/>"; print_r($order); exit();
        foreach ($order as $k => $val){
            $data['orderList'] = $k;
            $this->db->where('section_id', $val)->update('course_sections', $data);

        }
        $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Saved successfully!.'
                        . '</div>';
        echo $message;

    }

    public function save_order_vdo()
    {
         $order = $_POST['ID'];
         $k  = 1;

         $str = implode(",", $order);
        // echo "<pre/>"; print_r($order); exit();
        foreach ($order as $k => $val){
            $data['orderList'] = $k;
            $this->db->where('video_id', $val)->update('course_videos', $data);

        }
        $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Saved successfully!.'
                        . '</div>';
        echo $message;
    }

    function update_section()
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('section_id', 'Section Id', 'required|integer');
        $this->form_validation->set_rules('section_name', 'Section Name', 'required');
        $this->form_validation->set_rules('section_title', 'Section Title', 'required');
        if ($this->form_validation->run() != FALSE) {
            if ($this->course_model->update_section()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Section updated successfully!.'
                        . '</div>';
            }else{
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>Section updated failed! Please try again.</div>';
            }
            $this->session->set_flashdata('message', $message);
        }
        redirect(base_url('index.php/course/course_detail/'.$this->input->post('course_id')));
    }

    function update_video()
    {
        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        //        print_r($_POST);        exit();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('video_id', 'Video Id', 'required|integer');
        $this->form_validation->set_rules('video_title', 'Video Title', 'required');
        if ($this->form_validation->run() != FALSE) {
            if ($this->course_model->update_video()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Video updated successfully!.'
                        . '</div>';
            }else{
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
            }
            $this->session->set_flashdata('message', $message);
        }

        redirect(base_url('index.php/course/section_detail/'.$this->input->post('section_id')));
    }

    function delete_section($id)
    {
        if (!is_numeric($id))            show_404();

        if (!$this->session->userdata('log') || $this->session->userdata('user_role_id') > 4){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $have_video = $this->db->get_where('course_videos', array('section_id' => $id))->result();
        if (!empty($have_video)) {
            //echo "<pre/>"; print_r($have_video); exit();
            $message = '<div class="alert alert-danger">This section has videos. Please delete all videos on the section and try again.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_detail/'.$have_video[0]->course_id));
        }else{
            $course_id = $this->db->get_where('course_sections', array('section_id' => $id))->row()->course_id;
            $this->db->where('section_id', $id);
            $this->db->delete('course_sections');
            if ($this->db->affected_rows() == 1) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Section deleted successfully!'
                        . '</div>';
            }else {
                $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>An ERROR occurred! Please try again.</div>';
            }
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_detail/'.$course_id));
        }
    }

    public function enroll($id = null)
    {
        if (($id == '') OR !is_numeric($id))  show_404();

        if (!$this->session->userdata('log')){
            $this->session->set_userdata('back_url', current_url());
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Please login to view this page!</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/login_control'));
        }

        $course_info = $this->db->get_where('courses', array('course_id' => $id))->row();

        $user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();

        $purchased = $this->db->where('user_id', $this->session->userdata('user_id'))->where('pur_ref_id', $course_info->course_id)->get('puchase_history')->row();

        if(!$course_info->course_price || $purchased || ($user_info->subscription_id && $user_info->subscription_end > now()))
        {
            $message .= '<div class="alert alert-success alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'Sessions are unlocked now.'
                . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_summary/'.$id));
        }
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['course'] = $course_info;
        $data['content'] = $this->load->view('content/payment_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function payment($id, $method = 'PayPal')
    {
        $course_info = $this->db->where('course_id', $id)->select('course_id,course_title,course_price,created_by')->get('courses')->row();

        $currency_code = $this->db->select('currency.currency_code')
                        ->from('paypal_settings')
                        ->join('currency', 'currency.currency_id = paypal_settings.currency_id')
                        ->get()->row()->currency_code;

        if ('PayPal' == $method)
        {
            $payment_settings = $this->admin_model->get_paypal_settings();

            if($payment_settings->commission_percent == 100)
            {
                $this->payment_express($payment_settings, $course_info, $currency_code);
            }
            else
            {
                $this->payment_comission($payment_settings, $course_info, $currency_code);
            }
        }
        elseif('PayUMoney' == $method)
        {
            $payment_settings = $this->db->where('id', 1)->get('payu_settings')->row();
            $this->payUMoney($payment_settings, $course_info, $currency_code);
        }
        else
        {
            $message .= '<div class="alert alert-danger alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'The payment method is not valid.'
                . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/enroll/'.$id));
        }
    }

    private function payment_express($settings, $params, $currency_code)
    {
        $settings = array(
            'username' => $settings->api_username,
            'password' => $settings->api_pass,
            'signature' => $settings->api_signature,
            'test_mode' => ($settings->sandbox == 1)
        );

        $params = array(
            'amount' => $params->course_price,
            'currency' => $currency_code,
            'description' => strip_tags($params->title_name),
            'return_url' => base_url('index.php/course/payment_complete/'.$params->course_id),
            'cancel_url' => base_url('index.php/course/payment_canceled/'.$params->course_id)
        );

        $this->load->library('merchant');
        $this->merchant->load('paypal_express');
        $this->merchant->initialize($settings);
        $response = $this->merchant->purchase($params);

        if ($response->status() == Merchant_response::FAILED)
        {
            $err = $response->message();
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'PayPal Error: ' . $err
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_summary/' . $params->course_id));
        }
    }

    private function payment_comission($settings, $params, $currency_code)
    {
        $sandbox = ($settings->sandbox == 1);
        $config = array(
            'Sandbox' => $sandbox,
            'APIUsername' => $settings->api_username,
            'APIPassword' => $settings->api_pass,
            'APISignature' => $settings->api_signature,
            'ApplicationID' => $settings->application_id,
            'DeveloperEmailAccount' => 'bd.munna@hotmail.com'
        );

        $this->load->library('paypal/Paypal_adaptive', $config);

        // Prepare request arrays
        $PayRequestFields = array(
            'ActionType' => 'PAY',
            'CancelURL' => base_url('index.php/course/payment_canceled/'.$params->course_id),
            'CurrencyCode' => $currency_code,
            'FeesPayer' => 'EACHRECEIVER',
            'Memo' => strip_tags($params->course_title),
            'ReturnURL' => base_url('index.php/course/payment_done/'.$params->course_id),
            'ReverseAllParallelPaymentsOnError' => TRUE,
        );

        $ClientDetailsFields = array(
                'CustomerID' => 'Customer#'.$this->session->userdata('user_id'),
                'CustomerType' => 'Student',
            );

        // $FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');

        $Receivers = array();
        // $teacher_email = 'agbc_1296755893_biz@angelleye.com';
        $teacher_email = $this->db->where('user_id', $params->created_by)->get('users')->row()->paypal_id;

        $Receiver = array(
                'Amount' => $params->course_price,
                'Email' => $teacher_email,
                'InvoiceID' => '',
                'PaymentType' => 'SERVICE',
                'PaymentSubType' => '',
                'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''),
                'Primary' => TRUE
            );
        array_push($Receivers,$Receiver);
        $Receiver = array(
                'Amount' => $params->course_price * ($settings->commission_percent/100),
                'Email' => $settings->paypal_email,
                'InvoiceID' => '',
                'PaymentType' => 'SERVICE',
                'PaymentSubType' => '',
                'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''),
                'Primary' => FALSE
            );
        array_push($Receivers,$Receiver);

        // echo "<pre>"; print_r($params); echo "</pre>"; exit();
        $PayPalRequestData = array(
                    'PayRequestFields' => $PayRequestFields,
                    'ClientDetailsFields' => $ClientDetailsFields,
                    // 'FundingTypes' => $FundingTypes,
                    'Receivers' => $Receivers,
                );

        $PayPalResult = $this->paypal_adaptive->Pay($PayPalRequestData);

        if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
        {
            $errors = array_column($PayPalResult['Errors'], 'Message');

            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'PayPal Error: ' . $errors[0]
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_summary/' . $params->course_id));
        }
        $this->session->set_userdata('paykey', $PayPalResult['PayKey']);
        redirect($PayPalResult['RedirectURL']);
    }

    private function payUMoney($settings, $params, $currency_code)
    {
        // echo "<pre>"; print_r($params); echo "</pre>"; exit();
        if ($settings->sandbox == 1){
            $payu_url = "https://test.payu.in/_payment";
        }else{
            $payu_url = "https://secure.payu.in/_payment";
        }

        $MERCHANT_KEY = trim($settings->merchant_key); // Merchant key here as provided by Payu
        $SALT = trim($settings->salt); // Merchant Salt as provided by Payu

          // Generate random transaction id
        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

        $user = explode(' ', $this->session->userdata('user_name'));
        $firstname = $user[0];
        $lastname = end($user);

        $field = [];
        $field['key'] = $MERCHANT_KEY;
        $field['txnid'] = $txnid;
        $field['amount'] = $params->course_price;
        $field['productinfo'] = '[' . json_encode($params) . ']';
        $field['surl'] = base_url().'index.php/course/payment_complete/' . $params->course_id;
        $field['furl'] = base_url().'index.php/course/payment_failed';
        $field['curl'] = base_url().'index.php/course/payment_canceled';

        $field['firstname'] = $firstname;
        $field['lastname'] = $lastname; //optional
        $field['email'] = $this->session->userdata('user_email');
        $field['phone'] = $this->session->userdata('user_phone') ? : $this->session->userdata('support_phone');

        //$field['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));

        // Hash Sequence
        $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach($hashVarsSeq as $hash_var) {
          $hash_string .= isset($field[$hash_var]) ? $field[$hash_var] : '';
          $hash_string .= '|';
        }
        $hash_string .= $SALT;
        $hash = strtolower(hash('sha512', $hash_string));

        $field['hash'] = $hash;
        $field['service_provider'] = 'payu_paisa';

       // echo "<pre>"; print_r($field); echo "</pre>"; exit();

        //extract data from the post
        //set POST variables
        $field_string = '';
        //url-ify the data for the POST
        foreach($field as $key=>$value) { $field_string .= $key.'='.$value.'&'; }
        rtrim($field_string, '&');
        // echo "<pre>"; print_r($field_string); echo "</pre>"; exit();
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $payu_url);
        curl_setopt($ch,CURLOPT_POST, count($field));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        //        print_r($result);
          //      exit('<h2>Payment Error.</h2>');
    }

    public function payment_done($id)
    {
        $settings = $this->admin_model->get_paypal_settings();
        $config = array(
            'Sandbox' => ($settings->sandbox == 1),
            'APIUsername' => $settings->api_username,
            'APIPassword' => $settings->api_pass,
            'APISignature' => $settings->api_signature,
            'ApplicationID' => $settings->application_id,
            'DeveloperEmailAccount' => 'bd.munna@hotmail.com'
        );
        $this->load->library('paypal/Paypal_adaptive', $config);

        $PaymentDetailsFields = ['PayKey' => $this->session->userdata('paykey')];

        $PayPalRequestData = ['PaymentDetailsFields' => $PaymentDetailsFields];
        $PayPalResult = $this->paypal_adaptive->PaymentDetails($PayPalRequestData);

        if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
        {
            $errors = array_column($PayPalResult['Errors'], 'Message');

            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'PayPal Error: ' . $errors[0]
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/course/course_summary/' . $id));
        }

        $data = array();
        $data['PayerID'] = $PayPalResult['SenderEmail'];
        $data['token'] = $PayPalResult['PayKey'];
        $data['course_title'] = $PayPalResult['Memo'];
        $data['pay_amount'] = $PayPalResult['Receiver']['Amount'];
        // $data['pay_amount'] = $PayPalResult['Receiver']['Amount'] - ($PayPalResult['Receiver']['Amount'] * $settings->commission_percent/100);
        $data['currency_code'] = $PayPalResult['CurrencyCode'];
        $data['method'] = 'PayPal';
        $data['gateway_reference'] = $PayPalResult['PaymentInfo']['TransactionID'];
        $paymentRefId = $this->set_payment_detail($data);

        $data['paymentRefId'] = $paymentRefId;
        $data['pur_ref_id'] = $id;
        $this->set_purchase_detail($data);

        $message .= '<div class="alert alert-success alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'Sessions are unlocked now.'
                . '</div>';

        $this->session->set_flashdata('message', $message);

        redirect(base_url('index.php/course/course_summary/' . $id));
    }

    public function payment_canceled($id)
    {
        $message = '<div class="alert alert-danger alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'You canceled the Payment.'
                . '</div>';
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/course/course_summary/'.$id));
    }

    public function payment_complete($id)
    {
        $course_info = $this->db->get_where('courses', array('course_id' => $id))->row();
        $payment_settings = $this->admin_model->get_paypal_settings();
        $currency = $this->db->select('currency.currency_code,currency.currency_symbol')
                        ->from('paypal_settings')
                        ->join('currency', 'currency.currency_id = paypal_settings.currency_id')
                        ->get()->row_array();

        $settings = array(
            'username' => $payment_settings->api_username,
            'password' => $payment_settings->api_pass,
            'signature' => $payment_settings->api_signature,
            'test_mode' => ($payment_settings->sandbox == 1)
        );
        $params = array(
            'amount' => $course_info->course_price,
            'currency' => $currency['currency_code'],
            'cancel_url' => base_url('index.php/course/payment_canceled/'.$id)
        );

        $this->load->library('merchant');
        $this->merchant->load('paypal_express');
        $this->merchant->initialize($settings);
        $response = $this->merchant->purchase_return($params);

        if ($response->success()) {
            $message = '<div class="alert alert-sucsess alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Payment Successful!</div>';
            $this->session->set_flashdata('message', $message);
            $data = array();
            $data['PayerID'] = $this->input->get('PayerID');
            $data['token'] = $this->input->get('token');
            $data['course_title'] = $course_info->course_title;
            $data['pay_amount'] = $course_info->course_price;
            $data['currency_code'] = $currency_code . ' ' . $currency_symbol;
            $data['method'] = 'PayPal';
            $data['gateway_reference'] = $response->reference();
            $paymentRefId = $this->set_payment_detail($data);

            $data['paymentRefId'] = $paymentRefId;
            $data['pur_ref_id'] = $id;
            $this->set_purchase_detail($data);

            $message .= '<div class="alert alert-success alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Sessions are unlocked now.'
                    . '</div>';
        }
        else
        {
            $message = '<div class="alert alert-danger alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'PayPal Error: ' . $response->message()
                . '</div>';
        }
        $this->session->set_flashdata('message', $message);

        redirect(base_url('index.php/course/course_summary/' . $id));
    }

    public function set_payment_detail($info)
    {
        $data = array();
        $data['payer_id'] = $info['PayerID'];
        $data['token'] = $info['token'];
        $data['pay_amount'] = $info['pay_amount'];
        $data['payment_type'] = 'Course';
        $data['currency_code'] = $info['currency_code'];
        $data['user_id_ref'] = $this->session->userdata('user_id');
        $data['payment_reference'] = $info['course_title'];
        $data['pay_date'] = date('Y-m-d');
        $data['pay_method'] = $info['method'];
        $data['gateway_reference'] = $info['gateway_reference'];
        $this->db->insert('payment_history', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }

        return FALSE;
    }

    public function set_purchase_detail($info)
    {
        $data = array();
        $data['type'] = 'Course';
        $data['user_id'] = $this->session->userdata('user_id');
        $data['pur_ref_id'] = $info['pur_ref_id'];
        $data['pur_date'] = date('Y-m-d');

        $data['payment_id'] = $info['paymentRefId'];

        $this->db->insert('puchase_history', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

}

