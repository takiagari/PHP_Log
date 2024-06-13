
// 全ての行を取得
const allRows = [...document.querySelectorAll('#logTableBody tr')];
// 1ページあたりの行数
const rowsPerPage = 50;
const paginationTop = document.getElementById('paginationTop');
const paginationBottom = document.getElementById('paginationBottom');
const pageInfoTop = document.getElementById('pageInfoTop');
const pageInfoBottom = document.getElementById('pageInfoBottom');
let currentRows = allRows;
let totalPages = Math.ceil(currentRows.length / rowsPerPage);

/**
 * ページを表示する
 * 
 * 指定されたページ番号に基づいて表示行を切り替え
 * ページネーションとページ情報も更新
 * 
 * @param {number} page - 表示するページ番号
 */
const displayPage = (page) => {
  // 表示を開始する行のインデックスを計算
  const start = (page - 1) * rowsPerPage;
  // 表示を終了する行のインデックスを計算
  const end = start + rowsPerPage;

  // 現在の行の配列をループ
  currentRows.forEach((row, index) => {
    // 現在のインデックスが表示範囲内かどうかをチェック
    if (index >= start && index < end) {
      // 表示範囲内の場合、非表示を解除
      row.style.display = '';
    } else {
      // 表示範囲外の場合、行を非表示
      row.style.display = 'none';
    }

    // 各行に連番を振る
    const numberCell = row.querySelector('td[data-label="#"]');
    if (numberCell) {
      // 行番号を設定
      numberCell.textContent = start + (index + 1);
    }
  });

  // ページネーションを更新
  updatePagination(page);

  // ページ情報を更新
  updatePageInfo(start, end);
}


/**
 * ページネーションを更新する関数
 * @param {number} currentPage - 現在のページ番号
 */
const updatePagination = (currentPage) => {
  paginationTop.innerHTML = '';
  paginationBottom.innerHTML = '';
  const createButton = (text, page, disabled = false) => {
    const button = document.createElement('button');
    button.textContent = text;
    button.disabled = disabled;
    button.addEventListener('click', () => displayPage(page));
    return button;
  };
  paginationTop.appendChild(createButton('最初', 1, currentPage === 1));
  paginationBottom.appendChild(createButton('最初', 1, currentPage === 1));
  if (currentPage > 1) {
    paginationTop.appendChild(createButton('前', currentPage - 1));
    paginationBottom.appendChild(createButton('前', currentPage - 1));
  }
  paginationTop.appendChild(createButton(currentPage, currentPage, true));
  paginationBottom.appendChild(createButton(currentPage, currentPage, true));
  if (currentPage < totalPages) {
    paginationTop.appendChild(createButton('次', currentPage + 1));
    paginationBottom.appendChild(createButton('次', currentPage + 1));
  }
  paginationTop.appendChild(createButton('最後', totalPages, currentPage === totalPages));
  paginationBottom.appendChild(createButton('最後', totalPages, currentPage === totalPages));
}

/**
 * ページ情報を更新する関数
 * @param {number} start - 表示する最初の行インデックス
 * @param {number} end - 表示する最後の行インデックス
 */
const updatePageInfo = (start, end) => {
  const totalItems = currentRows.length;
  // 表示する最大値
  const endAdjusted = end > totalItems ? totalItems : end;
  const infoText = `表示中: ${start + 1} - ${endAdjusted} 件目 / 全 ${totalItems} 件`;
  pageInfoTop.textContent = infoText;
  pageInfoBottom.textContent = infoText;
}

/**
 * 重複URLを最新のものだけに絞るチェックボックスの処理
 */
const toggleUniqueLogs = () => {
  const isChecked = document.getElementById('toggleUnique').checked;
  const urlParams = new URLSearchParams(window.location.search);
  if (isChecked) {
    // 絞り込みパラメータ1
    urlParams.set('filter_unique', '1');
  } else {
    // 絞り込みパラメータ外す
    urlParams.delete('filter_unique');
  }
  // 再読み込み
  window.location.search = urlParams.toString();
}

/**
 * クリップボードにテキストをコピーする
 * 
 * ボタンの前の兄弟要素（入力フィールド）のテキストをクリップボードにコピー
 * コピーが成功すると、ボタンのテキストが一時的に「Copied!」に変更
 * 
 * @param {HTMLButtonElement} button - クリックされたボタン要素
 */
const copyToClipboard = async (button) => {
  // ボタンの前の兄弟要素（入力フィールド）を取得
  const input = button.previousElementSibling;

  try {
    // 入力フィールドのテキストをクリップボードにコピー
    await navigator.clipboard.writeText(input.value);
    
    // コピーが成功したらボタンのテキストを変更
    button.textContent = 'Copied!';

    // 2秒後にボタンのテキストを元の「Copy」に戻す
    setTimeout(() => {
        button.textContent = 'Copy';
    }, 2000);
  } catch (err) {
    // コピーに失敗した場合、エラーメッセージをコンソールに表示
    console.error('Failed to copy text: ', err);
  }
}

// 初期ページの表示
displayPage(1);