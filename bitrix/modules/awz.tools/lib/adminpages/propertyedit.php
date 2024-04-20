<?php

namespace Awz\Tools\AdminPages;

use Awz\Admin\Helper;
use Bitrix\Main\Localization\Loc;
use Awz\Admin\IForm;
use Awz\Admin\IParams;
use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;

Loc::loadMessages(__FILE__);

class PropertyEdit extends IForm implements IParams {

    public function __construct($params){
        parent::__construct($params);
    }

    public function trigerCheckActionAdd($func){
        return $func;
    }

    public function trigerCheckActionUpdate($func){
        return [__CLASS__,'updateHak'];
    }
    public static function updateHak($primary, $data){
        if(!Loader::includeModule('iblock')) return;
        $checkUpdates = [];
        foreach($data['VALUES']['val_from'] as $k=>$v){
            if($v != $data['VALUES']['val_to'][$k]){
                $checkUpdates[] = [$v, $data['VALUES']['val_to'][$k]];
            }
        }
        if(!empty($checkUpdates)){
            $propertyData = \Bitrix\Iblock\PropertyTable::getById($primary['ID'])->fetch();
            foreach($checkUpdates as $v){
                $r = \CIblockElement::getList([], [
                    '=IBLOCK_ID'=>$propertyData['IBLOCK_ID'],
                    '=PROPERTY_'.$propertyData['ID']=>$v[0]
                ], false, false, ['ID','IBLOCK_ID']
                );
                while($data = $r->fetch()){
                    \CIBlockElement::SetPropertyValues($data['ID'], $data['IBLOCK_ID'], $v[1], $propertyData['ID']);
                    if($data['FASET']=='Y'){
                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($data['IBLOCK_ID'], $data['ID']);
                    }
                }
            }

        }
        //echo'<pre>';print_r($data);echo'</pre>';
        //die();
    }

    public static function getTitle(): string
    {
        return Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_TITLE');
    }

    public static function getParams(): array
    {
        $arParams = array(
            "ENTITY" => "\\Awz\\Admin\\GensTable",
            "BUTTON_CONTEXTS"=>array('btn_list'=>false),
            "LIST_URL"=>'/bitrix/admin/awz_tools_property_list.php',
            "DEFAULT_VALUES"=>[
                "FIELD_ACTIVE"=>"Y"
            ],
            "TABS"=>array(
                "edit1" => array(
                    "NAME"=>Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_EDIT1'),
                    "FIELDS" => array(
                        "FASET"=>[
                            "NAME"=>"FASET",
                            "TYPE"=>"BOOL",
                            "DEFAULT"=>"N",
                            "TITLE"=>Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_FASET'),
                        ],
                        "VALUES"=>[
                            "NAME"=>"VALUES",
                            "TYPE"=>"CUSTOM",
                            "TITLE"=>Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_VALUES'),
                            "FUNC_VIEW"=>"ValuesPRM"
                        ]
                    )
                )
            )
        );
        return $arParams;
    }

    public function ValuesPRM($arField){
        if(!Loader::includeModule('iblock')) return;
        $ID = $this->getParam('ID');

        $propertyData = \Bitrix\Iblock\PropertyTable::getById($ID)->fetch();

        $edited = false;
        if($propertyData['PROPERTY_TYPE']=='S' && $propertyData['MULTIPLE']=='N'){
            $edited = true;
        }
        if($propertyData['PROPERTY_TYPE']=='N' && $propertyData['MULTIPLE']=='N'){
            $edited = true;
        }
        if($edited){
            $r = \CIblockElement::getList([], [
                'IBLOCK_ID'=>$propertyData['IBLOCK_ID'],
                '!PROPERTY_'.$propertyData['ID']=>false
            ],
                ['PROPERTY_'.$propertyData['ID']],
                false
            );
            ?>
            <table>
                <tr>
                    <th style="text-align:left;"><?=Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_VALUES_TD1')?></th>
                    <th style="text-align:left;"><?=Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_VALUES_TD2')?></th>
                </tr>
            <?
            $cn = 0;
            while($data = $r->fetch()){

                ?>
                <tr>
                    <td><input type="text" name="<?=$arField['NAME']?>[val_from][<?=$cn?>]" value="<?=htmlspecialcharsEx($data['PROPERTY_'.$propertyData['ID'].'_VALUE'])?>"></td>
                    <td><input type="text" name="<?=$arField['NAME']?>[val_to][<?=$cn?>]" value="<?=htmlspecialcharsEx($data['PROPERTY_'.$propertyData['ID'].'_VALUE'])?>"></td>
                </tr>
                <?
                $cn++;
                //print_r($data);
            }
            ?>
            </table>
            <?
        }else{
            echo Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_EDIT_VALUES_ERR');
        }

        //echo'<pre>';print_r($propertyData);echo'</pre>';


    }

}