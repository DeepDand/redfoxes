<?php
class Discussion extends CI_Controller
{

    private $current_line;
    private $recent = true;

    public function __construct(){
        parent::__construct();
        if(!isset($_SESSION)) {
            session_start();
            $_SESSION['id'] = session_id();
        }
        $this->lang->load('en_admin_lang');
        $this->load->model('Discussion_model');
    }

    public function dbAuth(){
    //if the last session was over 15 minutes ago
        if (isset($_SESSION['LAST_SESSION']) && (time() - $_SESSION['LAST_SESSION'] > 900)) {
            if(!isset($_SESSION['CAS'])) {
                $_SESSION['CAS'] = false; // set the CAS session to false
            }
        }
        $authenticated = $_SESSION['CAS'];
        //$casurl = "http%3A%2F%2Flocalhost%2Fredv3%2F%3Fc%3DAuth%26m%3DdbAuth";
        $casurl = "http%3A%2F%2Fdev.library.marist.edu%2Fredv3deep%2F%3Fc%3DDiscussion%26m%3DcreateDiscussionView";
        //$casurl = urlencode(base_url()."?c=Discussion&m=dbAuth");
        //  $casurl = html_entity_encode($casurl);
        //send user to CAS login if not authenticated
        if (!$authenticated) {
            $_SESSION['LAST_SESSION'] = time(); // update last activity time stamp
            $_SESSION['CAS'] = true;
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas/?service='.$casurl.'">';
            //header("Location: https://cas.iu.edu/cas/login?cassvc=IU&casurl=$casurl");
            exit;
        }
		if ($authenticated) {
            //print_r($_GET["ticket"]);
            if (isset($_GET["ticket"])) {
                //set up validation URL to ask CAS if ticket is good
                $_url = "https://login.marist.edu/cas/validate";
              //  $serviceurl = "http://localhost:9090/repository-2.0/?c=repository&m=cas_admin";
               // $cassvc = 'IU'; //search kb.indiana.edu for "cas application code" to determine code to use here in place of "appCode"
                //$ticket = $_GET["ticket"];
                //$casurl = urlencode(base_url()."?c=Discussion&m=createDiscussion_view");
                $params = "ticket=".$_GET["ticket"]."&service=".$casurl;
                $urlNew = "$_url?$params";

                //CAS sending response on 2 lines. First line contains "yes" or "no". If "yes", second line contains username (otherwise, it is empty).
                $ch = curl_init();
                $timeout = 5; // set to zero for no timeout
                curl_setopt ($ch, CURLOPT_URL, $urlNew);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $cas_answer = ob_get_contents();
                ob_end_clean();
                //$_SESSION['cas_answer'] = $cas_answer;
                if(strlen($cas_answer)>3){
                //split CAS answer into access and user
                    list($access,$user) = preg_split("/\n/",$cas_answer,2);
                    $access = trim($access);
                    $user = trim($user);
                    //set user and session variable if CAS says YES
                    if ($access == "yes") {
                        $_SESSION['user'] = $user;
                        $user= str_replace('@marist.edu',"",$user);
                        $_SESSION['cwid'] = $user;
                        $data['cwid'] = $user;
                        $data['title'] = "Marist Disussion Forums";
                        $this->load->view('createDiscussion_vieww',$data);
                    }else{
                        echo "<h1>UnAuthorized Access</h1>";
                    }
                } else {
                    echo "<h1>UnAuthorized Access</h1>".$cas_answer;
                }
            }//END SESSION USER
            else{
                echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas?service='.$casurl.'">';
            }
        } else  {
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas?service='.$casurl.'">';
        }
    }


