<?php
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/include.php");

	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/prolog.php");

	IncludeModuleLangFile(__FILE__);

	$POST_RIGHT = $APPLICATION->GetGroupRight("sonek.rtip2");

	if ($POST_RIGHT === "D")
		$APPLICATION->AuthForm("ACCESS_DENIED");

	$sTableID = "tbl_rubric";
	$oSort = new CAdminSorting($sTableID, "ID", "desc");
	$lAdmin = new CAdminList($sTableID, $oSort);

	/** ********************************************************************* **/
	/**                               ФИЛЬТР                                  **/
	/** ********************************************************************* **/
	/**                             CheckFilter                               **/
	function CheckFilter()
	{
		global $FilterArr, $lAdmin;
		foreach ($FilterArr as $f) global $$f;
		return count($lAdmin->arFilterErrors)==0;
	}

	$FilterArr = [
		"find",
		"find_type",
		"find_id",
		"find_lid",
		"find_active",
		"find_visible",
		"find_auto"
	];

	$lAdmin->InitFilter($FilterArr);

	if(CheckFilter())
	{
		$arFilter = [
			"ID" => ($find!="" and $find_type == "id"? $find:$find_id),
			"LID" => $find_lid,
			"ACTIVE" => $find_active,
			"VISIBLE" => $find_visible,
			"AUTO" => $find_auto,
		];
	}
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                ОБРАБОТКА ДЕЙСТВИЙ НАД ЭЛЕМЕНТАМИ СПИСКА               **/
	/** ********************************************************************* **/
	if($lAdmin->EditAction() and $POST_RIGHT==='W')
	{
		foreach ($FIELDS as $ID=>$arFields)
		{
			if(!$lAdmin->isUpdated($ID))
				continue;

			$DB->StartTransaction();
			$ID = IntVal($ID);

			$cData = new CRubric();

			if (($rsData = $cData->GetByID()) and ($arData = $rsData->Fetch()))
			{
				foreach ($arFields as $key => $value)
					$arData[$key] = $value;

				if (!$cData->Update($ID, $arData))
				{
					$lAdmin->AddGroupError('Ошибки '.$cData->LAST_ERROR, $ID);
					$DB->Rollback();
				}
			}
			else
			{
				$lAdmin->AddGroupError('Возобновлять нечего', $ID);
				$DB->Rollback();
			}
			$DB->Commit();
		}
	}

	/** обработка одиночных и групповых действий **/
	if (($arID = $lAdmin->GroupAction()) and $POST_RIGHT==="W")
	{
		if($_REQUEST['action_target'] === "selected")
		{
			$cData = new CRubric();
			$rsData = $cData->GetList([$by=>$order], $arFilter);
			while($arRes = $rsData->Fetch())
				$arID[] = $arRes['ID'];
		}
		/** пройдём по списку элементов **/
		foreach ($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;

			$ID = IntVal($ID);

		}

		/** для каждого элемента совершим требуемое действие **/
		switch ($_REQUEST['action'])
		{
			/** удаление **/
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CRubric::Delete($ID))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('Произошла ошибка удаления', $ID);
				}
				$DB->Commit();
			break;

			/** активация/деактивация **/
			case "activate":
			case "deactivate":
				$cData = new CRubric();
				if (($rsData = $cData->GetByID($ID)) and ($arFields = $rsData->Fetch()))
				{
					$arFields["ACTIVE"] = ($_REQUEST['action'] === "activate"?"Y":"N");
					if(!$cData->Update($ID,$arFields))
					{
						$lAdmin->AddGroupError('Произошла ошибка сохранения'.$cData->LAST_ERROR, $ID);
					}
				}
				else
				{
					$lAdmin->AddGroupError('Сохранять нечего', $ID);
				}
			break;
		}
	}
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                       ВЫБОРКА ЭЛЕМЕНТОВ СПИСКА                        **/
	/** ********************************************************************* **/
	/** выберем список рассылок **/
	$cData = new CRubric();
	$rsData = $cData->GetList([$by=>$order], $arFilter);

	/** преобразуем список в экземпляр класса CAdminResult **/
	$rsData = new CAdminResult($rsData, $sTableID);

	$rsData->NavStart();

	$lAdmin->NavText($rsData->GetNavPrint('Навигация'));
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                      ПОДГОТОВКА СПИСКА К ВЫВОДУ                       **/
	/** ********************************************************************* **/
	$lAdmin->AddHeaders([
		[
			"id" => "ID",
			"content" => "ID",
			"sort" => "id",
			"align" => "right",
			"default" => true,
		],
		[
			'id' => 'NAME',
			'content' => 'Название',
			'sort' => 'name',
			'default' => true,
		],
		[
			'id' => 'LID',
			'content' => 'Язык',
			'sort' => 'lid',
			'default' => true,
		],
		[
			'id' => 'SORT',
			'content' => 'Сортировка',
			'sort' => 'sort',
			'align' => 'right',
			'default' => true,
		],
		[
			'id' => 'ACTIVE',
			'content' => 'Активность',
			'sort' => 'act',
			'default' => true,
		],
		[
			'id' => 'VISIBLE',
			'content' => 'Показывать',
			'sort' => 'visible',
			'default' => true,
		],
		[
			'id' => 'AUTO',
			'content' => 'Авто',
			'sort' => 'auto',
			'default' => true,
		],
		[
			'id' => 'LAST_EXECUTED',
			'content' => 'Последнее выполнение',
			'sort' => 'last_executed',
			'default' => true,
		],
	]);

	while($arRes = $rsData->NavNext(true, "f_"))
	{
		$row =& $lAdmin->AddRow($f_ID, $arRes);

		/** параметр NAME будет редактироваться как текст, но отображаться ссылкой **/
		$row->AddInputField('NAME', ['size'=>20]);
		$row->AddViewField('NAME', '<a href="sonek.rtip_rubric_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');

		/** параметр LID будет редактироваться в ввиде выпадающего списка языков **/
		$row->AddEditField('LID', CLang::SelectBox('LID', $f_LID));

		/** параметр SORT будет редактироваться текстом **/
		$row->AddEditField('SORT', ['size'=>20]);

		/** параметры ACTIVE и VISIBLE будут редактироваться чекбоксами **/
		$row->AddCheckField('ACTIVE');
		$row->AddCheckField('VISIBLE');

		/** параметр AUTO будет отображаться в виде да или нет **/
		$row->AddViewField('AUTO', $f_AUTO==='Y'?'Да':'Нет');
		$row->AddEditField('AUTO', '<b>'.($f_AUTO === 'Y'?'Да':'Нет').'</b>');

		/** формируем контекстное меню **/
		$arActions = [];

		/** редактирование элемента **/
		$arActions[] = [
			'ICON' => 'edit',
			'DEFAULT' => true,
			'TEXT' => 'Редактировать',
			'ACTION' => $lAdmin->ActionRedirect('sonek.rtip2_rubric_edit.php?ID='.$f_ID)
		];
		/** удаление элемента **/
		if ($POST_RIGHT>='W')
		{
			$arActions[] = [
				'ICON' => 'delete',
				'TEXT' => 'Удалить',
				'ACTION' => 'if(confirm("Удалить"))'.$lAdmin->ActionDoGroup($f_ID, 'delete')
			];
		}

		/** разделитель **/
		$arActions[] = ['SEPARATOR' => true];

		/** проверка шаблона для автогенерируемых рассылок **/
		if(strlen($f_TEMPLATE)>0 && $f_AUTO==='Y')
			$arActions[] = [
				'ICON' => '',
				'TEXT' => 'Проверка шаблона для автогенерируемых рассылок',
				'ACTION' => $lAdmin->ActionRedirect('template_test.php?ID='.$f_ID)
			];

		/** если последний элемент - разделитель - очистим верстку **/
		if (is_set($arActions[count($arActions)-1], 'SEPARATOR'))
			unset($arActions[count($arActions)-1]);

		/** применим контекстное меню к строке **/
		$row->AddActions($arActions);
	}

	/** резюме таблицы **/
	$lAdmin->AddFooter(
		[
			[
				'title' => 'Количество элементов',
				'value' => $rsData->SelectedRowsCount()
			],
			[
				'counter' => true,
				'title' => 'Счётчик выбранных элементов',
				'value' => '0',
			],
		]
	);

	/** групповые действия **/
	$lAdmin->AddGroupActionTable(
		[
			'delete' => 'Удалить выбранные элементы',
			'activate' => 'Активировать выбранные элементы',
			'deactivate' => 'Деактивировать выбранные элементы'
		]
	);
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                         АДМИНИСТРАТИВНОЕ МЕНЮ                         **/
	/** ********************************************************************* **/
	$aContext = [
		[
			'TEXT' => 'Post Add',
			"LINK" => 'sonek.rtip2_rubric_edit.php?lang='.LANG,
			'TITLE' => 'Post Add Title',
			'ICON' => 'btn_new'
		]
	];
	$lAdmin->AddAdminContextMenu($aContext);
	/** ********************************************************************* **/

	/** ********************************************************************* **/
	/**                                 ВЫВОД                                 **/
	/** ********************************************************************* **/
	/** альтернативный вывод **/
	$lAdmin->CheckListMode();

	/** установим заголовок **/
	$APPLICATION->SetTitle('Заголовок');
	/** ********************************************************************* **/

	/** разделяем подготовку данных и вывод **/
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	/** ********************************************************************* **/
	/**                             ВЫВОД ФИЛЬТРА                             **/
	/** ********************************************************************* **/
	$oFilter = new CAdminFilter(
		$sTableID.'_filter',
		[
			'ID',
			'Сайт',
			'Активность',
			'Публикация',
			'Автоматическая'
		]
	);?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage()?>">
	<?$oFilter->Begin();?>
	<tr>
		<td>
			<b>Найти: </b>
		</td>
		<td>
			<input type="text" size="25" name="find" value="<?=htmlspecialchars($find)?>" title="f find title">
			<?php
				$arr = [
					'reference' => ['ID'],
					'reference_id' => ['id'],
				];
				echo SelectBoxFromArray('find_type', $arr, $find_type, '', '');
			?>
		</td>
	</tr>
	<tr>
		<td><?='ID'?></td>
		<td>
			<input type="text" name="find_id" size="47" value="<?=htmlspecialchars($find_id)?>">
		</td>
	</tr>
	<tr>
		<td>Сайт: </td>
		<td><input type="text" name="find_lid" size="47" value="<?=htmlspecialchars($find_lid)?>"></td>
	</tr>
	<tr>
		<td>Активность: </td>
		<td>
			<?php
				$arr = [
					'reference' => ['Да', 'Нет'],
					'reference_id' => ['Y', 'N'],
				];
				echo SelectBoxFromArray('find_active', $arr, $find_active, 'post all', '');
			?>
		</td>
	</tr>
	<tr>
		<td>Публикация: </td>
		<td><?=SelectBoxFromArray('find_visible', $arr, $find_visible, 'post all', '');?></td>
	</tr>
	<tr>
		<td>Автоматическая: </td>
		<td><?=SelectBoxFromArray('find_auto', $arr, $find_auto, 'post all', '');?></td>
	</tr>
	<?php
		$oFilter->Buttons(['table_id'=>$sTableID, 'url' => $APPLICATION->GetCurPage(), 'form' => 'find_form']);
		$oFilter->End();
	?>
</form>
<?php
	/** выведем страницу списка элементов **/
	$lAdmin->DisplayList();
	/** ********************************************************************* **/

	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");