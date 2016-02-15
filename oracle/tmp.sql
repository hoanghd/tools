create or replace PROCEDURE Z_<?php echo $name;?>(
							 varSCHEMA 			VARCHAR2
							,numSUBSYSTEM		NUMBER
							)
IS

	/*-- Z_移行設定テーブル カーソル定義 --*/
	CURSOR curSETTEI(WK_SUBSYSTEM NUMBER) IS
	SELECT
		 サブシステムID
		,設定作成日
		,設定更新日
		,個別設定01
		,個別設定02
		,個別設定03
		,個別設定04
		,個別設定05
		,個別設定06
		,個別設定07
		,個別設定08
		,個別設定09
		,個別設定10
		,個別設定11
		,個別設定12
		,個別設定13
		,個別設定14
		,個別設定15
		,個別設定16
		,個別設定17
		,個別設定18
		,個別設定19
		,個別設定20
	FROM
	    Z_移行設定テーブル
	WHERE
		サブシステムID = WK_SUBSYSTEM;

	/*-- <?php echo $srcTable['name'];?> --*/
	CURSOR curSRC IS
	SELECT 
		<?php echo join(",\n		", $srcTable['select']);?> 
	FROM
		<?php echo $srcTable['name'];?> 
	ORDER BY
		<?php echo join(",\n		", $srcTable['order']);?>; 

	/*-- フェッチ用 --*/
	fetSETTEI						curSETTEI%ROWTYPE;
	fetSRC						curSRC%ROWTYPE;

	/*-- 共通ワーク用 --*/
	varSQL							VARCHAR2(10000);			-- ＳＱＬ文
	intIN_SQL						INTEGER;				-- 登録カーソルＩＤ
	intIN_SQL_Prs					INTEGER := NULL;		-- 登録新プロセス
	intBAICnt						PLS_INTEGER := 10000;	-- 統計収集用件数
	intLoopCnt						PLS_INTEGER := 0;		-- 読込件数
	intLoopCnt1						PLS_INTEGER := 0;		-- 統計収集用登録件数
	intLoopCnt2						PLS_INTEGER := 0;		-- 登録件数
	intLoopCntErr					PLS_INTEGER := 0;		-- エラー件数
	intLoopCntGai					PLS_INTEGER := 0;		-- 対象外件数
	intLoopCntRep					PLS_INTEGER := 0;		-- 置換件数
	intLoopCntRep_OK				PLS_INTEGER := 0;		-- 置換件数（確認済み）

	varエラー内容						VARCHAR2(800);			-- エラー内容
	intCOMCnt						PLS_INTEGER := 1;		-- コミット用件数
	intLoopCommit					PLS_INTEGER := 0;		-- コミット件数
	intErrorFLG						PLS_INTEGER := 0;		-- エラーフラグ
	intLEN							PLS_INTEGER;			-- 検出位置
	varSTATUS						VARCHAR2(1);			-- 変換ファンクション用
	varDUMMY						VARCHAR2(7);			-- ダミー
	intHeadout						PLS_INTEGER := 0;		-- ログヘッダー出力
	-- 設定テーブル用
	var設定作成日					VARCHAR2(20);			-- 設定作成日
	var設定更新日					VARCHAR2(20);			-- 設定更新日
	varテーブルクリア有無				VARCHAR2(100);			-- 個別設定01（0：クリア無、1：クリア有）

	
	--移送先項目
<?php 
	foreach($vars as $row){
		$var = (($row[1] == 'NUMBER') ? 'num' : 'var') . $row[0] . '                  	' . $row[1];
		if(!empty($row[2])){
			$var .= "({$row[2]})";
		}
		
		if(!empty($row[3])){
			$var .= ":={$row[3]}";
		}
		
		echo "	{$var};\n";
	}
?>
 
	--ファンクションダミー
	varダミー							VARCHAR2(100);
