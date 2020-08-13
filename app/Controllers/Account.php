<?php 

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserTokenModel;

class Account extends BaseController
{
	protected $userModel;

	public function __construct()
	{
		$this->userModel = new UserModel();
		$this->userTokenModel = new UserTokenModel();
		session();

		// Google API Initialization
		require_once ROOTPATH . 'vendor/google/vendor/autoload.php';
		$this->gClient = new \Google_Client();
		$this->gClient->setClientId(getenv('GOOGLE_API_CLIENT_ID'));
		$this->gClient->setClientSecret(getenv('GOOGLE_API_CLIENT_SECRET'));
		$this->gClient->setRedirectUri(getenv('GOOGLE_API_REDIRECT_URI'));
		$this->gClient->addScope('email');
		$this->gClient->addScope('profile');

		// Facebook API Initialization
		require_once ROOTPATH . 'vendor/facebook/vendor/autoload.php';
		$this->fClient = new \Facebook\Facebook([
			'app_id' => getenv('FACEBOOK_API_APP_ID'),
			'app_secret' => getenv('FACEBOOK_API_APP_SECRET'),
			'default_graph_version' => getenv('FACEBOOK_API_DEFAULT_GRAPH_VERSION')
		]);
		$this->fClientHelper = $this->fClient->getRedirectLoginHelper();
	}

	public function index()
	{
		$data = [
			'title' => 'Account Login',
			'validation' => \Config\Services::validation()
		];

		return view('account/index', $data);
	}

	public function register()
	{
		$data = [
			'title' => 'Account Registration',
			'validation' => \Config\Services::validation()
		];
		
		return view('account/register', $data);
	}

	public function create()
	{
		// Validation
		if (!$this->validate([
			'email' => [
				'rules' => 'required|valid_email|is_unique[pc_users.Email]',
				'errors' => [
					'is_unique' => 'This {field} has already registered'
				]
			],
			'fullname' => 'required',
			'phone' => 'required|numeric',
			'password' => 'required|min_length[8]|matches[confirmpassword]',
			'confirmpassword' => 'required|matches[password]'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/register'))->withInput()->with('validation', $validation);
		}

		$newuser = [
			'Email' => htmlspecialchars($this->request->getPost('email')),
        	'Password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        	'FullName' => htmlspecialchars($this->request->getPost('fullname')),
        	'Phone' => htmlspecialchars($this->request->getPost('phone')),
        	'EmailConfirm' => '0',
        	'PhoneConfirm' => NULL
		];

		$this->userModel->save($newuser);

		$this->sendConfirmationEmail($newuser['Email']);

		// session()->setFlashdata('message', 'Register success, please login!');
		session()->setFlashdata('message', 'Register success, please check your email to activate your account!');
        session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

		return redirect()->to(base_url('account'));
	}

	private function sendConfirmationEmail($email)
	{
		// GENERATE TOKEN

		$this->userTokenModel->insertToken($email, 'activation');
		$token = $this->userTokenModel->getToken($email, 'activation');

		// SEND EMAIL

		require ROOTPATH . 'vendor/sendgrid/vendor/autoload.php';

		$fromemail = getenv('SENDGRID_FROM_EMAIL');
		$fromname = getenv('SENDGRID_FROM_NAME');
		$contentplain = 'Please follow this link to activate your account \n ';
		$contentplain = $contentplain . base_url() . '/account/activate?token=' . urlencode($token);
		$contenthtml = '<h3><a href ="' . base_url() . '/account/activate?token=' . urlencode($token);
		$contenthtml = $contenthtml . '">Click to activate your account</a></h3>';

		$sendgridmail = new \SendGrid\Mail\Mail();
		$sendgridmail->setFrom($fromemail, $fromname);
		$sendgridmail->setSubject('PulauCoding Account Activation');
		$sendgridmail->addTo($email);
		$sendgridmail->addContent('text/plain', $contentplain);
		$sendgridmail->addContent('text/html', $contenthtml);

		$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

		try {
			$response = $sendgrid->send($sendgridmail);
		} catch (Exception $exc) {
			session()->setFlashdata('message', 'Error in sending email! ' . $exc->getMessage());
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account'));
		}
	}

	private function sendForgotEmail($email)
	{
		// GENERATE TOKEN

		$this->userTokenModel->insertToken($email, 'forgotpassword');
		$token = $this->userTokenModel->getToken($email, 'forgotpassword');

		// SEND EMAIL

		require ROOTPATH . 'vendor/sendgrid/vendor/autoload.php';

		$fromemail = getenv('SENDGRID_FROM_EMAIL');
		$fromname = getenv('SENDGRID_FROM_NAME');
		$contentplain = 'Please follow this link to reset your password \n ';
		$contentplain = $contentplain . base_url() . '/account/passwordreset?token=' . urlencode($token);
		$contenthtml = '<h3><a href ="' . base_url() . '/account/passwordreset?token=' . urlencode($token);
		$contenthtml = $contenthtml . '">Click to reset your password</a></h3>';

		$sendgridmail = new \SendGrid\Mail\Mail();
		$sendgridmail->setFrom($fromemail, $fromname);
		$sendgridmail->setSubject('PulauCoding Password Reset');
		$sendgridmail->addTo($email);
		$sendgridmail->addContent('text/plain', $contentplain);
		$sendgridmail->addContent('text/html', $contenthtml);

		$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));

		try {
			$response = $sendgrid->send($sendgridmail);
		} catch (Exception $exc) {
			session()->setFlashdata('message', 'Error in sending email! ' . $exc->getMessage());
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account'));
		}
	}

