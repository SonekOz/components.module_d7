<?php
	use \Bitrix\Main\Localization\Loc;

	if (!check_bitrix_sessid())
		return;

    /** Работа с .settings.php **/
    $install_count = \Bitrix\Main\Config\Configuration::getInstance()->get('sonek_module_d7');
    echo CAdminMessage::ShowMessage([
        "MESSAGE" => Loc::getMessage('SONEK_D7_INSTALL_COUNT').$install_count['install'],
        "TYPE" => "OK"
    ]);

    $cache_type = \Bitrix\Main\Config\Configuration::getInstance()->get('cache');

    if(!$cache_type['type'] or $cache_type['type']=='none')
    {
        echo CAdminMessage::ShowMessage([
            "MESSAGE" => Loc::getMessage("SONEK_D7_NO_CACHE"),
            "TYPE" => "ERROR",
        ]);
    }
    /** ---------------------- **/


	if ($ex = $APPLICATION->GetException())
		echo CAdminMessage::ShowMessage([
			"TYPE" => "ERROR",
			"MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
			"DETAILS" => $ex->GetString(),
			"HTML" => true,
		]);
	else
		echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
?>
<form action="<?php echo $APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?php echo LANGUAGE_ID?>">
	<input type="submit" name="" value="<?php echo Loc::getMessage('MOD_BACK');?>">
</form>
