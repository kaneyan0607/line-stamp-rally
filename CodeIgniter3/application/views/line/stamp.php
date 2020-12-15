<!-- データベースにデータを入力するためのフォーム
titleとtextをフォームで入力し、モデル内でtitleを基にslugを作成する。 -->

<?php
//フォームバリデーションを行い、戻されたすべてのエラーメッセージを返す。
//メッセージがない場合、空も文字列を返す。
echo validation_errors();
?>

<?php
//formの開始タグを作成。action先をhttp://localhost:8888/line-stamp-rally/CodeIgniter3/searchに設定
//フォームヘルパーは自動的にCSRFのための隠しフィールドを挿入する
//リクエストメソッドはデフォルトではPOSTになるよう。
echo form_open('posts/stamp');
?>

<label for="line_id">line_id</label>
<input type="text" name="line_id"><br>

<input type="submit" name="submit" value="スタンプを押す" />

</form>