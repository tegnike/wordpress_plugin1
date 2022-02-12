<?php

/*
Plugin Name: CurrentYear
Version: 1.0
*/


// json 形式のデータを扱うための定義
header('Content-type: application/json');
// PHP5.1.0以上はタイムゾーンの定義が必須
date_default_timezone_set('Asia/Tokyo');
  
// HTMLエスケープ処理
function hsc_utf8($str) {
  return htmlspecialchars($str, ENT_QUOTES,'UTF-8');
}

/**
 * [current_year] returns the Current Year as a 4-digit string.
 * @return string Current Year
*/
add_shortcode( 'current_year', 'current_year' );

function current_year($result) {

    // --------------------------
    // 個別設定項目（３つ）
    // --------------------------
    // 送信先メールアドレス
    $to = 'xxxx@xxxx.com';
    // メールタイトル
    $subject = 'お問い合わせフォームより';
    // ドメイン（リファラチェックと送信元メールアドレスに利用）
    $domain = "http://localhost:8000/";
    
    //変数初期化
    $errflg =0;    // エラー判定フラグ
    $dispmsg ='';  // 画面出力内容
    
    // 入力項目
    $nameval = '';   // 名前
    $mailval = '';   // メールアドレス
    $urlval = '';    // ウェブサイト
    $textval = '';   // 内容
    $referrer = '';  // 遷移元画面
    
    // 画面からのデータを取得
    if(isset($_POST['nameval'])){ $nameval = $_POST['nameval']; }
    if(isset($_POST['mailval'])){ $mailval = $_POST['mailval']; }
    if(isset($_POST['urlval'])){ $urlval = $_POST['urlval']; }
    if(isset($_POST['textval'])){ $textval = $_POST['textval']; }
    if(isset($_POST['referrer'])){ $referrer = $_POST['referrer']; }
    
    if(strpos($_SERVER['HTTP_REFERER'], $domain) === false){
    // リファラチェック
    $dispmsg = '<p id="errmsg">【リファラチェックエラー】お問い合わせフォームから入力されなかったため、メール送信できませんでした。</p>';
    $errflg = 1;
    }
    else if($nameval == '' || $mailval == '' || $textval == ''){
    //必須チェック
    $dispmsg = '<p id="errmsg">【エラー】名前・メールアドレス・内容は必須項目です。</p>';
    $errflg = 1;
    }
    else if(!preg_match("/^[.!#%&-_0-9a-zA-Z?/+]+@[!#%&-_0-9a-z]+(.[!#%&-_0-9a-z]+)+$/", $mailval) || count( explode('@',$mailval) ) !=2){
    //メールアドレスチェック
    $dispmsg .= '<p id="errmsg">【エラー】メールアドレスの形式が正しくありません。</p>';
    $errflg = 1;
    }
    else{
    // メールデータ作成
    $subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject,'JIS','UTF-8'))."?=";
    $message= '名前：'.$nameval."n";
    $message.='メール：'.$mailval."n";
    $message.='ウェブサイト：'.$urlval."n";
    $message.="n――――――――――――――――――――――――――――――nn";
    $message.=$textval;
    $message.="nn――――――――――――――――――――――――――――――n";
    $message.='送信日時：'.date( "Y/m/d (D) H:i:s", time() )."n";
    $message.='送信元IPアドレス：'.@$_SERVER["REMOTE_ADDR"]."n";
    $message.='送信元ホスト名：'.getHostByAddr(getenv('REMOTE_ADDR'))."n";
    $message.='リファラURL：'.$referrer."n";
    $message.='お問い合わせページ：'.@$_SERVER['HTTP_REFERER']."n";
    $message= mb_convert_encoding($message,'JIS','UTF-8');
    $fromName = mb_encode_mimeheader(mb_convert_encoding($nameval,'JIS','UTF-8'));
    $header ='From: '.$fromName.'<wordpress@'.$domain.'>'."n";
    $header.='Reply-To: '.$mailval."n";
    $header.='Content-Type:text/plain;charset=iso-2022-jpnX-Mailer: PHP/'.phpversion();
    // メール送信
    $retmail = mail($to,$subject,$message,$header);
    
    // 送信結果の判定
    if( $retmail ){
        $dispmsg ='<p class="success">メールを送信しました。返信までしばらくお待ちください。</p>';
        $dispmsg.='<blockquote><p>名前： '.hsc_utf8($nameval).'<br />';
        $dispmsg.= 'メール： '.hsc_utf8($mailval).'<br />';
        $dispmsg.= 'ウェブサイト： '.hsc_utf8($urlval).'</p>';
        $dispmsg.= '<pre>'.hsc_utf8($textval).'</pre></blockquote>';
    }else{
        $dispmsg .= '<p id="errmsg">【エラー】メール送信に失敗しました。。</p>';
        $errflg = 1;
    }
    }
    
    // 処理結果を画面に戻す
    $result = array('errflg'=>$errflg, 'dispmsg'=>$dispmsg);


    $result = '<form id="mailform">
  <div>
    <label for="nameval">名前<span>(必須)</span></label> <input
    type="text" name="nameval" id="nameval" required />
  </div>
  <div>
    <label for="mailval">メールアドレス<span>(必須)</span></label> <input
    type="email" name="mailval" id="mailval" required />
  </div>
  <div>
    <label for="urlval">ウェブサイト</label> <input
    type="text" name="urlval" id="urlval" />
  </div>
  <div>
    <label for="textval">内容<span>(必須)</span></label> <textarea
    name="textval" id="textval" rows="12" required></textarea>
  </div>
  <p class="contact-submit">
    <input type="submit" value="送信 »" id="submit"> <input
    id="referrer" type="hidden" name="referrer" />
  </p>
</form>
<div id="dispmsg"></div>';

    return $result;
}

add_action('init', 'salcodes_init');

/** Always end your PHP files with this closing tag */
?>
