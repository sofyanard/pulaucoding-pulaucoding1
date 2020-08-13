<?php namespace App\Models;

use CodeIgniter\Model;

class UserTokenModel extends Model
{
    protected $table = 'pc_usertoken';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'Email',
        'Token',
        'Remark'
    ];

    public function generateToken()
    {
        return bin2hex(random_bytes(32));
        // return base64_encode(random_bytes(32));
    }

    public function getToken($email, $remark)
    {
        $usertoken = $this->where(['Email' => $email, 'Remark' => $remark])->first();
        return $usertoken['Token'];
    }

    public function getEmailByToken($token, $remark)
    {
        $usertoken = $this->where(['Token' => $token, 'Remark' => $remark])->first();
        if ($usertoken != NULL)
        {
            return $usertoken['Email'];
        }
        else
        {
            return NULL;
        }
    }

    public function deleteToken($email, $remark)
    {
        $this->where(['Email' => $email, 'Remark' => $remark])->delete();
    }

    public function insertToken($email, $remark)
    {
        $this->deleteToken($email, $remark);

        $data = [
            'Email' => $email,
            'Token' => $this->generateToken(),
            'Remark' => $remark
        ];

        $this->save($data);
    }

    
}