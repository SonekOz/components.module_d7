<?php
	use Bitrix\Main\Loader;
	use Bitrix\Main\Localization\Loc;

	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php");
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php");

	IncludeModuleLangFile(__FILE__);

	$POST_RIGHT = $APPLICATION->GetGroupRight("sonek.zadp2");

	if ($POST_RIGHT === "D")
		$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
?>

<?php
	/** здесь будет вся серверная обработка и подготовка данных **/
	$sTableID = "my_list";
	$oSort = new CAdminSorting($sTableID, "ID", "desc");
	$lAdmin = new CAdminList($sTableID, $oSort);
	//$by = mb_strtoupper($lAdmin->getField());
	//$order = mb_strtoupper($lAdmin->getOrder());

	function CheckFilter()
	{
		global $FilterArr, $lAdmin;
		foreach ($FilterArr as $f) global $$f;

		/**
			здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки,
			вызываем $lAdmin->AddFilterError("текст_ошибки").
		**/

		return count($lAdmin->arFilterErrors) == 0; /** если ошибки есть, вернем false; **/
	}
	/** опишем элементы фильтра **/
	$FilterArr = Array(
		"find_id",
		"find_name",
        "find_timestamp_x"
	);
	/** инициализируем фильтр **/
	$lAdmin->InitFilter($FilterArr);
	/** если все значения фильтра корректны, обработаем его **/
	if (CheckFilter())
	{
		/** создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра **/
		$arFilter = Array(
			"ID"		=> $find_id,
			"NAME"		=> $find_name,
            "TIMESTAMP_X" => $find_timestamp_x
		);
	}

	/** сохранение отредактированных элементов **/
	if($lAdmin->EditAction() && $POST_RIGHT=="W")
	{
		/** пройдем по списку переданных элементов **/
		foreach($lAdmin->GetEditFields() as $ID=>$arFields)
		{

			/** сохраним изменения каждого элемента **/
			$DB->StartTransaction();
			$ID = IntVal($ID);
			$cData = new CRubric;
			if(($rsData = $cData->GetByID($ID)) && ($arData = $rsData->Fetch()))
			{
				foreach($arFields as $key=>$value)
					$arData[$key]=$value;
				if(!$cData->Update($ID, $arData))
				{
					$lAdmin->AddGroupError(Loc::getMessage("rub_save_error")." ".$cData->LAST_ERROR, $ID);
					$DB->Rollback();
				}
			}
			else
			{
				$lAdmin->AddGroupError(Loc::getMessage("rub_save_error")." ".Loc::getMessage("rub_no_rubric"), $ID);
				$DB->Rollback();
			}
			$DB->Commit();
		}
	}
	/** обработка одиночных и групповых действий **/
	if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
	{
		/** если выбрано "Для всех элементов" **/
		if ($lAdmin->IsGroupActionToAll())
		{
			$cData = new CRubric;
			$rsData = $cData->GetList(array($by=>$order), $arFilter);
			while($arRes = $rsData->Fetch())
				$arID[] = $arRes['ID'];
		}
		$action = $lAdmin->GetAction();
		/** пройдем по списку элементов **/
		foreach($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;
			$ID = IntVal($ID);

			/** для каждого элемента совершим требуемое действие **/
			switch($action)
			{
				// удаление
				case "delete":
					@set_time_limit(0);
					$DB->StartTransaction();
					if(!CRubric::Delete($ID))
					{
						$DB->Rollback();
						$lAdmin->AddGroupError(Loc::getMessage("rub_del_err"), $ID);
					}
					$DB->Commit();
					break;

				// активация/деактивация
				case "activate":
				case "deactivate":
					$cData = new CRubric;
					if(($rsData = $cData->GetByID($ID)) && ($arFields = $rsData->Fetch()))
					{
						$arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
						if(!$cData->Update($ID, $arFields))
							$lAdmin->AddGroupError(Loc::getMessage("rub_save_error").$cData->LAST_ERROR, $ID);
					}
					else
						$lAdmin->AddGroupError(Loc::getMessage("rub_save_error")." ".Loc::getMessage("rub_no_rubric"), $ID);
					break;
			}
		}
	}

	// выберем список рассылок
	$cData = new CRubric;
	$rsData = $cData->GetList(array($by=>$order), $arFilter);
	// преобразуем список в экземпляр класса CAdminResult
	$rsData = new CAdminResult($rsData, $sTableID);
	// аналогично CDBResult инициализируем постраничную навигацию.
	$rsData->NavStart();
	// отправим вывод переключателя страниц в основной объект $lAdmin
	$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage("rub_nav")));

	$lAdmin->AddHeaders(array(
		array(  "id"    =>"ID",
			"content"  =>"ID",
			"sort"     =>"id",
			"default"  =>true,
		),
		array(  "id"    =>"NAME",
			"content"  =>Loc::getMessage("rub_name"),
			"sort"     =>"name",
			"default"  =>true,
		),
		array(  "id"    =>"TIMESTAMP_X",
			"content"  =>Loc::getMessage("rub_timestamp"),
			"sort"     =>"timestamp_x",
			"default"  =>true,
		),
	));

	while($arRes = $rsData->NavNext(true, "f_"))
	{
		// создаем строку. результат - экземпляр класса CAdminListRow
		$row =& $lAdmin->AddRow($f_ID, $arRes);

		// далее настроим отображение значений при просмотре и редактировании списка

		// параметр NAME будет редактироваться как текст, а отображаться ссылкой
		$row->AddInputField("NAME", array("size"=>20));
		$row->AddViewField("NAME", ''.$f_NAME.'');

		// параметр SORT будет редактироваться текстом
		$row->AddCalendarField("TIMESTAMP_X");

		// сформируем контекстное меню
		$arActions = Array();
		// редактирование элемента
		$arActions[] = array(
			"ICON"=>"edit",
			"DEFAULT"=>true,
			"TEXT"=>Loc::getMessage("rub_edit"),
			"ACTION"=>$lAdmin->ActionRedirect("sonek.zadp2_zadaniep2_edit.php?ID=".$f_ID)
		);

		// удаление элемента
		if ($POST_RIGHT>="W")
			$arActions[] = array(
				"ICON"=>"delete",
				"TEXT"=>Loc::getMessage("rub_del"),
				"ACTION"=>"if(confirm('".Loc::getMessage('rub_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
			);
		// вставим разделитель
		$arActions[] = array("SEPARATOR"=>true);
		// проверка шаблона для автогенерируемых рассылок
		if (strlen($f_TEMPLATE)>0 && $f_AUTO=="Y")
			$arActions[] = array(
				"ICON"=>"",
				"TEXT"=>Loc::getMessage("rub_check"),
				"ACTION"=>$lAdmin->ActionRedirect("template_test.php?ID=".$f_ID)
			);
		// если последний элемент - разделитель, почистим мусор.
		if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
			unset($arActions[count($arActions)-1]);

		// применим контекстное меню к строке
		$row->AddActions($arActions);
	}

	$lAdmin->AddFooter(
		array(
			array("title"=>Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // кол-во элементов
			array("counter"=>true, "title"=>Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
		)
	);
	// групповые действия
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>Loc::getMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы
		"activate"=>Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"), // активировать выбранные элементы
		"deactivate"=>Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // деактивировать выбранные элементы
	));

	/** ********************************************************************* **/
	/**                         АДМИНИСТРАТИВНОЕ МЕНЮ                         **/
	/** ********************************************************************* **/
	$aContext = [
		[
			'TEXT' => 'Post Add',
			"LINK" => 'sonek.zadp2_zadaniep2_edit.php?lang='.LANG,
			'TITLE' => 'Post Add Title',
			'ICON' => 'btn_new'
		]
	];
	$lAdmin->AddAdminContextMenu($aContext);
	/** ********************************************************************* **/

	$lAdmin->CheckListMode();
	$APPLICATION->SetTitle(Loc::getMessage("rub_title"));
?>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?php
	/** здесь будет вывод страницы с формой **/
	$oFilter = new CAdminFilter(
		$sTableID."_filter",
		array(
			'find_id' => 'ID',
			'find_name' => Loc::getMessage("rub_f_name"),
		)
	);
?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?php $oFilter->Begin();?>
	<tr>
		<td><?="ID"?>:</td>
		<td>
			<input type="text" name="find_id" size="47" value="<?=htmlspecialchars($find_id)?>">
		</td>
	</tr>

	<tr>
		<td><?=Loc::getMessage("rub_f_name").":"?></td>
		<td><input type="text" name="find_name" size="47" value="<?=htmlspecialchars($find_name)?>"></td>
	</tr>
	<?php
		$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
		$oFilter->End();
	?>
</form>
<?php $lAdmin->DisplayList();?>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
