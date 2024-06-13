<?php
/**
 * リファラ情報をログファイルに記録する。
 * 
 * 訪問者が別のドメインから来た場合にそのリファラ情報を記録する。
 * localhostや特定のドメインからのアクセスは無視。
 * ログは'log/referer/'ディレクトリに保存され、ファイルサイズが1MBを超えた場合、
 * 新しいログファイルに切り替える。
 *
 * @return void この関数は値を返しません。
 *
 * @note ファイルの書き込みに失敗した場合、エラーは表示されず、静かに失敗します（エラーをサイレントに処理）。
 * @note __DIR__ を使用して相対パスを指定することで、スクリプトの配置場所に依存しないパス指定を実現しています。
 * @note 記録終了日は関数内で固定値として設定されており、この日を過ぎるとログの記録は停止します。
 */
function logReferrer() {
    // ロギング除外ドメインリスト
    $ignoreDomains = [
        'my-domain-name.XXXX', // 自分のサイトドメイン
        'localhost', // 開発環境
        'www.bing.com', // bing検索
        'www.google.com', // google検索
    ];
    // 特定のディレクトリやページを除外するリストをフルパスで指定
    $ignorePaths = [
        'https://github.com/takiagari', // Github
    ];

    // '/log/referer/' ディレクトリ内にログファイルを保存
    $baseFileName = __DIR__ . '/log/referer/referrer_log.csv'; // log.phpから見た絶対パス
    $maxFileSize = 1000000; // 1MB
    $endDate = strtotime('2024-09-30'); // 記録終了日

    if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
        // リファラが存在しない場合は早期リターン
        return;
    }

    $referrer = $_SERVER['HTTP_REFERER'];
    // パースしたURLのホスト名
    $referrerHost = parse_url($referrer, PHP_URL_HOST);
    $referrerPath = parse_url($referrer, PHP_URL_HOST) . parse_url($referrer, PHP_URL_PATH);

    // ドメイン全体をチェック
    if (in_array($referrerHost, $ignoreDomains)) {
        return;
    }

    // 特定のディレクトリやページをチェック
    foreach ($ignorePaths as $path) {
        if (strpos($referrerPath, $path) === 0) {
            // 除外するパスが見つかった場合は処理をスキップ
            return;
        }
    }
    
    // 現在の日付が記録終了日以前かどうかをチェック
    if (time() > $endDate) {
        // 記録終了日を過ぎている場合は処理を終了
        return;
    }

    $date = date('Y-m-d H:i:s');

    // データを一行の文字列としてフォーマット
    $logEntry = $date . ',' . $referrer . "\n";

    // ファイルが最大サイズに達しているかチェック
    if (file_exists($baseFileName) && filesize($baseFileName) >= $maxFileSize) {
        // 現在の日時を含む新しいファイル名
        $newFileName = 'referrer_log_' . date('Y-m-d_H-i-s') . '.csv';
        // 現在のログファイルをリネーム
        rename($baseFileName, __DIR__ . 'log/referer/' . $newFileName);
    }

    // 新しいログファイルまたは現在のログファイルに追記（エラーをサイレントに処理）
    @file_put_contents($baseFileName, $logEntry, FILE_APPEND);
}

// ログ記録関数の呼び出し
logReferrer();

?>