BEGIN

	DBMS_OUTPUT.ENABLE(1000000);

	--***********************************************
	-- テーブル登録開始ログ  						*
	--***********************************************
	-- 処理開始
	DBMS_OUTPUT.PUT_LINE('<?php echo $name;?>　　開始');

	--***********************************************
	--* Z_移行設定テーブル							*
	--***********************************************
	-- 移行設定テーブルから作成日を取得する
	OPEN curSETTEI(numSUBSYSTEM);
		FETCH curSETTEI INTO fetSETTEI;
		IF curSETTEI%FOUND THEN
			-- 設定値NULLの場合は初期値を設定
			IF  fetSETTEI.設定作成日 IS NULL THEN
				fetSETTEI.設定作成日 := '2016/05/01';
			END IF;
			IF  fetSETTEI.設定更新日 IS NULL THEN
				fetSETTEI.設定更新日 := '2016/05/01';
			END IF;
			-- 作成日を取得
			var設定作成日 := TO_CHAR(fetSETTEI.設定作成日,'yyyy/mm/dd HH24:MI:SS');
			var設定更新日 := TO_CHAR(fetSETTEI.設定更新日,'yyyy/mm/dd HH24:MI:SS');
			CASE fetSETTEI.個別設定01
				WHEN '0' THEN						-- クリア無
					varテーブルクリア有無 := '0';
				WHEN '1' THEN						-- クリア有
					varテーブルクリア有無 := '1';
				ELSE								-- 設定が不正な場合は初期値の'1'をセット
					varテーブルクリア有無 := '1';
			END CASE;
		ELSE
			--作成日が存在しない（デフォルト値とする）
			var設定作成日 := '2016/05/01';
			var設定更新日 := '2016/05/01';
			varテーブルクリア有無 := '1';
		END IF;
	CLOSE curSETTEI;

	Z_LINK_CREATE('REMOTE', fetSETTEI.個別設定02, fetSETTEI.個別設定03, fetSETTEI.個別設定04);

	--***********************************************
	-- 移行先テーブルをクリア
	--***********************************************
	IF varテーブルクリア有無 = '1' THEN
		DELETE FROM <?php echo $name;?>@REMOTE;
		DELETE FROM Z_LOGテーブル WHERE テーブル名 = '<?php echo $name;?>';
	END IF;

	-- DATE型の書式が'yy-mm-dd'になっている為、登録する時にフォーマット変更
	EXECUTE IMMEDIATE 'ALTER SESSION SET NLS_DATE_FORMAT=''yyyy/mm/dd HH24:MI:SS''';

	-- 移行元データ判断
	FOR fetSRC IN curSRC LOOP
		--初期化
		varエラー内容 := '';
		intErrorFLG := 0;		
    
		--***************************
		--* エラー内容書き込み		*
		--***************************
		IF varエラー内容 IS NOT NULL THEN
			--LOG項目抽出
			varSQL := 'INSERT INTO Z_LOGテーブル(';
			varSQL := varSQL		 || '項目ID';
			varSQL := varSQL || ' ,' || 'テーブル名';
			varSQL := varSQL || ' ,' || '移行元コード1';  
			varSQL := varSQL || ' ,' || '移行元コード2';  
			varSQL := varSQL || ' ,' || '移行元コード3';  
			varSQL := varSQL || ' ,' || '移行元コード4'; 
			varSQL := varSQL || ' ,' || 'エラー内容';
			varSQL := varSQL || ' ) VALUES ( ';
			varSQL := varSQL ||		    CHR(39) || '1' || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || '<?php echo $name;?>' || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || fetSRC.管轄署 || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || fetSRC.対象物番号 || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || fetSRC.査察年月日 || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || fetSRC.レコード番号 || CHR(39);
			varSQL := varSQL || ' ,' || CHR(39) || varエラー内容 || CHR(39);
			varSQL := varSQL || ')';
			-- 登録更新カーソルID番号取得
			intIN_SQL := DBMS_SQL.Open_cursor;
			-- SQLの解析
			DBMS_SQL.Parse(intIN_SQL, varSQL, DBMS_SQL.Native);
			-- SQL実行
			intIN_SQL_Prs := DBMS_SQL.Execute(intIN_SQL);
			-- 登録更新カーソルクローズ
			DBMS_SQL.close_cursor(intIN_SQL);
		END IF;

		--***************************
		--* 移行先登録処理			*
		--***************************
		-- エラーがある場合、処理しない
		IF intErrorFLG = 0 THEN
			-- 新規レコードの作成
			varSQL := 'INSERT INTO ' || fetSETTEI.個別設定02 || '.<?php echo $name;?>(';