	public function login()
	{
		// Validation
		if (!$this->validate([
			'email' => 'required|valid_email',
			'password' => 'required'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account'))->withInput()->with('validation', $validation);
		}

		$email = $this->request->getPost('email');
		$password = $this->request->getPost('password');
		
		$user = NULL;
		$user = $this->userModel->getUserByEmail($email);

		if ($user)
        {
			if ($user['EmailConfirm'] == 1)
			{
				if (password_verify($password, $user['Password']))
				{
					$loginUser = [
						'UserId' => $user['Id'],
						'Email' => $user['Email'],
						'FullName' => $user['FullName']
					];

					session()->remove('loginUser');
					session()->set('loginUser', $loginUser);

					// var_dump(session()->get('loginUser'));

					return redirect()->to(base_url('home'));
				}
				else
				{
					session()->setFlashdata('message', 'Password incorrect!');
					session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

					return redirect()->to(base_url('account'))->withInput();
				}
			}
			else
            {
				session()->setFlashdata('message', 'Your account has not been activated!');
				session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

				return redirect()->to(base_url('account'))->withInput();
            }
		}
		else
		{
			session()->setFlashdata('message', 'User account not found!');
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account'))->withInput();
		}
	}

	public function logout()
    {
		session()->remove('loginUser');
		
		// Google API
		// Facebook API
		if (session()->get('access_token'))
		{
			// $this->gClient->revokeToken(); --->>> ini masih error ???
			session()->remove('access_token');
		};

		session_destroy();

		session()->setFlashdata('message', 'You are logged out!');
		session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

		return redirect()->to(base_url('account'));
	}

	public function activate()
    {
        $token = urlencode($this->request->getGet('token'));
        $email = $this->userTokenModel->getEmailByToken($token, 'activation');

        if ($email != NULL)
        {
			$this->userModel->activateUser($email);
			
			$this->userTokenModel->deleteToken($email, 'activation');

            session()->setFlashdata('message', 'Your account is now active. Please Login!');
            session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

            return redirect()->to(base_url('account'));
        }
        else
        {
            session()->setFlashdata('message', 'Your request has expired. Please make a new activation request!');
            session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

            return redirect()->to(base_url('account'));
        }

        return $token;
	}
	
	public function requestactivate()
	{
		$data = [
			'title' => 'Account Request Activation',
			'validation' => \Config\Services::validation()
		];

		return view('account/requestactivate', $data);
	}

	public function requestforgot()
	{
		$data = [
			'title' => 'Account Request Forgot Password',
			'validation' => \Config\Services::validation()
		];

		return view('account/requestforgot', $data);
	}

	public function processactivate()
	{
		// Validation
		if (!$this->validate([
			'email' => 'required|valid_email'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/requestactivate'))->withInput()->with('validation', $validation);
		}

		$email = $this->request->getPost('email');
		
		$user = NULL;
		$user = $this->userModel->getUserByEmail($email);

		if ($user)
        {
			if ($user['EmailConfirm'] != 1)
			{
				$this->sendConfirmationEmail($user['Email']);

				session()->setFlashdata('message', 'Request success, please check your email to activate your account!');
				session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

				return redirect()->to(base_url('account'))->withInput();
			}
			else
            {
				session()->setFlashdata('message', 'Your account is already active. Request rejected!');
				session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

				return redirect()->to(base_url('account/requestactivate'))->withInput();
            }
		}
		else
		{
			session()->setFlashdata('message', 'User account not found. Request rejected!');
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account/requestactivate'))->withInput();
		}
	}

	public function processforgot()
	{
		// Validation
		if (!$this->validate([
			'email' => 'required|valid_email'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/requestforgot'))->withInput()->with('validation', $validation);
		}

		$email = $this->request->getPost('email');
		
		$user = NULL;
		$user = $this->userModel->getUserByEmail($email);

		if ($user)
        {
			$this->sendForgotEmail($user['Email']);

			session()->setFlashdata('message', 'Request success, please check your email to reset your password!');
			session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

			return redirect()->to(base_url('account'))->withInput();
		}
		else
		{
			session()->setFlashdata('message', 'User account not found. Request rejected!');
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account/requestforgot'))->withInput();
		}
	}

	public function passwordreset()
	{
		$data = [
			'title' => 'Account Reset Password',
			'validation' => \Config\Services::validation()
		];

		return view('account/passwordreset', $data);
	}

	public function resetpassword()
	{
		$token = urlencode($this->request->getPost('token'));
		
		// Validation
		if (!$this->validate([
			'password' => 'required|min_length[8]|matches[confirmpassword]',
			'confirmpassword' => 'required|matches[password]'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url() . 'account/passwordreset?token=' . $token)->withInput()->with('validation', $validation);
		}

		$email = $this->userTokenModel->getEmailByToken($token, 'forgotpassword');
		$hashpassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        if ($email != NULL)
        {
			$this->userModel->updatePassword($email, $hashpassword);
			
			$this->userTokenModel->deleteToken($email, 'forgotpassword');

            session()->setFlashdata('message', 'Reset password success. You can login now!');
            session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

            return redirect()->to(base_url('account'));
        }
        else
        {
            session()->setFlashdata('message', 'Your request has expired. Please make a new request!');
            session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

            return redirect()->to(base_url('account'));
        }
	}

	

	public function passwordchange()
	{
		// Check if Login
		if(!$this->CheckLogin())
		{
			session()->setFlashdata('message', 'You are not logged in. This feature is not allowed!');
        	session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');
			
			return redirect()->to(base_url('account'));
		};

		// Check if Login with Internal Account
		if(!$this->CheckInternalLogin())
		{
			session()->setFlashdata('message', 'You are login with external account. This feature is not allowed!');
        	session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');
			
			return redirect()->to(base_url('home'));
		};

		$data = [
			'title' => 'Account Change Password',
			'validation' => \Config\Services::validation()
		];

		return view('account/passwordchange', $data);
	}

	public function changepassword()
	{
		// Validation
		if (!$this->validate([
			'password' => 'required|min_length[8]|matches[confirmpassword]',
			'confirmpassword' => 'required|matches[password]'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/passwordchange'))->withInput()->with('validation', $validation);
		}

		$email = session()->get('loginUser')['Email'];
		$hashpassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

		$this->userModel->updatePassword($email, $hashpassword);

        session()->setFlashdata('message', 'Change password success!');
        session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

        return redirect()->to(base_url('home'));
	}

	public function manage()
	{
		// Check if Login
		if(!$this->CheckLogin())
		{
			session()->setFlashdata('message', 'You are not logged in. This feature is not allowed!');
        	session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');
			
			return redirect()->to(base_url('account'));
		};

		// Check if Login with Internal Account
		if(!$this->CheckInternalLogin())
		{
			session()->setFlashdata('message', 'You are login with external account. This feature is not allowed!');
        	session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');
			
			return redirect()->to(base_url('home'));
		};

		$email = session()->get('loginUser')['Email'];
		$user = $this->userModel->getUserByEmail($email);

		$data = [
			'title' => 'Account Manage',
			'user' => $user,
			'validation' => \Config\Services::validation()
		];

		return view('account/manage', $data);
	}

	public function update()
	{
		// Validation
		if (!$this->validate([
			'fullname' => 'required',
			'phone' => 'required|numeric'
		]))
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/manage'))->withInput()->with('validation', $validation);
		}

		$email = $this->request->getPost('email');
        $fullname = htmlspecialchars($this->request->getPost('fullname'));
        $phone = htmlspecialchars($this->request->getPost('phone'));

		$this->userModel->updateProfile($email, $fullname, $phone);

		session()->setFlashdata('message', 'Update profile success!');
        session()->setFlashdata('msgclass', 'alert alert-success alert-dismissible fade show');

		return redirect()->to(base_url('home'));
	}

	public function upload()
	{
		// Validation
		if (!$this->validate([
			'avatar' => 'uploaded[avatar]|max_size[avatar,200]|is_image[avatar]|mime_in[avatar,image/jpg,image/png]'
		]))
		/*
		if (!$this->validate([
			'avatar' => [
				'rules' => 'uploaded[avatar]|max_size[avatar,200]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png]',
				'errors' => [
					'uploaded' => 'error uploaded',
					'max_size' => 'error max size',
					'is_image' => 'error is image',
					'mime_in'=> 'error mime type'
				]
			]
		]))
		*/
		{
			$validation = \Config\Services::validation();
			return redirect()->to(base_url('account/manage'))->withInput()->with('validation', $validation);
		}

		return 'Hello World!';
	}

	public function logingoogle()
	{
		$loginUrl = $this->gClient->createAuthUrl();

		return redirect()->to($loginUrl);
	}

	public function redirectgoogle()
	{
		if (isset($_GET['code']))
		{
			$token = $this->gClient->fetchAccessTokenWithAuthCode($_GET['code']);

			if (!isset($token['error']))
			{
				$this->gClient->setAccessToken($token['access_token']);
				
				session()->set('access_token', $token['access_token']);

				$oAuth = new \Google_Service_Oauth2($this->gClient);
				$userData = $oAuth->userinfo_v2_me->get();

				// Login success

				$loginUser = [
					'UserId' => $userData['id'],
					'Email' => $userData['email'],
					'FullName' => $userData['givenName'] . ' ' . $userData['familyName']
				];

				session()->remove('loginUser');
				session()->set('loginUser', $loginUser);

				// var_dump(session()->get('loginUser'));

				return redirect()->to(base_url('home'));
			}
			else
			{
				session()->setFlashdata('message', 'Authentication error!');
				session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

				return redirect()->to(base_url('account'));
			}
		}
		else
		{
			session()->setFlashdata('message', 'Google auth error!');
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account'));
		}
	}

	public function loginfacebook()
	{
		$loginUrl = $this->fClientHelper->getLoginUrl(getenv('FACEBOOK_API_REDIRECT_URL'), ['email']);
		
		return redirect()->to($loginUrl);
	}

	public function redirectfacebook()
	{
		if (isset($_GET['code']))
		{
			if (session()->get('access_token'))
			{
				$access_token = session()->get('access_token');
			}
			else
			{
				$access_token = $this->fClientHelper->getAccessToken();

				session()->set('access_token', $access_token);

				$this->fClient->setDefaultAccessToken(session()->get('access_token'));
			}

			$graph_response = $this->fClient->get("/me?fields=name,email", $access_token);

			$facebook_user_info = $graph_response->getGraphUser();

			/*
			if (!empty($facebook_user_info['id']))
			{
				$user_image = 'http://graph/facebook.com/' . $facebook_user_info['id'] . '/picture';
			}

			if (!empty($facebook_user_info['name']))
			{
				$user_name = $facebook_user_info['name'];
			}

			if (!empty($facebook_user_info['email']))
			{
				$user_email = $facebook_user_info['email'];
			}
			*/

			// Login success

			$loginUser = [
				'UserId' => $facebook_user_info['id'],
				'Email' => $facebook_user_info['email'],
				'FullName' => $facebook_user_info['name']
			];

			session()->remove('loginUser');
			session()->set('loginUser', $loginUser);

			// var_dump(session()->get('loginUser'));

			return redirect()->to(base_url('home'));
		}
		else
		{
			session()->setFlashdata('message', 'Facebook auth error!');
			session()->setFlashdata('msgclass', 'alert alert-danger alert-dismissible fade show');

			return redirect()->to(base_url('account'));
		}
	}
	


	private function CheckLogin()
	{
		if (session()->get('loginUser'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function CheckInternalLogin()
	{
		$email = session()->get('loginUser')['Email'];
		$user = $this->userModel->getUserByEmail($email);

		if ($user)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function test()
	{
		dd($this->request->getVar());
	}

	//--------------------------------------------------------------------

}
