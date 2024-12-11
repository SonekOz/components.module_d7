<?php
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php");
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php");

	IncludeModuleLangFile(__FILE__);

	$POST_RIGHT = $APPLICATION->GetGroupRight("sonek.rtip2");

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
		[
			'DIV' => 'edit2',
			'TAB' => GetMessage('rub_tab_generation'),
			'ICON' => 'main_user_edit',
			'TITLE' => GetMessage('rub_tab_generation_title')
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
			'ACTIVE' => ($ACTIVE <> 'Y'? 'N':'Y'),
			'NAME' => $NAME,
			'SORT' => $SORT,
			'DESCRIPTION' => $DESCRIPTION,
			'LID' => $LID,
			'AUTO' => ($AUTO <> 'Y'? 'N':'Y'),
			'DAYS_OF_MONTH' => $DAYS_OF_MONTH,
			'DAYS_OF_WEEK' => (is_array($DAYS_OF_WEEK)?implode(',', $DAYS_OF_WEEK):''),
			'TIMES_OF_DAY' => $TIMES_OF_DAY,
			'TEMPLATE' => $TEMPLATE,
			'VISIBLE' => ($VISIBLE <> 'Y'? 'N':'Y'),
			'FROM_FIELD' => $FROM_FIELD,
			'LAST_EXECUTED' => $LAST_EXECUTED
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
				LocalRedirect("/bitrix/admin/sonek.rtip2_rubric_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			else
				LocalRedirect("/bitrix/admin/sonek.rtip2_rubric_admin.php?lang=".LANG);
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
	$str_ACTIVE = 'Y';
	$str_AUTO = 'N';
	$str_DAYS_OF_MONTH = '';
	$str_DAYS_OF_WEEK = '';
	$str_TIMES_OF_DAY = '';
	$str_VISIBLE = 'Y';
	$str_LAST_EXECUTED = ConvertTimeStamp(false, "FULL");
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
			'LINK' => 'sonek.rtip2_rubric_admin.php?lang='.LANG,
			'ICON' => 'btn_list',
		]
	];
	if($ID>0)
	{
		$aMenu[] = ['SEPARATOR' => 'Y'];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_add'),
			'TITLE' => GetMessage('rub_mnu_add'),
			'LINK' => 'sonek.rtip2_rubric_edit.php?lang='.LANG,
			'ICON' => 'btn_new',
		];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_delete'),
			'TITLE' => GetMessage('rub_mnu_del'),
			'LINK' => 'javascript:if(confirm("'.GetMessage('rubric_mnu_del_conf').'"))window.location="sonek.rtip2_rubric_edit.php?ID='.$ID.'&action=delete&lang='.LANG,
			'ICON' => 'btn_delete',
		];
		$aMenu[] = ['SEPARATOR' => 'Y'];
		$aMenu[] = [
			'TEXT' => GetMessage('rub_check'),
			'TITLE' => GetMessage('rub_mnu_check'),
			'LINK' => 'template_test.php?lang='.LANG.'&ID='.$ID,
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
			<td width="40%"><?=GetMessage('rub_act')?></td>
			<td width="60%">
				<input type="checkbox" name="ACTIVE" value="Y"<?php if ($str_ACTIVE === 'Y') echo " checked"?>>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_visible')?></td>
			<td>
				<input type="checkbox" name="VISIBLE" value="Y"<?php if ($str_VISIBLE === 'Y') echo " checked"?>>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_site')?></td>
			<td>
				<?=CLang::SelectBox('LID', $str_LID)?>
			</td>
		</tr>
		<tr>
			<td><span class="required">*</span><?=GetMessage('rub_name')?></td>
			<td>
				<input type="text" name="NAME" value="<?=$str_NAME?>" size="30" maxlength="100">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_sort')?></td>
			<td>
				<input type="text" name="SORT" value="<?=$str_SORT?>" size="30">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_desc')?></td>
			<td>
				<textarea class="typearea" name="description" cols="45" rows="5" wrap="VIRTUAL"><?=$str_DESCRIPTION?></textarea>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_auto')?></td>
			<td>
				<input type="checkbox" name="AUTO" value="Y" <?php if($str_AUTO === 'Y') echo " checked"?> onclick="if(this.checked) tabControl.EnableTab('edit2'); else tabControl.DisableTab('edit2');">
			</td>
		</tr>
		<?php
			/** ********************************************************************* **/
			/** ********************************************************************* **/
			/**      ВТОРАЯ ЗАКЛАДКА - параметры автоматической генерации рассылки    **/
			/** ********************************************************************* **/
			$tabControl->BeginNextTab();
		?>
		<tr class = 'heading'>
			<td colspan="2"><?=GetMessage('rub_schedule')?></td>
		</tr>
		<tr>
			<td width="40%"><span class="required">*</span><?=GetMessage('rub_last_executed')." (".FORMAT_DATETIME."):"?></td>
			<td width="60%"><?=CalendarDate('LAST_EXECUTED', $str_LAST_EXECUTED, 'post_form', '20')?></td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_dom')?></td>
			<td>
				<input class="typeinput" type="text" name="DAYS_OF_MONTH" value="<?=$str_DAYS_OF_MONTH?>" size="30" maxlength="100">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage('rub_dow')?></td>
			<td>
				<table cellspacing="1" cellpadding="0" border="0" class="internal">
					<?php
					$arDoW = [
						'1' => GetMessage('rubric_mon'),
						'2' => GetMessage('rubric_tue'),
						'3' => GetMessage('rubric_wed'),
						'4' => GetMessage('rubric_thu'),
						'5' => GetMessage('rubric_fri'),
						'6' => GetMessage('rubric_sat'),
						'7' => GetMessage('rubric_sun'),
					];
					?>
					<tr class="heading">
						<?php
							foreach ($arDoW as $strVal => $strDoW)
							{
						?>
						<td><?=$strDoW?></td>
						<?php }?>
					</tr>
					<tr>
						<?php
							foreach ($arDoW as $strVal => $strDoW)
							{
							?>
								<td><input type="checkbox" name="DAYS_OF_WEEK[]" value="<?=$strVal?>" <?php if(array_search($strVal, $DAYS_OF_WEEK) !== false) echo " checked"?>></td>
							<?php }?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<span class="required">*</span><?=GetMessage('rub_tod')?>
			</td>
			<td>
				<input type="text" name="TIMES_OF_DAY" value="<?=$str_TIMES_OF_DAY?>" size="30" maxlength="255">
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('rub_template')?></td>
		</tr>
		<?php
			$arTemplates = CPostingTemplate::GetList();
			if(count($arTemplates) > 0){
		?>
		<tr>
			<td><span class="required">*</span><?=GetMessage('rub_templates')?></td>
			<td>
				<table>
					<?php
						$i = 0;
						foreach ($arTemplates as $arTemplate) {
							$arTemplate = CPostingTemplate::GetByID($strTemplate);
						?>
							<tr>
								<!-- тут ещё был код, но его не видно на видосе /** -->
								<td valign="top"><input type="radio" id="TEMPLATE<?=$i?>" value="<?=$arTemplate['PATH']?>"></td>
								<td>
									<label for="TEMPLATE<?=$i?>" title="<?=$arTemplate['DESCRIPTION']?>"><?=(strlen($arTemplate['NAME'])>0?$arTemplate['NAME']:GetMessage('rub_no_ist'))?></label>
									<?=$arTemplate['PATH']?>
								</td>
								<!-- **/ -->
								<?$i++?>
							</tr>
						<?php }?>
				</table>
			</td>
		</tr>
		<?php } else {?>
				<tr>
					<td colspan="2"><?=GetMessage('rub_no_templates')?></td>
				</tr>
		<?php }?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage('rub_post_fields')?></td>
		</tr>
		<tr>
			<td><span class="required">*</span><?=GetMessage('rub_post_fields_from')?></td>
			<td><input type="text" name="FROM_FIELD" value="<?=$str_FROM_FIELD?>" size="30" maxlength="255"></td>
		</tr>
		<?php
			/** завершение формы - вывод кнопок сохранений изменений **/
			$tabControl->Buttons(
				[
					'disabled' => ($POST_RIGHT<'W'),
					'back_url' => 'sonek.rtip2_rubric_admin.php?lang='.LANG,
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
		<script language="JavaScript">
			if(document.post_form.AUTO.checked)
                tabControl.EnableTab('edit2');
            else
                tabControl.DisableTab('edit2');
		</script>
		<?=BeginNote();?>
		<span class="required">*</span><?=GetMessage('REQUIRED_FIELDS')?>
		<?=EndNote();?>
	</form>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
