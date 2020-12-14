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
            //SELECT * FROM users,stamp_results
            $sql = "SELECT * FROM `users` JOIN stamp_results ON users.line_id = stamp_results.line_id";
            $query = $this->db->get($sql);

            //結果を配列で取得する。
            return $query->result_array();
        }

        // 次を生成:
        // SELECT * FROM stamp_results JOIN users ON users.line_id = stamp_results.line_id
        // 件数を取得

        $sql = "select users.*, COUNT(stamp_results.line_id) as cnt from users join stamp_results on users.line_id = stamp_results.line_id group by line_id having users.line_id = $id";
        $query = $this->db->query($sql);

        if (!$query) {
            log_message('debug', 'SELECTに失敗しました。');
            $error = $this->db->error();
            log_message('debug', print_r($error, true));
            return null;
        }

        //結果を1行配列で取得する
        return $query->row_array();


        // 次を生成:
        // SELECT * FROM questionnaire_results JOIN users ON users.line_id = questionnaire_results.ansswer_user_id
        // $select = array('users.*, COUNT(stamp_results.line_id)');
        // $join = array('stamp_results', 'users.line_id = stamp_results.line_id');
        // $groupby = array('line_id');
        // $query = $this->db->select($select)->$this->db->join($join[0], $join[1])->$this->db->group_by($groupby)->having('users', array('line_id' => $id));

        // //結果を1行配列で取得する
        // return $query->row_array();
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
            'line_id' => $this->input->post('line_id')
        );

        $this->db->insert('users', $data); //usersテーブルにinsert PK
        return $this->db->insert('stamp_results', $data_sub); //アンケート結果テーブルにinsert FK
    }
    //urlencode() を使用する場合
    //urlencode無しでtitleに日本語データを入力すると個別ページが開かない
    //urlencodeを使うことで$slugに格納されたUTF-8エンコードされた日本語を認識する。

    public function set_stamp($id = FALSE)
    {

        // if ($id === FALSE) {
        //     //SELECT * FROM users,stamp_results
        //     $query = $this->db->get('stamp_results');

        //     //結果を配列で取得する。
        //     return $query->result_array();
        // }

        //UPDATE文
        $this->db->set('stamp_result', "stamp_result + 1", false);
        $this->db->where('line_id', $id);
        $query = $this->db->update('stamp_results');
        return $query; // gives UPDATE `stamp_results` SET `stamp_result` = 'stamp_result+1' WHERE `id` = 2
        // // SELECT * FROM stamp_results WHERE 'line_id' = $id
        // $query = $this->db->get_where('stamp_results', array('line_id' => $id));

        // //結果を1行配列で取得する
        // return $query->row_array();
    }
}
