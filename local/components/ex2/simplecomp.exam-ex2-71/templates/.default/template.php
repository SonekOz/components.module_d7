<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<p><b><?=GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE")?></b></p>
<?php foreach ($arResult["END"] as $item){?>
<ul>
    <li><b><?=$item['NAME']?></b></li>
    <?php foreach ($item['ELEMENTS'] as $element){?>
    <ul>
        <li><?=$element['NAME']?> - <?=$element['PROPERTY_PRICE_VALUE']?> - <?=$element['PROPERTY_MATERIAL_VALUE']?></li>
    </ul>
    <?php }?>
</ul>
<?php }?>