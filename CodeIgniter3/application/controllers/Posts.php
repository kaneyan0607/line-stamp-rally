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
        $this->load->helper('url_helper');
    }


    //①ステータスの取得
    public function search() //$slug = NULL
    {
        //フロントへの返り値
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => NULL, 'error_message' => NULL),
            'is_answer' => 0, 'is_entry' => 0, 'stamnp_result' => 0, 'is_complete' => 0
        );
        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        // var_dump($status);
        $data['title'] = '①ステータスの取得';

        //バリデーション。line_idを必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');

        if ($this->form_validation->run() === FALSE) {

            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('templates/header', $data);
            $this->load->view('line/search');
            $this->load->view('templates/footer');
        } else {
            //WHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
            $data['users'] = $this->users_model->get_users();
            if (empty($data['users'])) { //もしも引数が空なら
                // echo "該当するユーザーがいません。";
                $data['result'] = '該当するユーザーがいません。';
                $this->load->view('templates/header', $data);
                $this->load->view('line/failure', $data);
                $this->load->view('templates/footer');
            } else {
                $data['status'] = $status; //フロントへの返り値
                //正しく入力された時は検索結果ページを表示する
                $this->load->view('templates/header', $data);
                $this->load->view('line/index', $data);
                $this->load->view('templates/footer');
                // echo json_encode($status);
            }
        }
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

            //アンケート回答済みかの確認
            // $id = $this->input->post('line_id');
            $data['users'] = $this->users_model->get_users();

            if (empty($data['users'])) {
                //正しく入力された時は成功ページを表示する
                $data['result'] = 'アンケートにご回答いただきありがとうございます！スタンプが1つ付きました。';
                $this->users_model->set_answer();
                $this->load->view('line/success', $data);
                echo 'JSONENCODE:' . json_encode($status);
            } else {
                $data['result'] = '既に回答済みです';
                $this->load->view('line/success', $data);
            }
        }
    }

    //スタンプを押していく処理 getで受け取るのはline_id
    public function stamp()
    {
        //フロントへの返り値
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => ""),
            'stamnp_result' => 0, 'is_complete' => 0
        );

        //スタンプを押す前のスタンプ情報
        var_dump($status);

        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        // var_dump($status);
        $data['title'] = 'スタンプを押す';

        //バリデーション。line_idを必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');

        if ($this->form_validation->run() === FALSE) {

            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('templates/header', $data);
            $this->load->view('line/stamp');
            $this->load->view('templates/footer');
        } else {
            //WHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
            $data['users'] = $this->users_model->get_users();
            $stamps = $data['users']['cnt'];
            if (empty($stamps)) { //もしも引数が空なら
                $data['result'] = 'キャンペーンにエントリーしていません';
                $this->load->view('templates/header', $data);
                $this->load->view('line/failure');
                $this->load->view('templates/footer');
            } elseif ($stamps < 6) {
                //もしもスタンプの数が6以下ならスタンプを一つUPDATEする。
                echo 'スタンプを一つ付与します';
                $db['error_log'] = $this->users_model->set_stamp();
                $status['stamnp_result'] = $stamps;
                echo json_encode($status);
                $this->load->view('templates/header', $data);
                $this->load->view('line/stamp');
                $this->load->view('templates/footer');
                // echo '<br>';
                // var_dump($db);
            } else {
                echo 'スタンプコンプリートしています。';
                // $data['result'] = 'スタンプコンプリートしています。';
                // $this->load->view('templates/header', $data);
                // $this->load->view('line/failure');
                // $this->load->view('templates/footer');
                $status['stamnp_result'] = $stamps;
                $status['is_complete'] = 1;
                echo json_encode($status);
                $this->load->view('templates/header', $data);
                $this->load->view('line/stamp');
                $this->load->view('templates/footer');
            }
        }
        //スタンプを押した後のスタンプ情報
        echo '<br>';
        echo '<br>';
        echo 'スタンプを押した後のスタンプ情報';
        var_dump($status);
    }

    //スタンプを本日押したのか確認する。GETでline_idを渡すと該当するline_idの押されたスタンプの最新の日付を取得する。
    public function stamp_day($id = NULL)
    {


        $stamp_array = $this->users_model->get_stamp($id);
        $stamp_day = $stamp_array["DATE_FORMAT(created_at, '%Y-%m-%d')"];

        echo 'DBから取得したスタンプの日付です。';
        var_dump($stamp_day);
        echo '<br>';
        echo 'PHPで取得した本日の日付です。';
        $today = date('Y-m-d');
        var_dump($today);
        echo '<br>';
        if (empty($stamp_day)) {
            echo 'ユーザーがいません';
        } elseif ($today == $stamp_day) {
            echo '本日は既にスタンプを押しました';
        } else {
            echo '本日はまだスタンプを押して無いです！';
        }
    }
}
