<?php
class Users_model extends CI_model
{
    public function __construct()
    {
        $this->load->database();
    }

    // //ユーザー情報を取得
    // public function get_users($id = FALSE)
    // // $slugのエスケープ処理はQuery Builderがしてくれる
    // {
    //     if ($id === FALSE) {
    //         //SELECT * FROM news
    //         $query = $this->db->get('users');

    //         //結果を配列で取得する。
    //         return $query->result_array();
    //     }

    //     // SELECT * FROM news WHERE 'slug' = $slug
    //     $query = $this->db->get_where('users', array('line_id' => $id));

    //     //結果を1行配列で取得する
    //     return $query->row_array();
    // }

    // //アンケート結果を取得
    // public function get_questionnaire($id = FALSE)
    // {
    //     if ($id === FALSE) {
    //         //SELECT * FROM news
    //         $query = $this->db->get('questionnaire_results');

    //         //結果を配列で取得する。
    //         return $query->result_array();
    //     }

    //     // SELECT * FROM news WHERE 'slug' = $slug
    //     $query = $this->db->get_where('questionnaire_results', array('line_id' => $id));

    //     //結果を1行配列で取得する
    //     return $query->row_array();
    // }

    //ユーザー情報を取得
    // public function get_users($id = FALSE)
    // // $slugのエスケープ処理はQuery Builderがしてくれる
    // {
    //     if ($id === FALSE) {
    //         //SELECT * FROM news
    //         $query = $this->db->get('users');

    //         //結果を配列で取得する。
    //         return $query->result_array();
    //     }

    //     // SELECT * FROM news WHERE 'slug' = $slug
    //     $query = $this->db->get_where('users', array('line_id' => $id));

    //     //結果を1行配列で取得する
    //     return $query->row_array();
    // }

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
}
