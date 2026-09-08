<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (($_POST['upflg'] ?? '') !== '1') {
    http_response_code(400);
    exit;
}

mb_language('Japanese');
mb_internal_encoding('UTF-8');

$company = trim((string) ($_POST['company'] ?? ''));
$name    = trim((string) ($_POST['name'] ?? ''));
$mail    = trim((string) ($_POST['mail'] ?? ''));
$tel     = trim((string) ($_POST['tel'] ?? ''));
$msg     = trim((string) ($_POST['msg'] ?? ''));
$check   = trim((string) ($_POST['check'] ?? ''));

/*
|--------------------------------------------------------------------------
| 入力チェック
|--------------------------------------------------------------------------
*/

if ($company === '' || $name === '' || $mail === '' || $msg === '' || $check === '') {
    http_response_code(422);
    exit;
}

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit;
}

// メールヘッダーへの改行コード混入を防止
if (preg_match('/[\r\n]/', $mail)) {
    http_response_code(422);
    exit;
}

// 異常に長い入力を拒否
if (
    mb_strlen($company) > 200 ||
    mb_strlen($name) > 100 ||
    mb_strlen($mail) > 254 ||
    mb_strlen($tel) > 50 ||
    mb_strlen($msg) > 5000
) {
    http_response_code(422);
    exit;
}

/*
|--------------------------------------------------------------------------
| 管理者宛メール
|--------------------------------------------------------------------------
*/

$today = date('Y/m/d H:i:s');

$mail_title = 'Coel';

// 最終的な送信先は後で設定
$mailto = 'support@at-gungun.co.jp';

$subject = "{$mail_title} お問い合わせフォームより送信";

$message = <<<maildata
{$mail_title} お問い合わせフォームよりメールがありました。

送信日時：{$today}

＜お問い合わせ内容＞

企業名：{$company}

お名前：{$name}

メールアドレス：{$mail}

お電話番号：{$tel}

お問い合わせ内容：
{$msg}

maildata;

$fromName = mb_encode_mimeheader($mail_title);

$header = "From: {$fromName} <support@at-gungun.co.jp>\r\n";
$header .= "Reply-To: {$mail}";

$efrom = '-fsupport@at-gungun.co.jp';

$adminSent = mb_send_mail(
    $mailto,
    $subject,
    $message,
    $header,
    $efrom
);

if (!$adminSent) {
    http_response_code(500);
    exit;
}

/*
|--------------------------------------------------------------------------
| お客様への自動返信
|--------------------------------------------------------------------------
*/

$subject_guest = "【{$mail_title}】お問い合わせありがとうございます";

$message_guest = <<<maildata
{$name} 様

お問い合わせいただきありがとうございます。

内容を確認の上、担当者よりご連絡いたします。

このメールは自動送信されています。

送信日時：{$today}

----------------------------------------------------------------------

＜お問い合わせ内容＞

企業名：{$company}

お名前：{$name}

メールアドレス：{$mail}

お電話番号：{$tel}

お問い合わせ内容：
{$msg}

----------------------------------------------------------------------

maildata;

$header_guest = "From: {$fromName} <support@at-gungun.co.jp>";

mb_send_mail(
    $mail,
    $subject_guest,
    $message_guest,
    $header_guest,
    $efrom
);

http_response_code(200);
exit;