    public function index(){
        if(isset($_SESSION['cwid'])){
            $data['cwid'] = $_SESSION['cwid'];
            $data['title'] = "Marist Disussion Forums";
            // $cwid = $_SESSION['user'];
            //$data['username'] = 'Deep';
            $userquery = $this->Discussion_model->checkuniqueuser($data['cwid']);
            if($userquery){
                $data['username'] = $this->Discussion_model->getusername($data['cwid']);
            } else {
                $data['username'] = 'something went wrong with db access';
            }
        } else {
            $data['title'] = "Marist Disussion Forums";
        }    
        $this->load->view('createDiscussion_vieww',$data);
    }

    public function logout(){
        $this->session->sess_destroy();
        unset($data['cwid']);
        redirect('Discussion','refresh');
    }

    public function createDiscussionView(){
        $this->load->model('Discussion_model');
        if (isset($_SESSION['LAST_SESSION']) && (time() - $_SESSION['LAST_SESSION'] > 900)) {
            if(!isset($_SESSION['CAS'])) {
                $_SESSION['CAS'] = false; // set the CAS session to false
            }
        }
        $authenticated = $_SESSION['CAS'];
        $casurl = base_url() . "Discussion/createDiscussionView";
        $casurl = urlencode($casurl);
        //send user to CAS login if not authenticated
        if (!$authenticated) {
            $_SESSION['LAST_SESSION'] = time(); // update last activity time stamp
            $_SESSION['CAS'] = true;
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas/?service='.$casurl.'">';
            //header("Location: https://cas.iu.edu/cas/login?cassvc=IU&casurl=$casurl");
            exit;
        }
		if ($authenticated) {
            //print_r($_GET["ticket"]);
            if (isset($_GET["ticket"])) {
                //set up validation URL to ask CAS if ticket is good
                $_url = "https://login.marist.edu/cas/validate";
              //  $serviceurl = "http://localhost:9090/repository-2.0/?c=repository&m=cas_admin";
               // $cassvc = 'IU'; //search kb.indiana.edu for "cas application code" to determine code to use here in place of "appCode"
                //$ticket = $_GET["ticket"];
                //$casurl = urlencode(base_url()."?c=Discussion&m=createDiscussion_view");
                $params = "ticket=".$_GET["ticket"]."&service=".$casurl;
                $urlNew = "$_url?$params";

                //CAS sending response on 2 lines. First line contains "yes" or "no". If "yes", second line contains username (otherwise, it is empty).
                $ch = curl_init();
                $timeout = 5; // set to zero for no timeout
                curl_setopt ($ch, CURLOPT_URL, $urlNew);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $cas_answer = ob_get_contents();
                ob_end_clean();
                //$_SESSION['cas_answer'] = $cas_answer;
                if(strlen($cas_answer)>3){
                //split CAS answer into access and user
                    list($access,$user) = preg_split("/\n/",$cas_answer,2);
                    $access = trim($access);
                    $user = trim($user);
                    //set user and session variable if CAS says YES
                    if ($access == "yes") {
                        $_SESSION['user'] = $user;
                        $user= str_replace('@marist.edu',"",$user);
                        $_SESSION['cwid'] = $user;
                        $data['cwid'] = $user;
                        $data['title'] = "Marist Disussion Forums";
                        // $cwid = $_SESSION['user'];
                        //$data['username'] = 'Deep';
                        $userquery = $this->Discussion_model->checkuniqueuser($data['cwid']);
                        if($userquery){
                            $data['username'] = $this->Discussion_model->getusername($data['cwid']);
                        } else {
                            $data['username'] = 'something went wrong with db access';
                        }
                        
                        $this->load->view('createDiscussion_vieww',$data);
                    }else{
                        echo "<h1>UnAuthorized Access</h1>";
                    }
                } else {
                    echo "<h1>UnAuthorized Access</h1>".$cas_answer;
                }
            }//END SESSION USER
            else{
                echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas?service='.$casurl.'">';
            }
        } else  {
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=https://login.marist.edu/cas?service='.$casurl.'">';
        }
    }

    public function successView(){
        $this->load->view('success_view');
    }

