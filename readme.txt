=== PChome 新聞台搬家匯入工具 ===
Contributors: mxp
Donate link: https://mxp.tw/lw
Tags: Mxp.TW, PChome2WP, 搬家, 中文, PChome, 匯入工具, 轉發, 網家, 新聞台
Requires at least: 4.7
Requires PHP: 5.4
Tested up to: 6.2
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

提供 PChome 新聞台的夥伴一個優質搬家匯入 WordPress 的工具！


== Description ==

× 感謝[Alice's Dream羊毛氈手作坊](https://woolwoolfelt.com/)贊助外掛圖像設計！

特色(Features):

1. 完整匯入新聞台文章、影像照片檔案、文章標籤（[進階授權專屬](https://mxp.tw/oI)）、留言（[進階授權專屬](https://mxp.tw/oI)）、版主回覆（[進階授權專屬](https://mxp.tw/oI)）
2. 匯入 WordPress 時彈性設定對應新分類
3. 可指定匯入文章
4. SEO 友善規劃（需配合作者架構完成轉移）
5. 內文HTML標籤整理，彈性配合各種主題
6. 自己來就很行，幾乎不需要問人了ＸＤ

需求(Requirements):

1. WordPress 4.7 以上
2. Linux 伺服器環境（作者尚未在Windows環境下測試過）
3. PHP 5.4.0 以上（建議 PHP 7）


== Installation ==

* 一般

> 進入網站後台，「外掛」->「安裝外掛」搜尋此外掛名稱

* 進階

1. 上傳外掛至外掛目錄 `wp-content/plugins/` 下。 Upload the plugin files to the `/wp-content/plugins/` directory
2. 於後台外掛功能處啟動外掛。 Activate the plugin through the 'Plugins' screen in WordPress
3. 啟用後在後台選單可找到「PChome 文章匯入工具」進行參數調整。 Use the 「PChome 文章匯入工具」 screen to configure the plugin
4. 完成。 Done


== Screenshots ==

1. **PChome 搬家工具設定頁（一般使用）** - 在此頁面輸入以及設定相關資料，準備開始搬家。

2. **PChome 搬家工具設定頁（授權進階）** - 在此頁面輸入以及設定相關資料，準備開始搬家。

3. **分類匯入工具頁** - 輸入新聞台分類資訊後開始操作匯入第一階段。

4. **文章匯入頁面** - 選擇全部批次匯入或個別匯入文章的方式。 


== Frequently Asked Questions ==

= 如何找到新聞台分類編號 =

進入新聞台首頁後右側邊欄的「文章分類」即是目前新聞台分類，複製分類連結後即可觀察

`http://mypaper.pchome.com.tw/你的新聞台帳號/category/分類數字編號`

= 正確的匯入轉移網站流程 =

1. 確定好你要的網域名稱
2. 找一個穩定的主機安裝好最新版的 WordPress
3. 後台外掛搜尋 `PChome2WP` 安裝此外掛
4. 更新「固定網址」為 `文章名稱`
5. 開啟外掛「文章匯入設定」頁面，輸入新聞台名稱以及編號後，點擊更新資訊儲存
6. 至「文章匯入工具」頁按照畫面說明開始從新聞台分類對應匯入回 WordPress 分類吧！
7. 或者，你可以考慮[聯絡作者](https://www.mxp.tw/contact/)付費協助。

= 碰到問題怎回報？ =

可以透過粉絲頁、網站或是個人臉書找到我。

臉書：[點此](https://www.facebook.com/mxp.tw)

粉絲頁： [點此](https://www.facebook.com/a.tech.guy)

網站：[聯絡我](https://www.mxp.tw/contact/)

== Changelog ==

= 1.0.7 =

* 更新支援的版本

= 1.0.6 =
* 修正付費使用者若無輸入標籤，該功能失靈問題

= 1.0.5 =
* 移除 `<div>` 與 `</div>` 標籤，避免結構不正確

= 1.0.4 =
* 修正命名

= 1.0.3 =
* 上線後更新版本測試
* 修正外掛名稱以及描述
* 測試語言標籤

= 1.0.2 =
* 準備好要發佈惹～

= 1.0.1 =
* 2017.11.02 修正回報項目

= 1.0.0 =
* 2017.10.26
* 提交

更早版本略- -

== Upgrade Notice ==

無
