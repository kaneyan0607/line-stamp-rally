<?php

class Posts extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //モデルのロード。users_modelというオブジェクト名で利用できる。データベースの情報をモデルから持ってきている。
        //このコントローラーの他のメソッドで使う。
        $this->load->model('users_model');

        //system/helpersのURLヘルパー関数をロード。ビューで行う。
        // $this->load->helper('url_helper');
    }

    public function index()
    {
        // echo "hello.!!!";
        //引数を指定せずに全ユーザーをモデル経由で連想配列として取得する。
        $data['users'] = $this->users_model->get_users();

        $data['title'] = 'index!';

        var_dump($data);
        echo '  /  ';
        var_dump($data['users'][0]['line_name'] . '!!!!!!!!!!!');

        $this->load->view('templates/header', $data);
        $this->load->view('line/index', $data);
        $this->load->view('templates/footer');
    }
}
