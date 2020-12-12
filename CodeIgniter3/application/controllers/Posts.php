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

    // public function index()
    // {
    //     // echo "hello.!!!";
    //     //引数を指定せずに全ユーザーをモデル経由で連想配列として取得する。
    //     $data['users'] = $this->users_model->get_users();
    //     $data['questionnaire_results'] = $this->users_model->get_questionnaire();
    //     $data['title'] = 'index!';

    //     var_dump($data);
    //     echo '<br>';
    //     echo '<br>';
    //     // var_dump('UserName:' . $data['users'][0]['line_name']);
    //     var_dump('UserID:' . $data['users'][0]['line_id']);
    //     echo '<br>';
    //     echo '<br>';
    //     var_dump('AnsswerUserId:' . $data['questionnaire_results'][0]['ansswer_user_id']);

    //     $this->load->view('templates/header', $data);
    //     $this->load->view('line/index', $data);
    //     $this->load->view('templates/footer');
    // }
    public function index($id = NULL) //$slug = NULL
    {

        $data['title'] = '①ステータス取得';

        // echo "hello.!!!";
        //引数を指定せずに全ユーザーをモデル経由で連想配列として取得する。
        $data['users'] = $this->users_model->get_users($id);

        if (empty($data['users'])) { //もしも引数が空なら
            // show_404();
            echo "該当するユーザーがいません。";
        } else {
            echo '照合できました';
        }

        // var_dump($data['users']['line_id']);
        // var_dump('UserName:' . $data['users'][0]['line_name']);
        // var_dump('DBのUserID:' . $data['users'][0]['line_id']);
        echo '<br>';
        echo '<br>';
        var_dump('パラーメーター値:' . $id);
        echo '<br>';
        echo '<br>';
        var_dump($data);
        $this->load->view('templates/header', $data);
        $this->load->view('line/index', $data);
        $this->load->view('templates/footer');
    }
}
