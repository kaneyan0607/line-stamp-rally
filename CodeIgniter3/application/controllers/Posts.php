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
    public function search()
    {
        //フロントへの返り値
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => ""),
            'is_answer' => 0, 'is_entry' => 0, 'stamnp_result' => 0, 'is_complete' => 0
        );
        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        // var_dump($status);
        $data['title'] = '①ステータスの取得';
        $this->load->view('templates/header', $data);

        //バリデーション。line_idを必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');

        $id = $this->input->post('line_id');

        if ($this->form_validation->run() === FALSE) { //formに入力されているか判定

            $status['result'] = 0;
            $status['error_info']['error_code'] = 400;
            $status['error_info']['error_message'] = "入力値がありません。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('line/search');
        } elseif (!preg_match('/^[a-z0-9-._]{2,40}$/', $id)) { //正規表現でline_idの値を検証

            $status['result'] = 0;
            $status['error_info']['error_code'] = 400;
            $status['error_info']['error_message'] = "line_idに使用できる文字は、半角英字 (小文字)、数字、「.」、 「-」、「_」となります。また、文字数は2~40文字以内で入力してください。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/search');
        } else {

            //WHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
            $data['users'] = $this->users_model->get_users();
            if (empty($data['users'])) { //もしも引数が空なら

                // echo "該当するユーザーがいません。";
                $status['error_info']['error_code'] = 401;
                $status['error_info']['error_message'] = "データベースと照合しましたが該当するユーザーがいません。";
                echo json_encode($status, JSON_UNESCAPED_UNICODE);
                $data['result'] = '該当するユーザーがいません。';
                $this->load->view('line/failure', $data);
            } else {

                //もしもスタンプの数が6個あればコンプリートフラグを立てる。
                $stamps = $data['users']['cnt']; //スタンプの数
                if ($stamps === "6") {
                    $status['is_complete'] = 1;
                }

                $status['is_answer'] = 1;
                $status['is_entry'] = 1;
                $status['stamnp_result'] = $stamps;

                $data['status'] = $status; //フロントへの返り値(viewでjsonencodeしている。)
                //正しく入力された時は検索結果ページを表示する
                $this->load->view('line/index', $data);
            }
        }
        $this->load->view('templates/footer');
    }



    //②データベースに初めて情報を入力するためのcreateメソッド　情報登録とともにスタンプが1つ付与
    public function create()
    {
        //フロントへの返り値
        $status = array(
            'result' => 0, 'error_info' => array('error_code' => 400, 'error_message' => "")
        );

        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = '②アンケートを登録する。';
        $this->load->view('templates/header', $data);

        //バリデーション。line_idとline_name、アンケート結果を必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');
        $this->form_validation->set_rules('line_name', 'Line_name', 'required');
        $this->form_validation->set_rules('answer', 'Answer', 'required');

        $id = $this->input->post('line_id');
        $name = $this->input->post('line_name');
        $ansswer = $this->input->post('answer');

        if ($this->form_validation->run() === FALSE) { //入力フォームに値が入ったかの判定

            $status['error_info']['error_message'] = "入力値がありません。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('line/create');
        } elseif (!preg_match('/^[a-z0-9-._]{2,40}$/', $id)) { //line_idの正規表現判定

            $status['error_info']['error_message'] = "line_idに使用できる文字は、半角英字 (小文字)、数字、「.」、 「-」、「_」となります。また、文字数は2~40文字以内で入力してください。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } elseif (!preg_match("/^.{1,20}$/", $name)) { //line_nameの正規表現判定

            $status['error_info']['error_message'] = "line_nameは1文字以上20文字以内です";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } elseif (!('北海道' === $ansswer || '青森県' === $ansswer || '岩手県' === $ansswer || '宮城県' === $ansswer || '秋田県' === $ansswer || '山形県' === $ansswer || '福島県' === $ansswer || '茨城県' === $ansswer || '栃木県' === $ansswer || '群馬県' === $ansswer || '埼玉県' === $ansswer || '千葉県' === $ansswer || '東京都' === $ansswer || '神奈川県' === $ansswer || '新潟県' === $ansswer || '富山県' === $ansswer || '石川県' === $ansswer || '福井県' === $ansswer || '山梨県' === $ansswer || '長野県' === $ansswer || '岐阜県' === $ansswer || '静岡県' === $ansswer || '愛知県' === $ansswer || '三重県' === $ansswer || '滋賀県' === $ansswer || '京都府' === $ansswer || '大阪府' === $ansswer || '兵庫県' === $ansswer || '奈良県' === $ansswer || '和歌山県' === $ansswer || '鳥取県' === $ansswer || '島根県' === $ansswer || '岡山県' === $ansswer || '広島県' === $ansswer || '山口県' === $ansswer || '徳島県' === $ansswer || '香川県' === $ansswer || '愛媛県' === $ansswer || '高知県' === $ansswer || '福岡県' === $ansswer || '佐賀県' === $ansswer || '長崎県' === $ansswer || '熊本県' === $ansswer || '大分県' === $ansswer || '宮崎県' === $ansswer || '鹿児島県' === $ansswer || '沖縄県' === $ansswer)) {

            //都道府県の判定
            $status['error_info']['error_message'] = "line_nameは1文字以上20文字以内です";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } else {

            //アンケート回答済みかの確認
            $data['users'] = $this->users_model->get_users();

            if (empty($data['users'])) { //回答していなかったら

                //正しく入力された時は成功ページを表示する
                $data['result'] = 'アンケートにご回答いただきありがとうございます！スタンプが1つ付きました。';
                $this->users_model->set_answer();
                $this->load->view('line/success', $data);
                $status['error_info']['error_code'] = "";
                $status['result'] = 1;
                echo 'JSONENCODE:' . json_encode($status, JSON_UNESCAPED_UNICODE);
            } else { //回答していたら

                $status['error_info']['error_code'] = 409;
                $status['error_info']['error_message'] = "既に回答済みです";
                echo json_encode($status, JSON_UNESCAPED_UNICODE);
                $data['result'] = '既に回答済みです';
                $this->load->view('line/success', $data);
            }
        }
        $this->load->view('templates/footer');
    }

    //③スタンプを押していく処理 getで受け取るのはline_id
    public function stamp()
    {
        //フロントへの返り値のための変数を準備
        $status = array(
            'result' => 1, 'error_info' => array('error_code' => "", 'error_message' => ""),
            'stamnp_result' => 0, 'is_complete' => 0
        );

        //フォームヘルパーとフォームライブラリをロードする。
        $this->load->helper('form');
        $this->load->library('form_validation');

        // var_dump($status);
        $data['title'] = '③スタンプを押す';
        $this->load->view('templates/header', $data);

        //バリデーション。line_idを必須入力、requiredに設定する。
        $this->form_validation->set_rules('line_id', 'Line_id', 'required');
        $id = $this->input->post('line_id');

        if ($this->form_validation->run() === FALSE) {

            $status['result'] = 0;
            $status['error_info']['error_code'] = 400;
            $status['error_info']['error_message'] = "入力値がありません。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('line/stamp');
        } elseif (!preg_match('/^[a-z0-9-._]{2,40}$/', $id)) { //正規表現でline_idの値を検証

            $status['result'] = 0;
            $status['error_info']['error_code'] = 400;
            $status['error_info']['error_message'] = "line_idに使用できる文字は、半角英字 (小文字)、数字、「.」、 「-」、「_」となります。また、文字数は2~40文字以内で入力してください。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/stamp');
        } else {

            //WHERE line_id = $id のusersとアンケート情報をモデル経由で連想配列として取得する。
            $data['users'] = $this->users_model->get_users();
            $stamps = $data['users']['cnt'];
            if (empty($stamps)) { //もしもスタンプを押した回数が空なら

                $status['error_info']['error_code'] = 401;
                $status['error_info']['error_message'] = "キャンペーンにエントリーしていません";
                echo json_encode($status, JSON_UNESCAPED_UNICODE);
                $data['result'] = 'キャンペーンにエントリーしていません';
                $this->load->view('line/failure');
            } elseif ($stamps < 6) { //スタンプが6つ以下か判定

                //スタンプを本日押したのか確認する。
                $stamp_array = $this->users_model->get_stamp();
                $stamp_day = $stamp_array["DATE_FORMAT(created_at, '%Y-%m-%d')"];
                $today = date('Y-m-d');

                if ($today === $stamp_day) {
                    $status['error_info']['error_code'] = 409;
                    $status['error_info']['error_message'] = "本日はスタンプを押しています。";
                    echo json_encode($status, JSON_UNESCAPED_UNICODE);
                    $this->load->view('line/stamp');
                } else {
                    //もしもスタンプの数が6以下ならスタンプを一つINSERTする。
                    echo 'スタンプを一つ付与します';
                    $db['error_log'] = $this->users_model->set_stamp();

                    //スタンプを押した後にスタンプの数を再度カウントして代入
                    $data['users'] = $this->users_model->get_users();
                    $stamps = $data['users']['cnt'];
                    $status['stamnp_result'] = $stamps;

                    echo json_encode($status);
                    $this->load->view('line/stamp');
                }
            } else {

                echo 'スタンプ6つでコンプリートしています。';
                $status['stamnp_result'] = $stamps;
                $status['is_complete'] = 1;
                echo json_encode($status);
                $this->load->view('line/stamp');
            }
        }
        $this->load->view('templates/footer');
    }
}
