<!-- データベースにデータを入力するためのフォーム
titleとtextをフォームで入力し、モデル内でtitleを基にslugを作成する。 -->

<?php
//フォームバリデーションを行い、戻されたすべてのエラーメッセージを返す。
//メッセージがない場合、空も文字列を返す。
echo validation_errors();
?>

<?php
//formの開始タグを作成。action先をhttp://localhost:8888/line-stamp-rally/CodeIgniter3/createに設定
//フォームヘルパーは自動的にCSRFのための隠しフィールドを挿入する
//リクエストメソッドはデフォルトではPOSTになるよう。
echo form_open('posts/create');
?>

<label for="line_id">line_id</label>
<input type="text" name="line_id"><br>

<label for="line_name">line_name</label>
<input type="text" name="line_name"><br>

<label for="answer">都道府県（アンケート）</label>
<textarea name="answer"></textarea><br>

<input type="submit" name="submit" value="送信" />

</form>