<?php 
	foreach($vars as $i=>$row){
		$var = (($row[1] == 'NUMBER') ? 'num' : 'var') . $row[0];
		$between = empty($i) ? '	     ' : " || ' ,'";
		echo "			varSQL := varSQL{$between} || '{$row[0]}';\n";
	}
?>
			varSQL := varSQL || ' ) VALUES ( ';
<?php 
	foreach($vars as $i=>$row){
		$var = (($row[1] == 'NUMBER') ? 'num' : 'var') . $row[0];
		$between = empty($i) ? '		    ' : " ' ,' || ";
		echo "			varSQL := varSQL ||{$between} CHR(39) || {$var} || CHR(39);\n";
	}
?>
			varSQL := varSQL || ')';
			-- 登録更新カーソルID番号取得
			intIN_SQL := DBMS_SQL.Open_cursor;
			-- SQLの解析
			DBMS_SQL.Parse(intIN_SQL, varSQL, DBMS_SQL.Native);
			-- SQL実行
			intIN_SQL_Prs := DBMS_SQL.Execute(intIN_SQL);
			-- 登録更新カーソルクローズ
			DBMS_SQL.close_cursor(intIN_SQL);			

			intLoopCnt1 := intLoopCnt1 + 1;			--統計収集用登録件数
			intLoopCnt2 := intLoopCnt2 + 1;			--登録件数
			intLoopCommit := intLoopCommit + 1;		--コミット件数

		ELSE
			intLoopCntErr := intLoopCntErr + 1;		--エラー件数
		END IF;

			intLoopCnt := intLoopCnt + 1;			--読込件数

		--コミット処理
		IF intLoopCommit >= intCOMCnt THEN
			COMMIT;
			intLoopCommit := 0;
		END IF;

		-- スキーマ統計の収集
		IF intLoopCnt1 >= intBAICnt THEN
			DBMS_STATS.GATHER_TABLE_STATS(OWNNAME=> varSCHEMA, TABNAME=> 'PB施設', PARTNAME=> NULL);
			intLoopCnt1 := 0;
		END IF;
	END LOOP;

	--***********************************************
	-- LOGファイルを閉じる
	--***********************************************
	--	UTL_FILE.FFLUSH(FNO1);
	--	UTL_FILE.FCLOSE(FNO1);

	--コミット処理
	COMMIT;

	--読込件数
		DBMS_OUTPUT.PUT_LINE('<?php echo $name;?> 読込件数=  ' || intLoopCnt || '件');
	--登録件数
		DBMS_OUTPUT.PUT_LINE('<?php echo $name;?> 登録件数=  ' || intLoopCnt2 || '件');
	--エラー件数
		DBMS_OUTPUT.PUT_LINE('<?php echo $name;?> エラー件数=  ' || intLoopCntErr || '件');
	--対象外件数
		DBMS_OUTPUT.PUT_LINE('<?php echo $name;?> 対象外件数=  ' || intLoopCntGai || '件');

	-- 処理終了
	DBMS_OUTPUT.PUT_LINE('<?php echo $name;?>　　終了');

	RETURN;


EXCEPTION
	WHEN OTHERS then
		Z_LINK_REMOVE('REMOTE');
		pDebugOutPut('SQL=' || varSQL);
		ROLLBACK;
END Z_<?php echo $name;?>;