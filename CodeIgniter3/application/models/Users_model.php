<?php
class Users_model extends CI_model
{
    public function __construct()
    {
        $this->load->database();
    }

    //ユーザー情報を取得
    public function get_users($id = FALSE)
    // $slugのエスケープ処理はQuery Builderがしてくれる
    {
        if ($id === FALSE) {
            //SELECT * FROM users,questionnaire_results
            $query = $this->db->get('users', 'questionnaire_results');

            //結果を配列で取得する。
            return $query->result_array();
        }

        // 次を生成:
        // SELECT * FROM questionnaire_results JOIN users ON users.line_id = questionnaire_results.ansswer_user_id
        $join = array('questionnaire_results', 'users.line_id = questionnaire_results.ansswer_user_id');
        $query = $this->db->join($join[0], $join[1])->get_where('users', array('line_id' => $id));

        //結果を1行配列で取得する
        return $query->row_array();
    }

    // アンケート結果を投稿するset_answer()メソッドを追加
    public function set_answer()
    {
        //URLヘルパーをロード。コントラクタでやってもよい。
        //アンサーセクションの時にコントローラーのコントラクトでロードするように設定しているのでここで明示的にロードしなくてもよい。
        $this->load->helper('url');

        // $slug_name = urlencode(url_title($this->input->post('line_name'), '-', TRUE));
        // $slug_answer = urlencode(url_title($this->input->post('answer'), '-', TRUE));
        $slug_name = url_title($this->input->post('line_name'));
        $slug_answer = url_title($this->input->post('answer'));

        $data = array(
            'line_id' => $this->input->post('line_id'),
            'line_name' => $slug_name
        );

        $data_sub = array(
            'answer' => $slug_answer,
            'ansswer_user_id' => $this->input->post('line_id')
        );

        $this->db->insert('users', $data); //usersテーブルにinsert PK
        return $this->db->insert('questionnaire_results', $data_sub); //アンケート結果テーブルにinsert FK
    }
}

//urlencode() を使用する場合
//urlencode無しでtitleに日本語データを入力すると個別ページが開かない
//urlencodeを使うことで$slugに格納されたUTF-8エンコードされた日本語を認識する。
