<?php
	use Bitrix\Main\Localization\Loc;
	use Bitrix\Main\Config\Option;

	$module_id = 'sonek.d7';

	Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT."/modules/main/options.php");
	Loc::loadMessages(__FILE__);

    if ($APPLICATION->GetGroupRight($module_id) < "S")
    {
        $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
    }

	\Bitrix\Main\Loader::includeModule($module_id);

	$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

	/** @var $aTabs - описание опций */
	$aTabs = [
		[
			'DIV' => 'edit1',
			'TAB' => Loc::getMessage('SONEK_D7_TAB_SETTINGS'),
			'OPTIONS' => [
				['field_text', Loc::getMessage('SONEK_D7_FIELD_TEXT_TITLE'),
					'',
					['textarea', 10, 50]
				],
				['field_line', Loc::getMessage('SONEK_D7_FIELD_LINE_TITLE'),
					'',
					['text', 10]
				],
				['field_list', Loc::getMessage('SONEK_D7_FIELD_LIST_TITLE'),
					'',
					['multiselectbox',
						['var1'=>'var1','var2'=>'var2','var3'=>'var3','var4'=>'var4']
					]
				],
			]
		],
		[
			"DIV" => "edit2",
			"TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
			"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
		],
	];

	/** Сохранение **/
	if ($request->isPost() && $request['Update'] && check_bitrix_sessid())
	{

		foreach ($aTabs as $aTab)
		{
			//Или можно использовать __AdmSettingsSaveOptions($MODULE_ID, $arOptions);
			foreach ($aTab['OPTIONS'] as $arOption)
			{
				if (!is_array($arOption)) //Строка с подсветкой. Используется для разделения настроек в одной вкладке
					continue;

				if ($arOption['note']) //Уведомление с подсветкой
					continue;


				//Или __AdmSettingsSaveOption($MODULE_ID, $arOption);
				$optionName = $arOption[0];

				$optionValue = $request->getPost($optionName);

				Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue):$optionValue);
			}
		}
	}

	/** Визуальный вывод **/
	$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
	<form method='post' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='academy_d7_settings'>

		<? foreach ($aTabs as $aTab):
			if($aTab['OPTIONS']):?>
				<? $tabControl->BeginNextTab(); ?>
				<? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

			<?      endif;
		endforeach; ?>

		<?
			$tabControl->BeginNextTab();

            require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/admin/group_rights.php");

			$tabControl->Buttons(); ?>

		<input type="submit" name="Update" value="<?echo GetMessage('MAIN_SAVE')?>">
		<input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
		<?=bitrix_sessid_post();?>
	</form>
<? $tabControl->End(); ?>