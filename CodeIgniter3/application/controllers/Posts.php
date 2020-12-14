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


    //①ステータスの取得
    public function index($id = NULL) //$slug = NULL
    {

        //フロントへの返り値
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
        if ($id !== NULL) {
            if (empty($data['users'])) { //もしも引数が空なら
                // show_404();
                echo "該当するユーザーがいません。";
                // var_dump($status);
            } else {
                echo $data['users']['line_name'] . 'の照合ができました';
                echo '<br>';
                $status = array(
                    'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => ""),
                    'is_answer' => 1, 'is_entry' => 1, 'stamnp_result' => $data['users']['stamp_result'], 'is_complete' => $data['users']['is_complete']
                );
                echo 'JSONENCODEしました: ' . json_encode($status);
            }
        } else {
            echo '$idがNULLです';
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



    //データベースに初めて情報を入力するためのcreateメソッド　情報登録とともにスタンプが1つ付与
    public function create()
    {
        //フロントへの返り値
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => "")
        );

        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'アンケートを登録する。';

        //バリデーション。line_idとline_name、アンケート結果を必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');
        $this->form_validation->set_rules('line_name', 'Line_name', 'required');
        $this->form_validation->set_rules('answer', 'Answer', 'required');

        if ($this->form_validation->run() === FALSE) {

            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('templates/header', $data);
            $this->load->view('line/create');
            $this->load->view('templates/footer');
        } else {
            //正しく入力された時は成功ページを表示する
            $this->users_model->set_answer();
            $this->load->view('line/success');
            echo json_encode($status);
        }
    }

    //スタンプを押していく処理 getで受け取るのはline_id
    public function stamp($id = NULL)
    {
        //フロントへの返り値
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => ""),
            'stamnp_result' => 0, 'is_complete' => 0
        );

        //現在のスタンプ情報を取得
        var_dump($status);

        //引数を指定してWHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
        $data['users'] = $this->users_model->get_users($id);

        echo '<br>';
        echo '<br>';
        echo '取得してきたアンケートデータ:';
        var_dump($data);
        echo '<br>';
        echo '<br>';

        //もしもスタンプの数が6以下ならスタンプを一つUPDATEする。
        $stamps = $data['users']['stamp_result'];
        if (empty($stamps)) { //空判定
            echo 'キャンペーンにエントリーしていません';
        } elseif ($stamps < 6) {
            echo 'スタンプを一つ付与します';
            $db['error'] = $this->users_model->set_stamp($id);
            $status['stamnp_result'] = $stamps;
            echo json_encode($status);
            echo '<br>';
            var_dump($db);
        } else {
            echo 'スタンプコンプリートしています。';
            $status['stamnp_result'] = $stamps;
            $status['is_complete'] = 1;
            echo json_encode($status);
        }

        //引数を指定してWHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
        $data['users'] = $this->users_model->get_users($id);

        echo '<br>';
        echo '<br>';
        echo '更新後に取得してきたアンケートデータ:';
        var_dump($data);
        echo '<br>';
        echo '<br>';
    }
}
