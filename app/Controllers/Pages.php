<?php

namespace App\Controllers;

use App\Models\Pages_mdl;

class Pages extends BaseController
{
	public function index()
	{
		if ($this->session->has('logged_in') && $this->session->get('logged_in') == '1') {
			return redirect('chats');
		} else {
			$this->login();
		}
	}

	public function login()
	{
		if ($this->session->has('logged_in') && $this->session->get('logged_in') == '1') {
			return redirect('chats');
		}

		$rules = [
			'email' => [
				'rules' => 'required|valid_email|trim',
				'errors' => [
					'required' => 'Your email is required',
					'valid_email' => 'Please provide a valid email address'
				]
			],
			'pwd' => [
				'rules' => 'required',
				'errors' => [
					'required' => 'Your password is required'
				]
			]
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->validate($rules)) {

			$Pages_mdl = new Pages_mdl();
			$res = $Pages_mdl->login();

			if ($res === 'not_found') {
				$this->session->setFlashdata('invalid', 'Incorrect Email/Password!');
				return redirect('login');
			} else {
				if ($res->acct_status == "0") {
					$this->session->setFlashdata('invalid', 'Please verify your account!');
					return redirect()->to('emailverify/' . $res->unique_id);
				} else {
					if (password_verify($_POST['pwd'], $res->pwd)) {
						echo "yes";
						die;
						$user_data = array(
							'id' => $res->id,
							'unique_id' => $res->unique_id,
							'fname' => $res->fname,
							'lname' => $res->lname,
							'email' => $res->email,
							'p_image' => $res->p_image,
							'status' => $res->status,
							'logged_in' => '1',
						);
						$this->session->set($user_data);
						$this->session->setFlashdata('valid', 'Welcome ' . $res->fname . ' ' . $res->lname . '');

						$unique_id = $this->session->get('unique_id');
						$id = $this->session->get('id');
						$this->Pages_mdl->is_online($id, $unique_id);
						$this->session->set('status', '1');

						return redirect('chats');
					} else {
						$this->session->setFlashdata('invalid', 'Incorrect Email/Password!');
						return redirect('login');
					}
				}
			}
		} else {
			$data['title'] = "login";
			echo view('templates/header', $data);
			echo view('login');
		}
	}

	public function register()
	{
		if ($this->session->get('logged_in') == '1') {
			return redirect('chats');
		}

		$rules = [
			'fname' => [
				'rules' => 'trim|required',
				'errors' => [
					'required' => 'Your First name is required'
				]
			],
			'lname' => [
				'rules' => 'trim|required',
				'errors' => [
					'required' => 'Your Last name is required'
				]
			],
			'gender' => [
				'rules' => 'trim|required',
				'errors' => [
					'required' => 'Your gender is required'
				]
			],
			'email' => [
				'rules' => 'trim|valid_email|required|is_unique[users.email]',
				'errors' => [
					'required' => 'Your email is required',
					'valid_email' => 'Please provide a valid email address',
					'is_unique' => '{field} "{value}" already exist',
				]
			],
			'pwd' => [
				'rules' => 'trim|required',
				'errors' => [
					'required' => 'Please pick a password'
				]
			],
		];

		if (!$this->validate($rules)) {
			$data['title'] = "register";

			echo view('templates/header', $data);
			echo view('register');
		} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->validate($rules)) {
			print_r($_POST);
			die;
			$rand = mt_rand(0, 10000000000);
			$fname = str_replace(" ", "_", strtolower(htmlentities($this->input->post("fname"))));

			if ($_FILES['p_image']['name']) {
				$config['upload_path'] = './assets/images';
				$config['allowed_types'] = 'jpg|jpeg|png|';
				$config['max_size'] = '2048';
				$config['max_height'] = '3000';
				$config['max_width'] = '3000';
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
				}
			} else {
				$gender = htmlentities($this->input->post('gender'));
				if ($gender === 'male') {
					$p_image = "male.png";
				} else if ($gender === 'female') {
					$p_image = "female.png";
				}
			}

