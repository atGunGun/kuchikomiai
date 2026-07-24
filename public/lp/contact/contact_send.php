<?php
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ===========================
       1. 必須項目の空チェック
    =========================== */
    $required_fields = [
        'company' => '会社名',
        'name'    => 'ご担当者名',
        'tel'     => '電話番号',
        'email'   => 'E-mail',
        'msg'     => 'ご用件',
        'check'   => 'プライバシーポリシーに同意する'
    ];
    
    foreach ($required_fields as $key => $label) {
        $val = $_POST[$key] ?? '';
        $val_trimmed = trim(str_replace('　', ' ', $val));
        
        if ($val_trimmed === '') {
            $errors[] = "「{$label}」が入力されていません。";
        }
    }

    /* ===========================
       2. 各項目の形式チェック
    =========================== */
    $tel_pattern = '/^0[0-9]{1,4}-?[0-9]{1,4}-?[0-9]{3,4}$/';

    if (!empty($_POST['tel'])) {
        if (!preg_match($tel_pattern, $_POST['tel'])) {
            $errors[] = "「電話番号」の形式が正しくありません。（例：078-231-1551 または ハイフンなし）";
        }
    }

    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "「emailアドレス」の形式が正しくありません。";
    }

    if( !empty($errors) ){
        $url = './?' . http_build_query($errors);
        header("Location: $url");
        exit;
    }

    /* ===========================
       設定
    =========================== */
    mb_language("uni");
    mb_internal_encoding("UTF-8");
    $today = date("Y/m/d H:i:s");

    $mail_title = "株式会社atGunGun";
    $mailto     = "info@at-gungun.com";
    $site_url_mail = 'info@at-gungun.com'; 
    $efrom = "-f info@at-gungun.com";

    /* ===========================
       管理者宛メール
    =========================== */
    $subject = "ホームページの『お問い合わせ』より送信";
    $encoded_subject = mb_encode_mimeheader($subject, 'UTF-8');
    $fromName = mb_encode_mimeheader($mail_title, "UTF-8");

    $boundary = "__BOUNDARY__" . md5(uniqid(rand(), true)) . "__";

    $header = "From: {$fromName} <{$mailto}>\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // 構文エラーを防ぐため、「"（ダブルクォーテーション）」で囲む形式に変更（編集のしやすさは同じです）
    $message = "
{$mail_title} ホームページの『お問い合わせ』よりメールがありました。

送信日時：{$today}

＜送信内容＞
会社名 : {$_POST['company']}
ご担当者名 : {$_POST['name']}
ご担当者ふりがな : {$_POST['kana']}
部署名 : {$_POST['job']}
電話番号 : {$_POST['tel']}
E-mail : {$_POST['email']}
URL : {$_POST['url']}
お問い合わせ詳細内容 : {$_POST['msg']}
";

    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode(trim($message))) . "\r\n";
    $body .= "--{$boundary}--\r\n";

    mail($mailto, $encoded_subject, $body, $header, $efrom);

    /* ===========================
       ユーザー宛メール
    =========================== */
    $mailto_guest = $_POST['email'];
    $subject2 = "【確認メール】お問い合わせありがとうございます。";

    $message2 = "
{$_POST['name']} 様
この度はお問い合わせ頂きまして、誠にありがとうございます。

内容を確認のうえ、担当より3営業日以内にご連絡をさせて頂きます。

このメールは、メールサーバーより自動送信しています。

送信日時：{$today}

----------------------------------------

＜お問い合わせ内容＞

会社名 : {$_POST['company']}
ご担当者名 : {$_POST['name']}
ご担当者ふりがな : {$_POST['kana']}
部署名 : {$_POST['job']}
電話番号 : {$_POST['tel']}
E-mail : {$_POST['email']}
URL : {$_POST['url']}
お問い合わせ詳細内容 : {$_POST['msg']}

----------------------------------------

URL：{$site_url_mail}
";

    $header2 = "From: {$fromName} <{$mailto}>\r\n";

    mb_send_mail($mailto_guest, $subject2, trim($message2), $header2, $efrom);

    header("Location: ./thanks.php");
    exit;

} else {
    header("Location: ./");
    exit;
}