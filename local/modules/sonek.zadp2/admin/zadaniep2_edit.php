<?php
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php");
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php");

	IncludeModuleLangFile(__FILE__);

	$POST_RIGHT = $APPLICATION->GetGroupRight("sonek.zadp2");

	if ($POST_RIGHT === "D")
		$APPLICATION->AuthForm("ACCESS_DENIED");
?>

<?php
	/** здесь будет вся серверная обработка и подготовка данных **/

	/** сформируем список закладок **/
	$aTabs = [
		[
			'DIV' => 'edit1',
			'TAB' => GetMessage('rub_tab_rubric'),
			'ICON' => 'main_user_edit',
			'TITLE' => GetMessage('rub_tab_rubric_title')
		],
	];
	$tabControl = new CAdminTabControl('tabControl', $aTabs);

	/** идентификатор редактируемой записи **/
	$ID = intval($ID);

	/** сообщение об ошибке **/
	$message = null;

	/** флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из бд **/
	$bVarsFromForm = false;

	/** ********************************************************************* **/
	/**                       ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                       **/
	/** ********************************************************************* **/
	if (
		$REQUEST_METHOD === "POST" /** проверка метода вызова страницы **/
		and
		($save!='' or $apply!='') /** проверка нажатия кнопок сохранить и применить **/
		and
		$POST_RIGHT === "W" /** проверка наличия прав на запись для модуля **/
		and
		check_bitrix_sessid() /** проверка идентификатора сессии **/
	)
	{
		$rubric = new CRubric();

		/** обработка данных формы **/
		$arFields = [
            'ID' => $ID,
			'NAME' => $NAME,
			'TIMESTAMP_X' => $TIMESTAMP_X,
			"LID"    => $LID,
		];

		if($ID > 0)
		{
			$res = $rubric->Update($ID, $arFields);
		}
		else
		{
			$ID = $rubric->Add($arFields);
			$res = ($ID > 0);
		}

		if($res)
		{
			if($apply != '')
				LocalRedirect("/bitrix/admin/sonek.zadp2_zadaniep2_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			else
				LocalRedirect("/bitrix/admin/sonek.zadp2_zadaniep2_admin.php?lang=".LANG);
		}
		else
		{
			if($e = $APPLICATION->GetException())
				$message = new CAdminMessage(GetMessage('rub_save_error'), $e);
			$bVarsFromForm = true;
		}
	}
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                   ВЫБОРКА И ПОДГОТОВКА ДАННЫХ ФОРМЫ                   **/
	/** ********************************************************************* **/

	/** значения по умолчанию **/
	$str_SORT = 100;
	$str_TIME_STAMP_X = ConvertTimeStamp(false, "FULL");
	$str_FROM_FIELD = COption::GetOptionString('subscribe', 'default_from');

	/** выборка данных **/
	if($ID > 0)
	{
		$rubric = CRubric::GetByID($ID);
		if(!$rubric->ExtractFields('str_'))
			$ID = 0;
	}

	if($ID > 0 && !$message)
		$DAYS_OF_WEEK = explode(',', $str_DAYS_OF_WEEK);
	if(!is_array($DAYS_OF_WEEK))
		$DAYS_OF_WEEK = [];

	if($bVarsFromForm)
		$DB->InitTableVarsForEdit('b_list_rubric', '', 'str_');

	$APPLICATION->SetTitle(($ID > 0)? GetMessage('rub_title_edit').$ID:GetMessage('rub_title_add'));
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                              ВЫВОД ФОРМЫ                              **/
	/** ********************************************************************* **/
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); /** второй общий пролог **/

	/** конфигурация административного меню **/
	$aMenu = [
		[
			'TEXT' => GetMessage('rub_list'),
			'TITLE' => GetMessage('rub_list_title'),
			'LINK' => 'sonek.zadp2_zadaniep2_admin.php?lang='.LANG,
			'ICON' => 'btn_list',
		]
	];
	if($ID>0)
	{
		$aMenu[] = ['SEPARATOR' => 'Y'];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_add'),
			'TITLE' => GetMessage('rub_mnu_add'),
			'LINK' => 'sonek.zadp2_zadaniep2_edit.php?lang='.LANG,
			'ICON' => 'btn_new',
		];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_delete'),
			'TITLE' => GetMessage('rub_mnu_del'),
			'LINK' => 'javascript:if(confirm("'.GetMessage('rubric_mnu_del_conf').'"))window.location="sonek.zadp2_zadaniep2_edit.php?ID='.$ID.'&action=delete&lang='.LANG,
			'ICON' => 'btn_delete',
		];
		$aMenu[] = ['SEPARATOR' => 'Y'];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_check'),
			'TITLE' => GetMessage('rub_mnu_check'),
			'LINK' => 'sonek.zadp2_zadaniep2_admin.php?lang='.LANG,
		];
	}

	/** создание экземпляра класса административного меню **/
	$context = new CAdminContextMenu($aMenu);

	/** вывод административного меню **/
	$context->Show();

	if($_REQUEST['mess'] === 'ok' && $ID > 0)
		CAdminMessage::ShowMessage(['MESSAGE' => GetMessage('rub_saved'), 'TYPE' => 'OK']);

	if($message)
		echo $message->Show();
	elseif($rubric->LAST_ERROR!='')
		CAdminMessage::ShowMessage($rubric->LAST_ERROR);
	/** ********************************************************************* **/
?>
<?php
	/** здесь будет вывод страницы с формой **/
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
	<?=bitrix_sessid_post();?>
	<?php $tabControl->Begin();?>
	<?php
		/** ********************************************************************* **/
		/**                            ПЕРВАЯ ЗАКЛАДКА                            **/
		/** ********************************************************************* **/
	?>
	<?php $tabControl->BeginNextTab();?>
    <tr>
        <?php if ($str_ID) {?>
        <td><span class="required">*</span><?=GetMessage('rub_id')?>:</td>
        <td>
	        <?=$str_ID?>
        </td>
        <?php }?>
    </tr>
    <tr hidden="">
        <td><?echo GetMessage("rub_site")?></td>
        <td><?echo CLang::SelectBox("LID", $str_LID);?></td>
    </tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage('rub_name')?>:</td>
		<td>
			<input type="text" name="NAME" value="<?=$str_NAME?>" size="30" maxlength="100">
		</td>
	</tr>
    <tr>
        <td width="40%"><span class="required">*</span><?=GetMessage('rub_last_timestamp_x')?>:</td>
        <td width="60%"><?=CalendarDate('LAST_TIMESTAMP_X', $str_TIME_STAMP_X, 'post_form', '20')?></td>
    </tr>
	<?php
		/** завершение формы - вывод кнопок сохранений изменений **/
		$tabControl->Buttons(
			[
				'disabled' => ($POST_RIGHT<'W'),
				'back_url' => 'sonek.zadp2_zadaniep2_admin.php?lang='.LANG,
			]
		);
	?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<?php
		if ($ID > 0 and !$bCopy)
		{
			?>
			<input type="hidden" name="ID" value="<?=$ID?>">
		<?php }?>
	<?php
		/** завершаем интерфейс закладок **/
		$tabControl->End();

		/** дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка **/
		$tabControl->ShowWarnings('post_form', $message);

		/** дополнительно: динамическая блокировка закладки, если требуется **/
	?>
	<?=BeginNote();?>
	<span class="required">*</span><?=GetMessage('REQUIRED_FIELDS')?>
	<?=EndNote();?>
</form>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
