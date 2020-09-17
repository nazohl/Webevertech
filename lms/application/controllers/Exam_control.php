<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Exam_control extends MS_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('exam_model');
        $this->load->model('admin_model');
    }

    public function index()
    {
        if ($this->input->post('token') == $this->session->userdata('token')) {
            exit('Can\'t re-submit the form');
        }
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }

        if ( count($this->input->post('ans')) < 1 ) {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'You didn\'t answer any question.</div>';
            $this->session->set_flashdata('message',$message);
            redirect(base_url('index.php/exam_control/view_all_mocks'));
        }

        $result_id = $this->exam_model->evaluate_result();

        if ($result_id)
        {
            $this->session->set_userdata('token', $this->input->post('token'));
            redirect(base_url('index.php/exam_control/view_result_detail/'.$result_id));
        } else {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'An ERROR occurred! Please contact to admin.</div>';
            $this->session->set_flashdata('message',$message);
            redirect(base_url('index.php/exam_control/view_all_mocks'));
        }
    }

    public function view_all_mocks($message = '')
    {
        $data = array();
        $data['share'] = true;
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['mocks'] = $this->exam_model->get_all_mocks();
        $data['categories'] = $this->exam_model->get_categories();
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
        $data['message'] = $message;
        $data['content'] = $this->load->view('content/view_mock_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function view_mocks_by_category($cat_id)
    {
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['mocks'] = $this->exam_model->get_mocks_by_category($cat_id);
        $data['categories'] = $this->exam_model->get_categories();
        $data['category_name'] = $this->db->get_where('sub_categories', array('id' => $cat_id))->row()->sub_cat_name;
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
        $data['content'] = $this->load->view('content/view_mock_list', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function mocks_type($type)
    {
        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['categories'] = $this->exam_model->get_categories();
      //    $data['mock_count'] = $this->exam_model->mock_count($data['categories']);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['user_role'] = $this->admin_model->get_user_role();
            $data['mocks'] = $this->exam_model->get_mocks_by_price($type);
        if($type === 'free'){
            $data['category_name'] = 'Free';
        }else if($type === 'paid'){
            $data['category_name'] = 'Paid';
        }else{
            redirect(base_url('index.php/exam_control/view_all_mocks'));
        }
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $data['content'] = $this->load->view('content/view_mock_list', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function view_exam_summary($id = '', $message = '')
    {
        if (!is_numeric($id)) show_404();

        $data = array();
        $data['share'] = true;
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['mock'] = $this->exam_model->get_mock_by_id($id);
        if (!$data['mock']) show_404();
        $data['message'] = $message;
        $data['content'] = $this->load->view('content/exam_summary', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function view_exam_instructions($id = '', $message = '')
    {
        if (!is_numeric($id))            show_404();

        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Please login to view this page!</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/login_control'));
        }

        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['message'] = $message;
        $data['mock'] = $this->exam_model->get_mock_by_id($id);
        if (!$data['mock']) show_404();
        if ($data['mock']->exam_price != 0)
        {
            $user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();

            if (($user_info->subscription_id == 0) OR ($user_info->subscription_end <= now()))
            {
                $payment_token = $this->exam_model->get_pay_token($id, $this->session->userdata('pay_id'));
// echo "<pre>"; print_r($payment_token); echo "</pre>"; exit();
                if (!$payment_token)
                {
                    redirect(base_url('index.php/exam_control/payment/' . $id), 'refresh');
                }
            }
        }

        $data['content'] = $this->load->view('content/exam_instructions', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function proceed($id = null)
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

        $exam_info = $this->exam_model->get_mock_by_id($id);

        $user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();

        $payment_token = $this->exam_model->get_pay_token($id, $this->session->userdata('pay_id'));

        if ($exam_info->exam_price == 0 || $user_info->subscription_end > now() || $payment_token)
        {
            redirect(base_url('index.php/exam_control/view_exam_instructions/'.$id), 'refresh');
        }

        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['exam'] = $exam_info;
        $data['content'] = $this->load->view('content/payment_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }

    public function payment($id, $method = 'PayPal')
    {
        $exam_info = $this->db->where('title_id', $id)->select('title_id,title_name,exam_price,user_id')->get('exam_title')->row();

        $currency_code = $this->db->select('currency.currency_code')
                        ->from('paypal_settings')
                        ->join('currency', 'currency.currency_id = paypal_settings.currency_id')
                        ->get()->row()->currency_code;

        if ('PayPal' == $method)
        {
            $payment_settings = $this->admin_model->get_paypal_settings();

            if($payment_settings->commission_percent == 100)
            {
                $this->payment_express($payment_settings, $exam_info, $currency_code);
            }
            else
            {
                $this->payment_comission($payment_settings, $exam_info, $currency_code);
            }
        }
        elseif('PayUMoney' == $method)
        {
            $payment_settings = $this->db->where('id', 1)->get('payu_settings')->row();
            $this->payUMoney($payment_settings, $exam_info, $currency_code);
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
            'amount' => $params->exam_price,
            'currency' => $currency_code,
            'description' => strip_tags($params->title_name),
            'return_url' => base_url('index.php/exam_control/payment_complete/'.$params->title_id),
            'cancel_url' => base_url('index.php/exam_control/payment_canceled/'.$params->title_id)
        );

        $this->load->library('merchant');
        $this->merchant->load('paypal_express');
        $this->merchant->initialize($settings);
        $response = $this->merchant->purchase($params);

        if ($response->status() == Merchant_response::FAILED) {
            $err = $response->message();
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'PayPal Error: ' . $err
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/exam_control/view_exam_summary/' . $params->course_id));
        }
        //  else {
        //     $data = array();
        //     $data['order_token'] = sha1(rand(0, 999999) . $id);
        //     $data['exam_id'] = $id;
        //     $set_token = $this->exam_model->set_order_token($data);
        // }
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
            'CancelURL' => base_url('index.php/exam_control/payment_canceled/'.$params->title_id),
            'CurrencyCode' => $currency_code,
            'FeesPayer' => 'EACHRECEIVER',
            'Memo' => strip_tags($params->title_name),
            'ReturnURL' => base_url('index.php/exam_control/payment_done/'.$params->title_id),
            'ReverseAllParallelPaymentsOnError' => TRUE,
        );
        $ClientDetailsFields = array(
                'CustomerID' => 'Customer#'.$this->session->userdata('user_id'),
                'CustomerType' => 'Student',
            );

        // $FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');

        $Receivers = array();
        // $teacher_email = 'agbc_1296755893_biz@angelleye.com';
        $teacher_email = $this->db->where('user_id', $params->user_id)->get('users')->row()->paypal_id;

        $Receiver = array(
                'Amount' => $params->exam_price,
                'Email' => $teacher_email,
                'InvoiceID' => '',
                'PaymentType' => 'SERVICE',
                'PaymentSubType' => '',
                'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''),
                'Primary' => TRUE
            );
        array_push($Receivers,$Receiver);
        $Receiver = array(
                'Amount' => $params->exam_price * ($settings->commission_percent/100),
                'Email' => $settings->paypal_email,
                'InvoiceID' => '',
                'PaymentType' => 'SERVICE',
                'PaymentSubType' => '',
                'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''),
                'Primary' => FALSE
            );
        array_push($Receivers,$Receiver);


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
            redirect(base_url('index.php/exam_control/view_exam_summary/' . $params->title_id));
        }
        $this->session->set_userdata('paykey', $PayPalResult['PayKey']);
        redirect($PayPalResult['RedirectURL']);
    }

    private function payUMoney($settings, $params, $currency_code)
    {
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
        $field['amount'] = $params->exam_price;
        $field['productinfo'] = '[' . json_encode($params) . ']';
        $field['surl'] = base_url().'index.php/exam_control/payment_complete/' . $params->title_id;
        $field['furl'] = base_url().'index.php/exam_control/payment_failed';
        $field['curl'] = base_url().'index.php/exam_control/payment_canceled';

        $field['firstname'] = $firstname;
        $field['lastname'] = $lastname; //optional
        $field['email'] = $this->session->userdata('user_email');
        $field['phone'] = $this->session->userdata('user_phone') ? : $this->session->userdata('support_phone');

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
    }

    public function payment_done($id)
    {
        $message = '';
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

            $message .= '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'PayPal Error: ' . $errors[0]
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/exam_control/view_exam_summary/' . $id));
        }

        // echo "<pre>"; print_r($PayPalResult); echo "</pre>"; exit();

        $data = array();
        $data['PayerID'] = $PayPalResult['SenderEmail'];
        $data['token'] = $PayPalResult['PayKey'];
        $data['title_name'] = $PayPalResult['Memo'];
        $data['pay_amount'] = $PayPalResult['Receiver']['Amount'];
        // $data['pay_amount'] = $PayPalResult['Receiver']['Amount'] - ($PayPalResult['Receiver']['Amount'] * $settings->commission_percent/100);
        $data['currency_code'] = $PayPalResult['CurrencyCode'];
        $data['method'] = 'PayPal';
        $data['gateway_reference'] = $PayPalResult['PaymentInfo']['TransactionID'];
        $payment_id = $this->set_payment_detail($data);

        $data['paymentRefId'] = $payment_id;
        $data['pur_ref_id'] = $id;
        $this->set_purchase_detail($data);

        $this->session->set_userdata('payment_token', $data['token']);
        $this->session->set_userdata('pay_id', $payment_id);

        $message .= '<div class="alert alert-success alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'Sessions are unlocked now.'
                . '</div>';

        $this->session->set_flashdata('message', $message);

        redirect(base_url('index.php/exam_control/view_exam_instructions/' . $id));
    }

    public function payment_complete($id)
    {
        $item_info = $this->exam_model->get_item_detail($id);
        $payment_settings = $this->admin_model->get_paypal_settings();
        $currency = $this->db->select('currency.currency_code,currency.currency_symbol')
                        ->from('paypal_settings')
                        ->join('currency', 'currency.currency_id = paypal_settings.currency_id')
                        ->get()->row_array();
        if ($payment_settings->sandbox == 1) {
            $mode = TRUE;
        }else{
            $mode = FALSE;
        }
        $settings = array(
            'username' => $payment_settings->api_username,
            'password' => $payment_settings->api_pass,
            'signature' => $payment_settings->api_signature,
            'test_mode' => $mode
        );
        $params = array(
            'amount' => $item_info->exam_price,
            'currency' => $currency['currency_code'],
            'cancel_url' => base_url('index.php/exam_control/view_all_mocks'));

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
            $data['exam_title'] = $item_info->title_name;
            $data['pay_amount'] = $item_info->exam_price;
            $data['currency_code'] = $currency_code . ' ' . $currency_symbol;
            $data['method'] = 'PayPal';
            $data['gateway_reference'] = $response->reference();
            $payment_id = $this->exam_model->set_payment_detail($data);

            $this->session->set_userdata('payment_token', $data['token']);
            $this->session->set_userdata('pay_id', $payment_id);

            redirect(base_url() . 'index.php/exam_control/view_exam_instructions/' . $id);
        } else {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . $response->message()
                    . '</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/exam_control/view_exam_summary/'.$id));
        }
    }

    public function payment_canceled($id)
    {
        $message = '<div class="alert alert-danger alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'You canceled the Payment.'
                . '</div>';
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/exam_control/view_exam_summary/'.$id));
    }

    public function start_exam($id = '', $message = '')
    {
        $this->load->helper('cookie');

        if (($id == '') OR !is_numeric($id)) show_404();

        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Please login to view this page!</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/login_control'));
        }

        $data = array();
        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['message'] = $message;
        $data['mock'] = $this->exam_model->get_mock_by_id($id);

        if (!$data['mock'])  show_404();

        if ($data['mock']->exam_price != 0)
        {
            $user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();

            if (($user_info->subscription_id == 0) OR ($user_info->subscription_end <= now()))
            {
                $payment_token = $this->exam_model->get_pay_token($id, $this->session->userdata('pay_id'));

                if (!$payment_token)
                {
                    redirect('index.php/exam_control/payment/' . $id, 'refresh');
                }
            }
        }

        if($this->input->cookie('ExamTimeDuration')){
            $data['duration'] = $this->input->cookie('ExamTimeDuration', TRUE)-1;
        } else {
            $data['duration'] = $data['mock']->duration;
        }

        $all_questions = $this->exam_model->get_mock_detail($id);
        $counter = count($all_questions);

        if ($data['mock']->random_ques_no != NULL && $data['mock']->random_ques_no > 0)
        {
            $questions = array();
            $i=0;
            do{
                $index = rand(0, $counter-1);
                if (array_key_exists($index, $questions)) {
                    continue;
                }
                $questions[$index] = $all_questions[$index];
                $i++ ;
            }while($i < $data['mock']->random_ques_no);

            $data['questions'] = $questions;

        }else{
            $data['questions'] = $all_questions;
        }

        $data['ques_count'] = $counter;
        $data['answers'] = $this->exam_model->get_mock_answers($data['questions']);
        $data['content'] = $this->load->view('content/start_exam', $data, TRUE);
        $data['no_contact_form'] = TRUE;
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
        $this->session->unset_userdata('pay_id');
        $this->session->unset_userdata('payment_token');
    }

    public function view_results($message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }
        $userId = $this->session->userdata('user_id');
        $data = array();
        $data['class'] = 25; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', '', TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        if ($this->session->userdata('user_role_id') <= 4) {
            $data['results'] = $this->exam_model->get_all_results();
            $data['content'] = $this->load->view('content/view_all_results', $data, TRUE);
        } else {
            $data['results'] = $this->exam_model->get_my_results($userId);
            $data['content'] = $this->load->view('content/view_my_results', $data, TRUE);
        }
        $data['footer'] = $this->load->view('footer/admin_footer', '', TRUE);
        $this->load->view('dashboard', $data);
    }

    public function view_exam_detail($id = '', $message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }
        if (!is_numeric($id))  show_404();
        $author = $this->exam_model->view_result_detail($id);
        if (empty($author)) {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Not available!</div>';
            $this->view_results($message);
        } else {
            if (($author->participant_id != $this->session->userdata('user_id')) && ($this->session->userdata('user_id') > 4)) {
                exit('<h2>You are not Authorised person to do this!</h2>');
            } else {
                $data = array();
                $data['class'] = 25; // class control value left digit for main manu rigt digit for submenu
                $data['header'] = $this->load->view('header/admin_head', '', TRUE);
                $data['top_navi'] = $this->load->view('header/admin_top_navigation', '', TRUE);
                $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
                $data['message'] = $message;
                $data['results'] = $author;
                $data['content'] = $this->load->view('content/exam_detail', $data, TRUE);
                $data['footer'] = $this->load->view('footer/admin_footer', '', TRUE);
                $this->load->view('dashboard', $data);
            }
        }
    }

    public function view_result_detail($id = '', $message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }
        if (!is_numeric($id)) {
            show_404();
        }
        $author = $this->exam_model->view_result_detail($id);
        if (empty($author)) {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Not available!</div>';
            $this->view_results($message);
        } else {
            if (($author->participant_id != $this->session->userdata('user_id')) && ($this->session->userdata('user_id') > 3)) {
                exit('<h2>You are not Authorised person to do this!</h2>');
            } else {
                $data = array();
                $data['class'] = 25; // class control value left digit for main manu rigt digit for submenu
                $data['header'] = $this->load->view('header/admin_head', '', TRUE);
                $data['top_navi'] = $this->load->view('header/admin_top_navigation', '', TRUE);
                $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
                $data['message'] = $message;
                $data['results'] = $author;
                $data['content'] = $this->load->view('content/result_detail', $data, TRUE);
                $data['footer'] = $this->load->view('footer/admin_footer', '', TRUE);
                $this->load->view('dashboard', $data);
            }
        }
    }

    public function delete_results($id = '')
    {
        if (!is_numeric($id)) {
            return FALSE;
        }
        $author = $this->exam_model->get_result_by_id($id);
        if (empty($author) OR (($author->user_id != $this->session->userdata('user_id')) && ($this->session->userdata('user_id') > 2)))
        {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        if ($this->exam_model->delete_result($id)) {
            $message = '<div class="alert alert-success alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Successfully Deleted!'
                    . '</div>';
            $this->view_results($message);
        } else {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'An ERROR occurred! Please try again.</div>';
            $this->view_results($message);
        }
    }


    public function set_payment_detail($info)
    {
        $data = array();
        $data['payer_id'] = $info['PayerID'];
        $data['token'] = $info['token'];
        $data['pay_amount'] = $info['pay_amount'];
        $data['payment_type'] = 'Exam';
        $data['currency_code'] = $info['currency_code'];
        $data['user_id_ref'] = $this->session->userdata('user_id');
        $data['payment_reference'] = $info['title_name'];
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
        $data['type'] = 'Exam';
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
