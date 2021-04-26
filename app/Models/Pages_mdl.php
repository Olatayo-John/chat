<?php

namespace App\Models;

use CodeIgniter\Model;

class Pages_mdl extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['unique_id', 'fname', 'lname', 'gender', 'email', 'about', 'v_code', 'pwd', 'p_image', 'status', 'acct_status', 'created_at'];

    public function login()
    {
        $email = htmlentities($_POST['email']);

        $query = $this->where('email', $email)->get();
        if ($query->getNumRows() == '0') {
            return "not_found";
            exit;
        } else {
            return $query->getRow();
            exit;
        }
    }

    public function is_online($id, $unique_id)
    {
        $unique_id = $this->session->userdata('unique_id');
        $id = $this->session->userdata('id');

        $this->where(array('id' => $id, 'unique_id' => $unique_id));
        $this->set('status', '1');
        $this->set('created_at', date(DATE_COOKIE));
        $this->update('users');
        return true;
    }

    public function is_offline($id, $unique_id)
    {
        $this->where(array('id' => $id, 'unique_id' => $unique_id));
        $this->set('status', '0');
        $this->set('created_at', date(DATE_COOKIE));
        $this->update('users');
        return true;
    }

    public function register($unique_id, $v_code, $p_image)
    {
        $data = array(
            'unique_id' => $unique_id,
            'fname' => htmlentities($this->input->post('fname')),
            'lname' => htmlentities($this->input->post('lname')),
            'gender' => htmlentities($this->input->post('gender')),
            'email' => htmlentities($this->input->post('email')),
            'about' => '',
            'v_code' => password_hash($v_code, PASSWORD_DEFAULT),
            'pwd' => password_hash($this->input->post('pwd'), PASSWORD_DEFAULT),
            'p_image' => $p_image,
            'status' => '0',
            'acct_status' => '0',
            'created_at' => date(DATE_COOKIE),
        );
        $this->insert('users', $data);
        return true;
        exit;
    }

    public function check_uniqueid($unique_id)
    {
        $this->where('unique_id', $unique_id);
        $query = $this->get();
        if ($query->getNumRows() == '0') {
            return false;
            exit;
        } else {
            return $query->getRow();
            exit;
        }
    }

    public function emailverify($unique_id)
    {
        $this->where('unique_id', $unique_id);
        $query = $this->get();
        if ($query->getNumRows() == '0') {
            return false;
            exit;
        } else {
            $request = \Config\Services::request();
            $vcode = htmlentities($request->getPost('vcode'));

            if (!empty($vcode) && strlen($vcode) > 0) {
                $data = $query->getRow();
                if (password_verify($vcode, $data->v_code)) {
                    $res = $this->update_acctstatus($unique_id);
                    if ($res !== true) {
                        return false;
                        exit;
                    } else {
                        return true;
                        exit;
                    }
                } else {
                    return false;
                    exit;
                }
            } else {
                return false;
                exit;
            }
        }
    }

    public function update_acctstatus($unique_id)
    {
        $this->where('unique_id', $unique_id)->set('acct_status', '1')->update();
        return true;
    }

    public function update_vcode($v_code, $unique_id)
    {
        $this->set('v_code', password_hash($v_code, PASSWORD_DEFAULT));
        $this->where('unique_id', $unique_id);
        $this->update();
        return true;
    }
}
