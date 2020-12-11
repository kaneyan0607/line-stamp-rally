<?php
class Users_model extends CI_model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function get_users($id = FALSE)
    // $slugのエスケープ処理はQuery Builderがしてくれる
    {
        if ($id === FALSE) {
            //SELECT * FROM news
            $query = $this->db->get('users');

            //結果を配列で取得する。
            return $query->result_array();
        }

        // SELECT * FROM news WHERE 'slug' = $slug
        $query = $this->db->get_where('users', array('line_id' => $id));

        //結果を1行配列で取得する
        return $query->row_array();
    }
}
