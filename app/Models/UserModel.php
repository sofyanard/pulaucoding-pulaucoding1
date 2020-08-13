<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'pc_users';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'Email',
        'Password',
        'FullName',
        'Phone',
        'EmailConfirm',
        'PhoneConfirm'
    ];

    public function getUserByEmail($email)
    {
        return $this->where(['Email' => $email])->first();
    }

    public function activateUser($email)
    {
        $this->where(['Email' => $email])->set('EmailConfirm', 1)->update();
    }

    public function updatePassword($email, $hashpassword)
    {
        $this->where(['Email' => $email])->set('Password', $hashpassword)->update();
    }

    public function updateProfile($email, $fullname, $phone)
    {
        $data = [
            'FullName' => $fullname,
            'Phone' => $phone
        ];
        $this->where(['Email' => $email])->set($data)->update();
    }
}