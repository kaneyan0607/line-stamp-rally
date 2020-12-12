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

    public function index($id = NULL) //$slug = NULL
    {

        $status = array(
            'result' => 1, 'error_info' => array('error_code' => NULL, 'error_message' => NULL),
            'is_answer' => 0, 'is_entry' => 0, 'stamnp_result' => 0, 'is_complete' => 0
        );

        var_dump($status);

        echo '<br>';
        $data['title'] = '①ステータス取得';

        //引数を指定してWHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
        $data['users'] = $this->users_model->get_users($id);

        //↓取得した値に対しての正規表現による判定を後ほど実装
        if ($id === NULL) {
            echo '$idがNULLです';

            if (empty($data['users'])) { //もしも引数が空なら
                // show_404();
                echo "該当するユーザーがいません。";
                // var_dump($status);
            } else {
                echo '照合できました';
                echo $data['users']['line_name'];
                echo '<br>';
                $status = array(
                    'result' => 1, 'error_info' => array('error_code' => NULL, 'error_message' => NULL),
                    'is_answer' => 1, 'is_entry' => 1, 'stamnp_result' => $data['users']['stamp_result'], 'is_complete' => $data['users']['is_complete']
                );
                echo json_encode($status);
            }
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
