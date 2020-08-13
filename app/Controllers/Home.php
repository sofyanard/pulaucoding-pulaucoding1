<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		session();
		
		$data = [
			'title' => 'Home'
		];

		return view('home/index', $data);
	}

	public function info()
	{
		$data = [
			'title' => 'Info'
		];

		return view('home/info', $data);
	}

	public function welcome()
	{
		return view('welcome_message');
	}

	//--------------------------------------------------------------------

}