    public function failView(){
        $this->load->view('fail_view');
    }

    /*public function createDiscussion_view(){
        $data['user'] = $_SESSION['user'];
        $cwid = $_SESSION['user'];
        $userquery = $this->Discussion_model->checkuniqueuser($cwid);
        if($userquery){
            $data['username'] = $this->Discussion_model->getusername($cwid);
        } else {
            $data['username'] = '';
        }
        $data['title'] = "Marist Disussion Forums";
        $this->load->view('createDiscussion_vieww',$data);
    }*/

    public function discussionList(){
        $page_data['query'] = $this->Discussion_model->discussion_list();
        $this->load->view('discussionList_view',$page_data);
    }

    public function create() {
        //$this->form_validation->set_rules('cwid', $this->lang->line('cwid'), 'required|min_length[8]|max_length[8]');
        $this->form_validation->set_rules('ds_title', $this->lang->line('discussion_ds_title'), 'required|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('ds_body', $this->lang->line('discussion_ds_body'), 'required|min_length[1]|max_length[500]');
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = "Marist Disussion Forums";
            $this->load->view('newDiscussion_view',$data);//add alert and bring user to same page to fill the form again.
        } else {
            $data = array(
                'cwid' => $_SESSION['user'],
                'ds_title' => $this->input->post('ds_title'),
                'ds_body' =>  $this->input->post('ds_body'),
                'category' => $this->input->post('category'),
                'ds_num' => $this->input->post('ds_num'),
                'firstname' => $_SESSION['firstname']
            );
            $dtitle = $this->input->post('ds_title');
            $dbody = $this->input->post('ds_body');
            $flag = $this->Discussion_model->create($data);
            $emailID = $this->Discussion_model->getEmailId($data);
            $d_id = $this->Discussion_model->getDId($data);
            if ($flag && isset($emailID) && isset($d_id)) {
                $checkmail = $this->email_user("deep",$emailID,$d_id,$data['ds_body']);
                if($checkmail){
                    return 1;
                }

                /*$did = $this->Discussion_model->find_discussion($dtitle,$dbody);
                $discussion_data['query'] = $this->Discussion_model->fetch_discussion($did);
                $this->load->view('discussionDetails_view',$discussion_data);*/
                //redirect(base_url()); //need to redirected to the list of discussions __******
            } else {
                // error
                // load view and flash sess error
                $this->load->view('errors/error_exception');
            }
        }
    }
    public function discussionDetails(){
        //details include the discussion body, posts on the discussion and the comments on these posts
        $did = $this->uri->segment(3);
        if(strlen($did) > 12){
            $did=substr($did,6,-6);
		}
        //var_dump($this->uri->segment(3));
        $pid = array();
        //fetch discussions from Discussion IDs
        if($did != '') {
            $discussion_data['query'] = $this->Discussion_model->fetch_discussion($did);
            $discussion_data['postquery'] = $this->Discussion_model->fetch_post($did);//,$config['per_page'],$page);
            $this->load->view('discussionDetails_view',$discussion_data);
        }
        else {
            $this->load->view('fail_view');
        }
    }
    public function addNewPost(){
        $data['title'] = "Marist Disussion Forums";
        // Submitted form data
        //$data['cwid']   = $_POST['cwid'];
        //$data['cwid']   = $_SESSION['user'];
        $data['cwid']   = "12345678";
        $data['p_title']   = $this->input->post('postTitle');
        $data['p_body']   = $this->input->post('postBody');
        $data['d_id']   = $this->input->post('d_id');
        //$data['firstname'] = $_SESSION['firstname'];
        $data['firstname'] = "Deep";
        $did = $this->input->post('d_id');
        if($data['p_title'] != NULL){
            if($this->Discussion_model->createPost($data)){
                $post_data['postquery'] = $this->Discussion_model->fetch_post($did);
                $post_data['query'] = $this->Discussion_model->fetch_discussion($did);
                $emailID = $this->Discussion_model->find_email($data['d_id']);
                //$d_id = $this->Discussion_model->getDId($data);
                $checkmail = $this->email_user("deep",$emailID,$did,$data['p_body']);
                if($checkmail){
                    $this->load->view('discussionDetails_view',$post_data);
                }

            } else {
                $this->load->view('fail_view');
            }
        }
        else {
            $this->load->view('fail_view');
        }
    }
    public function addEmail(){
        $data['title'] = "Marist Disussion Forums";
        // Submitted form data
        //$data['cwid']   = $_POST['cwid'];
        $data['cwid'] = $_SESSION['user'];
        $cwid = $_SESSION['user'];
        $data['emailid'] = $this->input->post('emailid');
        $data['firstname'] = $this->input->post('firstname');
        $data['lastname'] = $this->input->post('lastname');
        if($data['emailid'] != NULL){
            if($this->Discussion_model->addEmailId($data)){
                $userquery = $this->Discussion_model->checkuniqueuser($cwid);
                if($userquery){
                    if($_SESSION['firstname']='') {
                        $data['username'] = $this->Discussion_model->getusername($cwid);
                        $_SESSION['firstname'] = $data['username'];
                    } else {
                        $_SESSION['fisrtname'] = 	$this->input->post('firstname');
                    }
                } else {
                    $data['username'] = '';
                }
                $data['title'] = "Marist Disussion Forums";
                $this->load->view('createDiscussion_vieww',$data);
            } else {
                $this->load->view('fail_view');
            }
        } else {
            $this->load->view('fail_view');
        }
    }

