<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE")?></b></p>
<?php foreach ($arResult['USERS'] as $user) {?>
<ul>
    <li>[<?=$user['ID']?>] - <?=$user['LOGIN']?></li>
	<?php foreach ($user['ELEMENTS'] as $news) {?>
    <ul>
        <li>- <?=$news['NAME']?></li>
    </ul>
    <?php }?>
</ul>
<?php }?>