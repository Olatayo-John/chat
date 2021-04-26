<?php

namespace App\Models;

use CodeIgniter\Model;

class Users_mdl extends Model
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function chats()
    {
        $chat_arr = array();

        $subquery = $this->db->get('users');
        foreach ($subquery->result() as $u_id) {
            $this->db->select('users.id as uid,users.unique_id,users.fname,users.lname,users.p_image,users.status,users.acct_status,messages.id as mid,messages.user_id_from,messages.user_id_to,messages.msg,messages.msg_status,messages.sent_at,messages.sent_date,messages.sent_time,messages.read_at');
            $this->db->from('messages');
            $this->db->join("users", "users.unique_id = " . htmlentities($u_id->unique_id) . "", "inner");
            $this->db->where("(user_id_from = '" . htmlentities($u_id->unique_id) . "' AND user_id_to = '" . $this->session->userdata('unique_id') . "') OR (user_id_from = '" . $this->session->userdata('unique_id') . "' AND user_id_to = '" . htmlentities($u_id->unique_id)  . "')");
            $this->db->order_by('messages.id', 'desc');
            $this->db->limit('1');
            $subquery = $this->db->get()->result();

            if (count($subquery) !== 0) {
                array_push($chat_arr, $subquery);
            }
        }

        $sortedchatarr = array();

        foreach ($chat_arr as $key => $row) {
            if (!empty($row[0])) {
                $sortedchatarr[$key] = $row[0]->sent_at;
            }
        }

        array_multisort($sortedchatarr, SORT_DESC, $chat_arr);

        // print_r($chat_arr);
        // die;
        return $chat_arr;
    }

    public function openchat($uidfrom, $uidto, $uniqueid, $msgid)
    {
        if (($this->session->userdata('unique_id') !== $uidfrom) && ($this->session->userdata('unique_id') == $uidto)) {
            $res = $this->markasread($uniqueid, $uidto, $uidfrom, $msgid);
        }
        // if ($res === true) {
        $this->db->select('users.id as uid,users.unique_id,users.fname,users.lname,users.p_image,users.status,users.acct_status,messages.id as mid,messages.user_id_from,messages.user_id_to,messages.msg,messages.msg_status,messages.sent_at,messages.sent_date,messages.sent_time');
        $this->db->join("users", "users.unique_id = " . htmlentities($uniqueid) . "", "inner");
        $this->db->where("(user_id_from = '" . htmlentities($uidfrom)  . "' AND user_id_to = '" . htmlentities($uidto)  . "') OR (user_id_from = '" . htmlentities($uidto)  . "' AND user_id_to = '" . htmlentities($uidfrom)  . "')");
        $query = $this->db->get('messages');
        return $query->result();
        // }
    }

    public function markasread($uniqueid, $uidto, $uidfrom, $msgid)
    {
        $res = $this->update_readat($uidfrom, $uidto, $msgid);
        if ($res === true) {
            $this->db->where(array('user_id_from' => $uidfrom, 'user_id_to' => $uidto));
            $this->db->set('msg_status', '0');
            $this->db->update('messages');
            return true;
        }
    }

    public function update_readat($uidfrom, $uidto, $msgid)
    {
        $this->db->where(array('user_id_from' => $uidfrom, 'user_id_to' => $uidto, 'id' => $msgid));
        $query = $this->db->get('messages')->row();
        if (empty($query->read_at)) {
            $this->db->where(array('user_id_from' => $uidfrom, 'user_id_to' => $uidto, 'id' => $msgid));
            $this->db->set('read_at', date("Y-m-d h:i:sa"));
            $this->db->update('messages');
        }
        return true;
    }

    public function chat_search($searchvalue)
    {
        $search_arr = array();

        $this->db->where(array('id !=' => $this->session->userdata('id'), 'unique_id !=' => $this->session->userdata('unique_id')));
        $this->db->like('fname', $searchvalue);
        $this->db->or_like('lname', $searchvalue);
        $this->db->order_by('fname', 'DESC');

        $query = $this->db->get('users')->result();

        foreach ($query as $uid) {
            $this->db->select('users.id as uid,users.unique_id,users.fname,users.lname,users.p_image,users.status,users.acct_status,messages.id as mid,messages.user_id_from,messages.user_id_to,messages.msg,messages.msg_status,messages.sent_at,messages.sent_date,messages.sent_time,messages.read_at');
            $this->db->from('messages');
            $this->db->join("users", "users.unique_id = " . htmlentities($uid->unique_id) . "", "inner");
            $this->db->where("(user_id_from = '" . htmlentities($uid->unique_id) . "' AND user_id_to = '" . $this->session->userdata('unique_id') . "') OR (user_id_from = '" . $this->session->userdata('unique_id') . "' AND user_id_to = '" . htmlentities($uid->unique_id)  . "')");
            $this->db->order_by('messages.id', 'desc');
            $this->db->limit('1');
            $subquery = $this->db->get()->result();

            if (count($subquery) > 0) {
                array_push($search_arr, $subquery);
            }
        }
        return $search_arr;
    }

    public function empty_chatsearch($searchvalue)
    {
        $search_arr = array();

        $this->db->where(array('id !=' => $this->session->userdata('id'), 'unique_id !=' => $this->session->userdata('unique_id')));
        $this->db->like('fname', $searchvalue);
        $this->db->or_like('lname', $searchvalue);
        $this->db->order_by('fname', 'DESC');

        $query = $this->db->get('users')->result();

        foreach ($query as $uid) {
            $this->db->select('*');
            $this->db->from('messages');
            $this->db->where("(user_id_from = '" . htmlentities($uid->unique_id) . "' AND user_id_to='" . $this->session->userdata('unique_id') . "' ) OR (user_id_to = '" . htmlentities($uid->unique_id)  . "' and user_id_from = '" . $this->session->userdata('unique_id') . "')");
            $this->db->order_by('messages.id', 'desc');
            $this->db->limit('1');
            $subquery = $this->db->get();

            if ($subquery->num_rows() == 0) {
                $this->db->where('unique_id', $uid->unique_id);
                $userquery = $this->db->get('users')->result();
                array_push($search_arr, $userquery);
            }
        }

        return $search_arr;
    }

    public function sendmsg($uidfrom, $uniqueid, $msg)
    {
        if (!empty($uidfrom) && !empty($uniqueid) && !empty($msg)) {
            $data = array(
                'user_id_from' => htmlentities($uidfrom),
                'user_id_to' => htmlentities($uniqueid),
                'msg' => htmlentities($msg),
                'msg_status' => '1',
                'sent_at' => date("Y-m-d h:i:sa"),
                'sent_date' => date("Y-m-d"),
                'sent_time' => date("h:i:sa"),
            );

            if ($this->db->insert('messages', $data)) {
                return $this->db->insert_id();
                exit;
            } else {
                return false;
                exit;
            }
        } else {
            return false;
            exit;
        }
    }

    public function outgoingdelete($msgid, $uidto, $uidfrom)
    {
        $this->db->where(array('id' => $msgid, 'user_id_to' => $uidto, 'user_id_from' => $uidfrom));
        if ($this->db->delete('messages')) {
            return true;
            exit;
        } else {
            return false;
            exit;
        }
    }

    public function msginfo($msgid, $uidto, $uidfrom)
    {
        $this->db->where(array('id' => $msgid, 'user_id_to' => $uidto, 'user_id_from' => $uidfrom));
        $query = $this->db->get('messages');
        if ($query->num_rows() > 0) {
            return $query->row();
            exit;
        } else {
            return false;
            exit;
        }
    }

    public function message_reload($uidfrom, $uniqueid)
    {
        $this->db->select('users.id as uid,users.unique_id,users.fname,users.lname,users.p_image,users.status,users.acct_status,messages.id as mid,messages.user_id_from,messages.user_id_to,messages.msg,messages.msg_status,messages.sent_at,messages.sent_date,messages.sent_time');
        $this->db->join("users", "users.unique_id = " . htmlentities($uniqueid) . "", "inner");
        $this->db->where(array('user_id_to' => htmlentities($uidfrom), 'user_id_from' => htmlentities($uniqueid), 'msg_status' => '1'));
        $query = $this->db->get('messages');
        return $query->result();
    }

    public function chatstatus($uidfrom, $uniqueid)
    {
        $this->db->where('unique_id', $uniqueid);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
            exit;
        }
    }

    public function friends()
    {
        $this->db->order_by('fname', 'asc');
        $this->db->where(array('id !=' => $this->session->userdata('id'), 'unique_id !=' => $this->session->userdata('unique_id')));
        $query = $this->db->get('users');
        return $query;
    }

    public function friend_search($searchvalue)
    {
        $this->db->where(array('id !=' => $this->session->userdata('id'), 'unique_id !=' => $this->session->userdata('unique_id')));
        $this->db->like('fname', $searchvalue);
        $this->db->or_like('lname', $searchvalue);
        $this->db->order_by('fname', 'DESC');

        $query = $this->db->get('users');
        return $query;
    }

    public function edit()
    {
        $this->db->where(array('id' => $this->session->userdata('id'), 'unique_id' => $this->session->userdata('unique_id')));
        $query = $this->db->get('users');
        return $query->row();
    }

    public function saveedit($p_image)
    {
        $this->db->where(array('id' => $this->session->userdata('id'), 'unique_id' => $this->session->userdata('unique_id')));
        $data = array(
            'fname' => htmlentities($this->input->post('fname')),
            'lname' => htmlentities($this->input->post('lname')),
            'gender' => htmlentities($this->input->post('gender')),
            'about' => htmlentities($this->input->post('about')),
            'p_image' => $p_image,
            'created_at' => date("Y-m-d h:i:sa"),
        );
        $this->db->update('users', $data);
        return true;
    }
}