			$name = htmlentities($this->input->post('fname'));
			$email = htmlentities($this->input->post('email'));
			$v_code = mt_rand(0, 999999);
			$unique_id = $rand;
			$link = base_url('emailverify/' . $unique_id);

			$res = $this->send_vcode($name, $email, $v_code, $link);
			if ($res !== true) {
				$this->session->setFlashdata('invalid', $res);
				return redirect('register');
			} else {
				// $this->load->model('Pages_mdl');
				// $res = $this->Pages_mdl->register($unique_id, $v_code, $p_image);
				$res = false;
				if ($res !== true) {
					$this->session->setFlashdata('invalid', 'Error creating your account.Please try again!');
					return redirect('register');
				} else {
					$this->session->setFlashdata('valid', 'Verification code has been sent to your registered email address!');
					return redirect('emailverify/' . $unique_id);
				}
			}
		}
	}

	public function send_vcode($name, $email, $v_code, $link)
	{
		$body = "Hello " . $name . "\n\nYour verification code is " . $v_code . "\nEnter the above code in our website to activate your account.\nClick here " . $link . "\n\nIf you have any questions, send us an email at info@nktech.in.\n\nBest Regards,\nNKTECH\nhttps://nktech.in";

		$this->email->setFrom('jvweedtest@gmail.com', 'ChatApp');
		$this->email->setTo($email);
		$this->email->setSubject("Your Verification Code");
		$this->email->setMessage($body);

		if ($this->email->send()) {
			return true;
		} else {
			return $this->email->printDebugger();
		}
	}

	public function emailverify($unique_id)
	{
		$Pages_mdl = new Pages_mdl();
		$res = $Pages_mdl->check_uniqueid($unique_id);

		if ($res == false) {
			$this->session->setFlashdata('invalid', 'Wrong credentials');
			return redirect('login');
		} else {
			$acct_status = $res->acct_status;
			if ($acct_status == '1') {
				$this->session->setFlashdata('valid', 'Your account has been verified.');
				return redirect('login');
			}

			$rules = [
				'vcode' => 'required|trim'
			];

			if (!$this->validate($rules)) {
				$data['key'] = $unique_id;
				$data['title'] = 'verification';

				// $this->session->setFlashdata('invalid', 'Invalid code');
				echo view('templates/header', $data);
				echo view('templates/emailverify', $data);
			} else {
				$res = $Pages_mdl->emailverify($unique_id);
				if ($res !== true) {
					$this->session->setFlashdata('invalid', 'Invalid code');
					return redirect()->to($unique_id);
				} else {
					$this->session->setFlashdata('valid', 'Your account has been verified!');
					return redirect('login');
				}
			}
		}
	}

	public function resend_vcode($unique_id)
	{
		$Pages_mdl = new Pages_mdl();
		$res = $Pages_mdl->check_uniqueid($unique_id);

		if ($res == false) {
			$this->session->setFlashdata('invalid', 'Wrong credentials');
			return redirect('login');
		} else {
			$acct_status = $res->acct_status;
			if ($acct_status == '1') {
				$this->session->setFlashdata('valid', 'Your account has already been verified.');
				return redirect('login');
			} else {
				$name = $res->fname;
				$email = $res->email;
				$v_code = mt_rand(0, 999999);
				$link = base_url('emailverify/' . $unique_id);

				$mres = $this->send_vcode($name, $email, $v_code, $link);

				if ($mres !== true) {
					$this->session->setFlashdata('invalid', "Error sending mail. Please try again");
					return redirect()->back();
				} else {
					$Pages_mdl->update_vcode($v_code, $unique_id);
					$this->session->setFlashdata('valid', 'Verification code sent to your mail.');
					return redirect()->back();
				}
			}
		}
	}

	public function contact()
	{
		$data['title'] = "contact";
		echo view('templates/header', $data);
		echo view('templates/contact');
	}
}
