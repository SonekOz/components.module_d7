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
?>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?php
	/** здесь будет вывод страницы с формой **/
?>
<?php
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
