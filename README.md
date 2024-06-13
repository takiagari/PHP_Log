# PHP_Log
個人サイトで過去に独自ロギングとチェックをしていたので、当時のコードを一部抜粋して公開します。

# 機能
- アクセス日時,リファラURLのシンプルなロギングファイルを作成
- ロギングを無視する対象はlog.phpの中に配列で定義
- ログファイルをチェックするページ（ログビューワー）あり
- ログビューワーは簡単なレスポンシブ対応
- ログビューワーでは重複するリファラログを除外して表示可能

# 主要なファイル内容
- log/referer/referrer_log.csv - リファラログファイル
- log.js - ログビューワーでのページネーションなどフロント機能処理
- log.php - リファラログファイルに書き込む処理を担当
- log_viewer.php - ログビューワー
- style.css - ログビューワーのCSS

## その他必要なもの
ログビューワーは独自の認証機能と併用しました。  
こちらに公開する際に認証部分を削除しています。

## 使用技術
- PHP
- HTML
- CSS
- JavaScript