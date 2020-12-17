<?php
class Users_model extends CI_model
{
    public function __construct()
    {
        $this->load->database();
    }

    //ユーザー情報を取得
    public function get_users()
    // $slugのエスケープ処理はQuery Builderがしてくれる
    {
        $id = $this->input->post('line_id');

        if ($id === FALSE) {
            //SELECT * FROM users
            $query = $this->db->get('users');

            //結果を配列で取得する。
            return $query->result_array();
        }

        $sql = "select users.*, COUNT(stamp_results.users_id) as cnt from users join stamp_results on users.id = stamp_results.users_id group by users_id having users.line_id = ? ";
        $query = $this->db->query($sql, array($id));
        //結果を1行配列で取得する
        return $query->row_array();
    }

    // アンケート結果を投稿するset_answer()メソッドを追加
    public function set_answer()
    {
        //URLヘルパーをロード。コントラクタでやってもよい。
        //アンサーセクションの時にコントローラーのコントラクトでロードするように設定しているのでここで明示的にロードしなくてもよい。
        $this->load->helper('url');

        $line_id = url_title($this->input->post('line_id'));
        $slug_name = url_title($this->input->post('line_name'));
        $slug_answer = url_title($this->input->post('answer'));

        $users = array(
            'line_id' => $line_id,
            'line_name' => $slug_name,
            'answer' => $slug_answer
        );

        $this->db->insert('users', $users); //usersテーブルにinsert PK
        // SELECT id FROM users WHERE 'line_id' = $line_id
        $this->db->select('id');
        $query = $this->db->get_where('users', array('line_id' => $line_id)); //アンケート登録後、PKのidを取得するためにselect文を実行
        //結果を1行配列で取得する
        return $query->row_array();
    }
    //urlencode() を使用する場合
    //urlencode無しでtitleに日本語データを入力すると個別ページが開かない
    //urlencodeを使うことで$slugに格納されたUTF-8エンコードされた日本語を認識する。

    //スタンプを押す
    public function set_stamp($id)
    {

        // $id = $this->input->post('line_id');

        //UPDATE文
        $this->db->set('users_id', $id);
        $query = $this->db->insert('stamp_results');
        return $query;
    }

    //最新のスタンプ投稿日を取得する
    public function get_stamp($id)
    {

        if ($id === FALSE) {
            //SELECT * FROM news
            $query = $this->db->get('stamp_results');

            //結果を配列で取得する。
            return $query->result_array();
        }

        //最新のスタンプ投稿日の日付と年数を取得する
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') FROM stamp_results WHERE users_id = ? ORDER BY created_at DESC LIMIT 1";
        $query = $this->db->query($sql, array($id));
        //結果を1行配列で取得する
        return $query->row_array();
    }
}
