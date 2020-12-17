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
            'answer' => 0, 'entry' => 0, 'stamnp' => 0, 'complete' => 0
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
                    $status['complete'] = 1;
                }

                $status['answer'] = 1;
                $status['entry'] = 1;
                $status['stamnp'] = $stamps;

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
        $name_cnt = mb_strlen($this->input->post('line_name')); //mb_strlenは全角、半角も一文字判定
        $answer = $this->input->post('answer');

        if ($this->form_validation->run() === FALSE) { //入力フォームに値が入ったかの判定

            $status['error_info']['error_message'] = "入力値がありません。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            //submit前や不正な入力な時はフォームを表示する。
            $this->load->view('line/create');
        } elseif (!preg_match('/^[a-z0-9-._]{2,40}$/', $id)) { //line_idの正規表現判定

            $status['error_info']['error_message'] = "line_idに使用できる文字は、半角英字 (小文字)、数字、「.」、 「-」、「_」となります。また、文字数は2~40文字以内で入力してください。";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } elseif (1 < $name_cnt && $name_cnt > 21) { //line_nameの文字数判定

            $status['error_info']['error_message'] = "line_nameは1文字以上20文字以内です";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } elseif (!('北海道' === $answer || '青森県' === $answer || '岩手県' === $answer || '宮城県' === $answer || '秋田県' === $answer || '山形県' === $answer || '福島県' === $answer || '茨城県' === $answer || '栃木県' === $answer || '群馬県' === $answer || '埼玉県' === $answer || '千葉県' === $answer || '東京都' === $answer || '神奈川県' === $answer || '新潟県' === $answer || '富山県' === $answer || '石川県' === $answer || '福井県' === $answer || '山梨県' === $answer || '長野県' === $answer || '岐阜県' === $answer || '静岡県' === $answer || '愛知県' === $answer || '三重県' === $answer || '滋賀県' === $answer || '京都府' === $answer || '大阪府' === $answer || '兵庫県' === $answer || '奈良県' === $answer || '和歌山県' === $answer || '鳥取県' === $answer || '島根県' === $answer || '岡山県' === $answer || '広島県' === $answer || '山口県' === $answer || '徳島県' === $answer || '香川県' === $answer || '愛媛県' === $answer || '高知県' === $answer || '福岡県' === $answer || '佐賀県' === $answer || '長崎県' === $answer || '熊本県' === $answer || '大分県' === $answer || '宮崎県' === $answer || '鹿児島県' === $answer || '沖縄県' === $answer)) {

            //都道府県の判定
            $status['error_info']['error_message'] = "line_nameは1文字以上20文字以内です";
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
            $this->load->view('line/create');
        } else {

            //アンケート回答済みかの確認
            $data['users'] = $this->users_model->get_users();
            echo 'アンケート回答済みかの確認:';
            var_dump($data);

            if (empty($data['users'])) { //回答していなかったら

                //正しく入力された時は成功ページを表示する
                $data['result'] = 'アンケートにご回答いただきありがとうございます！スタンプが1つ付きました。';
                $data['id'] = $this->users_model->set_answer(); //アンケートに登録と同時にinsertしたカラムのpkを取得

                $this->users_model->set_stamp($data['id']['id']); //スタンプを登録

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
            'stamnp' => 0, 'complete' => 0
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
                $this->load->view('line/failure', $data);
            } elseif ($stamps < 6) { //スタンプが6つ以下か判定

                //スタンプを本日押したのか確認する。
                $stamp_array = $this->users_model->get_stamp($data['users']['id']);
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
                    $data['stamp_log'] = $this->users_model->set_stamp($data['users']['id']); //ユーザー情報のPKであるidを渡す

                    //スタンプを押した後にスタンプの数を再度カウントして代入
                    $data['users'] = $this->users_model->get_users();
                    $stamps = $data['users']['cnt'];
                    $status['stamnp'] = $stamps;

                    echo json_encode($status);
                    $this->load->view('line/stamp');
                }
            } else {

                echo 'スタンプ6つでコンプリートしています。';
                $status['stamnp'] = $stamps;
                $status['complete'] = 1;
                echo json_encode($status);
                $this->load->view('line/stamp');
            }
        }
        $this->load->view('templates/footer');
    }
}
