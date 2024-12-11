<?php
	use \Bitrix\Main\Localization\Loc;

	if (!check_bitrix_sessid())
		return;

	if ($ex = $APPLICATION->GetException())
		echo CAdminMessage::ShowMessage([
			"TYPE" => "ERROR",
			"MESSAGE" => Loc::getMessage("MOD_UNINST_ERR"),
			"DETAILS" => $ex->GetString(),
			"HTML" => true,
		]);
	else
		echo CAdminMessage::ShowNote(Loc::getMessage("MOD_UNINST_OK"));

	/** Работа с .settings.php **/
	$uninstall_count = \Bitrix\Main\Config\Configuration::getInstance()->get('sonek_module_d7');
	echo CAdminMessage::ShowMessage([
		"MESSAGE" => Loc::getMessage('SONEK_D7_UNINSTALL_COUNT').$uninstall_count['uninstall'],
		"TYPE" => "OK"
	]);
	/** ---------------------- **/
?>
<form action="<?php echo $APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?php echo LANGUAGE_ID?>">
	<input type="submit" name="" value="<?php echo Loc::getMessage('MOD_BACK');?>">
</form>
