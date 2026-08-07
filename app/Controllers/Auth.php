<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login(): string
    {
        return view('login');
    }

    public function do_login(): string
    {
        $data = $this->request->getJSON(true);

        $email = $data['email'];
        $passwd = $data['passwd'];

        $query = $this->db->query("select passwd from auth where email = ? LIMIT 1", [$email]);
        
        if($query->getNumRows() > 0){
            $hash = $query->getRow()->passwd;
            
            if (password_verify($passwd, $hash)) {
                $this->session->set('logined', true);
                $this->session->set('email', $email);
                return json_encode(['code'=>100]);
            } else {
                $this->session->set('logined', false);
                return json_encode(['code'=>0]);
            }
        }
        $this->session->set('logined', false);
        return json_encode(['code'=>0]);
    }
}
