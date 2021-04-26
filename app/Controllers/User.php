<?php

namespace App\Controllers;

use App\Models\Pages_mdl;
use App\Models\Users_mdl;

class User extends BaseController
{
    public function index()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $this->chats();
        }
    }

    public function logout()
    {
        $unique_id = $this->session->get('unique_id');
        $id = $this->session->get('id');

        // $Pages_mdl = new Pages_mdl();
        // $Pages_mdl->is_offline($id, $unique_id);

        $this->session->destroy();

        $this->session->setFlashdata('valid', 'Logged Out!');
        return redirect('login');
    }

    public function pushernoti()
    {
        $this->load->model('Users_mdl');
        $newmsg = $this->Users_mdl->new_msg();
        // $newmsg = false;
        if ($newmsg !== false) {
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );
            $pusher = new Pusher\Pusher(
                '0e610562daa4fcb70499',
                '2bc58618b327df709db3',
                '1190400',
                $options
            );
            $pusher->trigger('my-channel', 'my-event', $newmsg[0]->msg);
        }
    }

    public function chats()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $data['title'] = "chats";
            $this->load->model('Users_mdl');
            $data['chats'] = $this->Users_mdl->chats();
            // print_r($data['chats']);
            // die;

            echo view('templates/header', $data);
            echo view('chats', $data);
        }
    }

    public function openchat()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        if (isset($_POST['uidto']) && isset($_POST['uidfrom']) && isset($_POST['uniqueid']) && isset($_POST['msgid'])) {
            $this->load->model('Users_mdl');
            $data['chats'] = $this->Users_mdl->openchat($_POST['uidfrom'], $_POST['uidto'], $_POST['uniqueid'], $_POST['msgid']);
            $data['token'] = csrf_hash();

            echo json_encode($data);
        }
    }

    public function chatlist_reload()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        $output = "";

        $this->load->model('Users_mdl');
        $data = $this->Users_mdl->chats();

        if (count($data) == 0) {
            $output = '<div class="no_chat text-danger"><h6>You have no chats</h6></div>';
            echo $output;
        } else {
            for ($i = 0; $i < count($data); $i++) {
                if (isset($data[$i][0])) {
                    $output = '<div class="chat"uniqueid="' . $data[$i][0]->unique_id . '" u_id_from="' . $data[$i][0]->user_id_from . '" u_id_to="' . $data[$i][0]->user_id_to . '" msgid="' . $data[$i][0]->mid . '">
                                    <div class="chat_img">
                                        <img src="' . base_url('assets/images/' . $data[$i][0]->p_image) . '" width="50px" height="50px">
                                    </div>
                                    <div class="chat_details">
                                        <div class="chat_details_up">
                                            <div class="chat_name">
                                                <h6>' . ucfirst($data[$i][0]->fname) . " " . ucfirst($data[$i][0]->lname) . '</h6>
                                            </div>';
                    if (($data[$i][0]->msg_status == '1') && ($data[$i][0]->unique_id === $data[$i][0]->user_id_from)) {
                        $output .= '<div class="chat_noti">
                                        <i class="fas fa-envelope text-success"></i>
                                    </div>';
                    }
                    $output .= '</div>
                                <div class="chat_details_down d-flex">
                                    <div class="chat_msg">
                                        ' . word_limiter($data[$i][0]->msg, 6) . '
                                    </div>
                                    <div class="chat_time">
                                        <div class="">' . $data[$i][0]->sent_time . '</div>
                                    </div>
                                </div>
                                </div>
                            </div>';
                    echo $output;
                }
            }
        }
    }

    public function chat_search()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        $output = "";

        $this->load->model('Users_mdl');
        $data['userchat'] = $this->Users_mdl->chat_search(htmlentities($_POST['searchvalue']));
        $data['nochats'] = $this->Users_mdl->empty_chatsearch(htmlentities($_POST['searchvalue']));

        if (count($data['userchat']) === 0) {
            $output .= '<div class="no_user p-3 text-light font-weight-bolder">No messages with user "<span class="text-danger">' . $_POST['searchvalue'] . '</span>"</div>';
        } else {
            for ($i = 0; $i < count($data['userchat']); $i++) {
                if (isset($data['userchat'][$i][0])) {
                    $output .= '<div class="chat" uniqueid="' . $data['userchat'][$i][0]->unique_id . '" u_id_from="' . $data['userchat'][$i][0]->user_id_from . '" u_id_to="' . $data['userchat'][$i][0]->user_id_to . '" msgid="' . $data['userchat'][$i][0]->mid . '">
                                    <div class="chat_img">
                                        <img src="' . base_url('assets/images/' . $data['userchat'][$i][0]->p_image) . '" width="50px" height="50px">
                                    </div>
                                    <div class="chat_details">
                                        <div class="chat_details_up">
                                            <div class="chat_name">
                                                <h6>' . ucfirst($data['userchat'][$i][0]->fname) . " " . ucfirst($data['userchat'][$i][0]->lname) . '</h6>
                                            </div>';
                    if (($data['userchat'][$i][0]->msg_status == '1') && ($data['userchat'][$i][0]->unique_id === $data['userchat'][$i][0]->user_id_from)) {
                        $output .= '<div class="chat_noti">
                                        <i class="fas fa-envelope text-success"></i>
                                    </div>';
                    }
                    $output .= '</div>
                                <div class="chat_details_down d-flex">                                 
                                    <div class="chat_msg">
                                        ' . word_limiter($data['userchat'][$i][0]->msg, 6) . '
                                    </div>
                                    <div class="chat_time">
                                        <div class="">' . $data['userchat'][$i][0]->sent_time . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            }
        }

        if (count($data['nochats']) > 0) {

            $output .= '<h6 class="contact">CONTACTS</h6>';

            for ($i = 0; $i < count($data['nochats']); $i++) {
                if (!empty($data['nochats'][$i][0])) {
                    $output .= '<div class="empty_chats_list">
                        <div class="empty_chat" uniqueid="' . $data['nochats'][$i][0]->unique_id . '" u_pimage="' . $data['nochats'][$i][0]->p_image . '" u_name="' . $data['nochats'][$i][0]->fname . ' ' . $data['nochats'][$i][0]->lname . '" u_status="' . $data['nochats'][$i][0]->status . '">
                            <div class="empty_chat_img">
                                <img src="' . base_url('assets/images/' . $data['nochats'][$i][0]->p_image) . '" width="50px" height="50px">
                            </div>
                            <div class="empty_chat_details">
                                <div class="empty_chat_name">
                                    <h6>' . ucfirst($data['nochats'][$i][0]->fname) . " " . ucfirst($data['nochats'][$i][0]->lname) . '</h6>
                                </div>
                                <div class="empty_chat_msg">

                                </div>
                            </div>
                        </div>
                    </div>';
                }
            }
        }

        echo $output;
    }

    public function sendmsg()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        $this->load->model('Users_mdl');
        if (isset($_POST['msg']) && isset($_POST['uidfrom']) && isset($_POST['uniqueid'])) {
            $res = $this->Users_mdl->sendmsg($_POST['uidfrom'], $_POST['uniqueid'], $_POST['msg']);

            if (isset($res) && !empty($res) && $res != false) {
                $data['res'] = 'success';
                $data['msg'] = htmlentities($_POST['msg']);
                $data['return_id'] = $res;
            } else {
                $data['res'] = 'error';
            }
        } else {
            $data['res'] = 'error';
        }

        $data['token'] = csrf_hash();
        echo json_encode($data);
    }

    public function outgoingdelete()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        if (isset($_POST['msgid']) && isset($_POST['uidto']) && isset($_POST['uidfrom'])) {
            $this->load->model('Users_mdl');
            $res = $this->Users_mdl->outgoingdelete($_POST['msgid'], $_POST['uidto'], $_POST['uidfrom']);
            // $res = false;
            if ($res !== true) {
                $data['res'] = 'error';
            } else {
                $data['res'] = 'success';
            }
        }

        $data['token'] = csrf_hash();
        echo json_encode($data);
    }

    public function msginfo()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        if (isset($_POST['msgid']) && isset($_POST['uidto']) && isset($_POST['uidfrom'])) {
            $this->load->model('Users_mdl');
            $res = $this->Users_mdl->msginfo($_POST['msgid'], $_POST['uidto'], $_POST['uidfrom']);
            if ($res == false) {
                $data['res'] = 'error';
            } else {
                $data['info'] = $res;
                $data['res'] = 'success';
            }
        }

        $data['token'] = csrf_hash();
        echo json_encode($data);
    }

    public function message_reload()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }
        if (!empty($_POST['uidfrom']) && !empty($_POST['uniqueid'])) {

            $this->load->model('Users_mdl');
            $data['info'] = $this->Users_mdl->message_reload($_POST['uidfrom'], $_POST['uniqueid']);
            $data['userstatus'] = $this->Users_mdl->chatstatus($_POST['uidfrom'], $_POST['uniqueid']);
            $data['token'] = csrf_hash();
            echo json_encode($data);
        }
    }

    public function friends()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $data['title'] = "friends";
            $this->load->model('Users_mdl');
            $data['friends'] = $this->Users_mdl->friends();

            echo view('templates/header', $data);
            echo view('friends', $data);
        }
    }

    public function friendlist_reload()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        $output = "";

        $this->load->model('Users_mdl');
        $data = $this->Users_mdl->friends();

        foreach ($data->result() as $friend) {
            $output = '<div class="friend">
                            <div class="friend_details">
                                <a href=""><img src="' . base_url("assets/images/" . $friend->p_image) . '"></a>
                            </div>
                            <div class="friend_details">
                                <a href="" class="friend_name">' . $friend->fname . ' ' . $friend->lname . '</a>';
            if ($friend->status == '1') {
                $output .= '<i class="fas fa-circle text-success ml-1"></i>';
            } elseif ($friend->status == '0') {
                $output .= '<i class="fas fa-circle text-danger ml-1"></i>';
            }
            $output .= '</div>
                        </div>';

            echo $output;
        }
    }

    public function friend_search()
    {
        if ($this->session->get('logged_in') !== '1') {
            return false;
        }

        $output = "";

        $this->load->model('Users_mdl');
        $data = $this->Users_mdl->friend_search(htmlentities($_POST['searchvalue']));


        if ($data->num_rows() == 0) {
            $output = '<div class="no_user p-3 text-light font-weight-bolder">User "<span class="text-danger">' . $_POST['searchvalue'] . '</span>" does not exist</div>';
            echo $output;
        } else {
            foreach ($data->result() as $friend) {
                $output = '<div class="friend">
                                <div class="friend_details">
                                    <a href=""><img src="' . base_url("assets/images/" . $friend->p_image) . '"></a>
                                </div>
                                <div class="friend_details">
                                    <a href="" class="friend_name">' . $friend->fname . ' ' . $friend->lname . '</a>';
                if ($friend->status == '1') {
                    $output .= '<i class="fas fa-circle text-success ml-1"></i>';
                } elseif ($friend->status == '0') {
                    $output .= '<i class="fas fa-circle text-danger ml-1"></i>';
                }
                $output .= '</div>
                            </div>';
                echo $output;
            }
        }
    }

    public function profile()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $data['title'] = "profile";
            $this->load->model('Users_mdl');
            $data['info'] = $this->Users_mdl->edit();

            echo view('templates/header', $data);
            echo view('profile', $data);
        }
    }

    public function edit()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $this->form_validation->set_rules('fname', 'First Name', 'required|trim|html_escape');
            $this->form_validation->set_rules('lname', 'Last Name', 'required|trim|html_escape');
            $this->form_validation->set_rules('gender', 'Gender', 'required|trim|html_escape');
            $this->form_validation->set_rules('about', 'About', 'trim|html_escape');

            if ($this->form_validation->run() == FALSE) {
                $data['title'] = "edit";
                $this->load->model('Users_mdl');
                $data['info'] = $this->Users_mdl->edit();

                echo view('templates/header', $data);
                echo view('edit', $data);
            } else {
                $rand = mt_rand(0, 10000000000);
                $fname = str_replace(" ", "_", strtolower(htmlentities($this->input->post("fname"))));

                if ($_FILES['p_image']['name']) {
                    $config['upload_path'] = './assets/images';
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';
                    $config['max_size'] = '5120';
                    $config['max_height'] = '2000';
                    $config['max_width'] = '2000';
                    $config['file_name'] = $rand . "_" . $fname;
                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('p_image')) {
                        $upload_error = array('error' => $this->upload->display_errors());
                        foreach ($upload_error as $error) {
                            $this->session->setFlashdata('invalid', $error);
                        }
                        return redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        $uploaded = $_FILES['p_image']['name'];
                        $uploaded_ext = htmlentities(strtolower(pathinfo($uploaded, PATHINFO_EXTENSION)));
                        $data = array('upload_data' => $this->upload->data());
                        $p_image = $rand . "_" . $fname . "." . $uploaded_ext;

                        $def_images = array('male.png', 'female.png');
                        if (!in_array($this->session->get('p_image'), $def_images)) {
                            $file_path = './assets/images/' . $this->session->get('p_image') . '';
                            unlink($file_path);
                        }

                        $this->session->set('p_image', $p_image);
                        $this->session->set('fname', htmlentities($this->input->post('fname')));
                        $this->session->set('lname', htmlentities($this->input->post('lname')));
                    }
                } else {
                    if (htmlentities($this->input->post('gender')) == 'male') {
                        $p_image = 'male.png';
                        $this->session->set('p_image', $p_image);
                    } else {
                        $p_image = 'female.png';
                        $this->session->set('p_image', $p_image);
                    }
                }

                $this->load->model('Users_mdl');
                $res = $this->Users_mdl->saveedit($p_image);
                if ($res != true) {
                    $this->session->setFlashdata('invalid', 'Error updating Profile!');
                    return redirect('edit');
                } else {
                    $this->session->setFlashdata('valid', 'Profile updated!');
                    return redirect('profile');
                }
            }
        }
    }

    public function settings()
    {
        if ($this->session->get('logged_in') !== '1') {
            return redirect('login');
        } else {
            $data['title'] = "settings";
            $this->load->model('Users_mdl');
            // $data['info'] = $this->Users_mdl->settings();

            echo view('templates/header', $data);
            echo view('settings', $data);
        }
    }
}
