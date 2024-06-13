<?php

// 認証機能はこの上で呼び出す


// 重複URLを最新のものだけに絞るかどうかの判定
$filterUnique = isset($_GET['filter_unique']) && $_GET['filter_unique'] === '1';

// ログファイルのパス
$logFile = __DIR__ . '/log/referer/referrer_log.csv';

$logs = [];
if (file_exists($logFile)) {
    $file = fopen($logFile, 'r');
    while (($line = fgetcsv($file)) !== FALSE) {
        $logs[] = $line;
    }
    fclose($file);
    // 新しい順に並べ替え
    $logs = array_reverse($logs);

    if ($filterUnique) {
        // 重複を除いた最新のログを取得
        $logs = getUniqueLogs($logs);
    }
} else {
    $logs[] = ["ログファイルが見つかりません。", ""];
}

/**
 * 重複を除いた最新のログを取得する
 * 
 * @param array $logs ログの配列。各ログは配列で、0番目の要素がタイムスタンプ、1番目の要素がURL
 * @return array 重複するURLの中で最新のログのみを含む配列
 */
function getUniqueLogs($logs) {
    // 結果を格納する配列
    $uniqueLogs = [];
    // URLごとの最新タイムスタンプを追跡する配列
    $urls = [];
    
    // ログの配列をループする
    foreach ($logs as $log) {
        // 現在のログのURLを取得
        $url = $log[1];
        // 現在のログのタイムスタンプを取得
        $timestamp = strtotime($log[0]);
        // URLがまだ登録されていないか、現在のタイムスタンプが既存のタイムスタンプより新しい場合
        if (!isset($urls[$url]) || $urls[$url]['timestamp'] < $timestamp) {
            // URLに関連付けられたログとタイムスタンプを更新
            $urls[$url] = [
                'log' => $log,
                'timestamp' => $timestamp
            ];
        }
    }
    
    // 最新のログのみを返す
    return array_column($urls, 'log');
}

/**
 * 指定された日時をフォーマットする
 * 
 * @param string $dateTime フォーマットする日時を表す文字列
 * @return string フォーマットされた日付と時間を含むHTML文字列
 */
function formatDate($dateTime) {
    // 指定日付でDateTimeオブくジェクト作成
    $date = new DateTime($dateTime);
    // フォーマットされた日付と時間をタグで囲んで返す
    return $date->format('Y-m-d') . ' <span class="time">' . $date->format('H:i') . '</span>';
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Log Viewer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <h2>Log Viewer</h2>
  <div class="toggle-container">
    <label class="toggle-label" for="toggleUnique">重複URLは最新のみ</label>
    <input type="checkbox" id="toggleUnique" <?php if ($filterUnique) echo 'checked'; ?>>
  </div>
  <div class="page-info" id="pageInfoTop"></div>
  <div class="pagination" id="paginationTop"></div>
  <table id="logTable">
    <thead>
      <tr>
        <th>#</th>
        <th>日付</th>
        <th>URL</th>
      </tr>
    </thead>
    <tbody id="logTableBody">
      <?php foreach ($logs as $index => $log): ?>
        <tr>
          <td data-label="#"><?php echo $index + 1; ?></td>
          <td data-label="日付"><?php echo formatDate(htmlspecialchars($log[0])); ?></td>
          <td data-label="URL">
            <div class="url-container">
              <input type="text" value="<?php echo htmlspecialchars($log[1]); ?>" readonly>
              <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="pagination" id="paginationBottom"></div>
  <div class="page-info" id="pageInfoBottom"></div>
  <script src="log.js"></script>
</body>
</html>