    public function search_discussion(){
        $data['title'] = "Marist Disussion Forums";
        $ds_num = $this->uri->segment(3);
        $did = $this->Discussion_model->find_discussion($ds_num);
        if($did != '') {
            $discussion_data['query'] = $this->Discussion_model->fetch_discussion($did);
            $discussion_data['postquery'] = $this->Discussion_model->fetch_post($did);
            $this->load->view('discussionDetails_view',$discussion_data);
        } else {
            $this->load->view('fail_view');
        }
    }

    /*email_user
     * $requesterName = discussion creator
     * $requesterEmail = category email
     * $requestID = $d_id
     */
    public function email_user($requesterName, $requesterEmail, $d_id, $ds_body){
        $this->load->library('email');
        $this->load->model('Discussion_model');
        $config['protocol'] = "sendmail";
        $config['smtp_host'] = "tls://smtp.googlemail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = "cannavinolibrary@gmail.com";
        $config['smtp_pass'] = "redfoxesLibrary";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";
        $this->email->initialize($config);
        $this->email->from('cannavinolibrary@gmail.com', 'James A. Cannavino Library');
        $this->email->to($requesterEmail);

        $six_digit_random_string =  $this -> generateRandomString();
        $UUID=$six_digit_random_string.$d_id;
        $six_digit_random_string =  $this -> generateRandomString();
        $UUID = $UUID.$six_digit_random_string;

        $url = base_url()."/Discussion/discussionDetails/".$UUID;

        $this->email->subject("Discussion Id: " . $d_id);

        $emailBody = '<html><body>';

        $emailBody .= '<table width="100%"; rules="all" style="border:1px solid #3A5896;" cellpadding="10">';

        $emailBody .= "<h4>Dear $requesterName,<br/><br/>Someone posted a discussion.</h4></br></tr>";

        $emailBody .= "<tr><td colspan=2 font='colr:#3A5896;'><br />><I>*Please click the link below for the discussion.<br />$url</I></td></tr>";

        $emailBody .= "</table>";

        $emailBody .= "</body></html>";
        $this->email->message($emailBody);

        if ($this->email->send()) {
            return 1;
        } else {
            return 0;
        }
    }

    public function generateRandomString() {
        $length = 6;
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHI0123456789JKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
?>
