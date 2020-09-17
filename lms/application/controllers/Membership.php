<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Membership extends MS_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('membership_model');
        $this->load->model('admin_model');
    }

    public function index($message = '')
    {

        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }

        if ($this->session->userdata('user_role_id') > 3){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $data = array();
        $data['class'] = 81; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['memberships'] = $this->membership_model->get_all_memberships();
        $data['features'] = $this->membership_model->get_features();
        $data['content'] = $this->load->view('admin/view_memberships', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function subscribe($id = null)
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

        $subscribtion_info = $this->db->where('price_table_id', $id)->get('price_table')->row();

        $user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();

        $data['header'] = $this->load->view('header/head', '', TRUE);
        $data['top_navi'] = $this->load->view('header/top_navigation', $data, TRUE);
        $data['subscribtion'] = $subscribtion_info;
        $data['content'] = $this->load->view('content/payment_detail', $data, TRUE);
        $data['footer'] = $this->load->view('footer/footer', $data, TRUE);
        $this->load->view('home', $data);
    }


    public function payment($id, $method = 'PayPal')
    {
        $subscribtion_info = $this->db->where('price_table_id', $id)->select('price_table_id,price_table_title,price_table_cost')->get('price_table')->row();

        $currency_code = $this->db->select('currency.currency_code')
                        ->from('paypal_settings')
                        ->join('currency', 'currency.currency_id = paypal_settings.currency_id')
                        ->get()->row()->currency_code;

        if ('PayPal' == $method)
        {
            $payment_settings = $this->admin_model->get_paypal_settings();
            $this->payment_express($payment_settings, $subscribtion_info, $currency_code);
        }
        elseif('PayUMoney' == $method)
        {
            $payment_settings = $this->db->where('id', 1)->get('payu_settings')->row();
            $this->payUMoney($payment_settings, $subscribtion_info, $currency_code);
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
            'amount' => $params->price_table_cost,
            'currency' => $currency_code,
            'description' => strip_tags($params->price_table_title),
            'return_url' => base_url('index.php/membership/payment_complete/'.$params->price_table_id),
            'cancel_url' => base_url('index.php/membership/payment_canceled/'.$params->price_table_id)
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
            redirect(base_url('index.php/membership/subscribe/' . $params->price_table_id));
        }
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
        $field['amount'] = $params->price_table_cost;
        $field['productinfo'] = '[' . json_encode($params) . ']';
        $field['surl'] = base_url().'index.php/exam_control/payment_complete/' . $params->price_table_id;
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

    public function payment_complete($id)
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/login_control'));
        }
        $this->load->model('exam_model');
        $this->load->model('admin_model');
        $membership = $this->membership_model->get_offer_by_id($id);
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
            'amount' => $membership->price_table_cost,
            'currency' => $currency['currency_code'],
            'cancel_url' => base_url('index.php/membership')
        );

        $this->load->library('merchant');
        $this->merchant->load('paypal_express');
        $this->merchant->initialize($settings);
        $response = $this->merchant->purchase_return($params);

        if ($response->success()) {
            $duration = '+ '. $membership->offer_duration.' '. $membership->offer_type.'';

            $subscription_start = date("Y-m-d");
            $subscription_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($subscription_start)) . $duration));

            $subscription_info = array();
            $subscription_info['subscription_id'] = $id;
            $subscription_info['subscription_start'] = strtotime($subscription_start);
            $subscription_info['subscription_end'] = strtotime($subscription_end);

            $this->admin_model->set_subscription($subscription_info);

            $message = '<div class="alert alert-sucsess alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'You subscribed successfully!</div>';
            $this->session->set_flashdata('message', $message);
            $data = array();
            $data['PayerID'] = $this->input->get('PayerID');
            $data['token'] = $this->input->get('token');
            $data['exam_title'] = $membership->price_table_title;
            $data['pay_amount'] = $membership->price_table_cost;
            $data['currency_code'] = $currency_code . ' ' . $currency_symbol;
            $data['method'] = 'PayPal';
            $data['gateway_reference'] = $response->reference();
            $token_id = $this->exam_model->set_payment_detail($data);

            $this->session->set_userdata('payment_token', $data['token']);
            $this->session->set_userdata('pay_id', $token_id);

            redirect(base_url() . 'login_control/dashboard_control/' . $this->session->userdata('user_id'));
        } else {
            $message = $response->message();
            echo('Error processing payment: ' . $message);
        }
    }

    public function payment_canceled($id = null)
    {
        $message = '<div class="alert alert-danger alert-dismissable">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                . 'You canceled the Payment.'
                . '</div>';
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/guest/pricing'));
    }

    public function add($message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 3){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $data = array();
        $data['class'] = 82; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['content'] = $this->load->view('form/create_offer_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function save()
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 3){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('membership_type', 'Membership Type', 'required');;
        $this->form_validation->set_rules('feature[0]', 'feature 1', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/membership/add_feature'));
        } else {
            if ($this->membership_model->save_offer()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Offer Added Successfully!.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/membership'));
            } else {
                $message = '<div class="alert alert-danger alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Somthing is wrong!';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/membership/add'));
            }
        }
    }

    public function edit($id = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 3){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        if (($id == '') OR (!is_numeric($id))) show_404();
        $data = array();
        $data['class'] = 81; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['offer'] = $this->membership_model->get_offer_by_id($id);
        $data['features'] = $this->membership_model->get_features_by_parent_id($id);
        $data['content'] = $this->load->view('form/edit_offer_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function update()
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 3){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('membership_id', 'Membership Type', 'required');;
        if ($this->form_validation->run() == FALSE) {
            $this->edit($this->input->post('membership_id'));
        } else {
            if ($this->membership_model->update_offer()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Offer updated successfully!.'
                        . '</div>';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/membership'));
            } else {
                $message = '<div class="alert alert-danger alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Somthing is wrong!';
                $this->session->set_flashdata('message', $message);
                redirect(base_url('index.php/membership/edit/'.$this->input->post('membership_id')));
            }
        }
    }

    public function delete($id = '')
    {
        if (($id == '') OR (!is_numeric($id))) show_404();

        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }

        if ($this->session->userdata('user_role_id') > 2)
        {
                $message = '<div class="alert alert-danger alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'You are not Authorised person to do this!'
                        . '</div>';
        }else{

            if ($this->membership_model->delete_offer($id)) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Offer deleted successfully!.'
                        . '</div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Somthing is wrong!'
                        . '</div>';
            }
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/membership'));
    }

    public function set_top_offer(){
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 2){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        if ($this->membership_model->set_top_offer()) {
            $message = '<div class="alert alert-success alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Updated!.'
                    . '</div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissable">'
                    . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                    . 'Somthing is wrong!';
        }
        $this->session->set_flashdata('message', $message);
        redirect(base_url('index.php/membership'));
    }

    public function add_feature($message = '')
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 2){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $data = array();
        $data['class'] = 83; // class control value left digit for main manu rigt digit for submenu
        $data['header'] = $this->load->view('header/admin_head', $data, TRUE);
        $data['top_navi'] = $this->load->view('header/admin_top_navigation', $data, TRUE);
        $data['sidebar'] = $this->load->view('sidebar/admin_sidebar', $data, TRUE);
        $data['message'] = $message;
        $data['memberships'] = $this->membership_model->get_all_memberships();
        $data['content'] = $this->load->view('form/add_feature_form', $data, TRUE);
        $data['footer'] = $this->load->view('footer/admin_footer', $data, TRUE);
        $this->load->view('dashboard', $data);
    }

    public function save_features()
    {
        if (!$this->session->userdata('log')) {
            $this->session->set_userdata('back_url', current_url());
            redirect(base_url('index.php/admin'));
        }
        if ($this->session->userdata('user_role_id') > 2){
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>You are not allowed to view this page.</div>';
            $this->session->set_flashdata('message', $message);
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('membership_id', 'Membership Type', 'callback_membership_id_check');;
        $this->form_validation->set_rules('feature[0]', 'feature 1', 'required');
        if ($this->form_validation->run() == FALSE) {
            redirect(base_url('index.php/membership/add_feature'));
        } else {
            if ($this->membership_model->save_features()) {
                $message = '<div class="alert alert-success alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Feature Added Successfully!.'
                        . '</div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissable">'
                        . '<button type="button" class="close" data-dismiss="alert" aria-hidden="TRUE">&times;</button>'
                        . 'Somthing is wrong!';
            }
            $this->session->set_flashdata('message', $message);
            redirect(base_url('index.php/membership'));
        }
    }

    public function membership_id_check($val)
    {
        //Callback Function for form validation
        if ($val == 0) {
            $this->form_validation->set_message('membership_id_check', 'Select membership type.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}