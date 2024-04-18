<?php

namespace Awz\Tools\AdminPages;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Awz\Admin\IList;
use Awz\Admin\IParams;
use Awz\Admin\Helper;

Loc::loadMessages(__FILE__);

class PropertyList extends IList implements IParams {

    public function __construct($params){
        parent::__construct($params);
    }

    public function trigerGetRowListAdmin($row){
        //Helper::viewListField($row, 'ID', ['type'=>'entity_link'], $this);
        Helper::editListField($row, 'ACTIVE', ['type'=>'checkbox'], $this);
        Helper::editListField($row, 'IS_REQUIRED', ['type'=>'checkbox'], $this);
        Helper::editListField($row, 'FILTRABLE', ['type'=>'checkbox'], $this);
        Helper::editListField($row, 'SEARCHABLE', ['type'=>'checkbox'], $this);
        Helper::editListField($row, 'CODE', ['type'=>'string'], $this);
        Helper::editListField($row, 'NAME', ['type'=>'string'], $this);
        Helper::editListField($row, 'XML_ID', ['type'=>'string'], $this);
        Helper::editListField($row, 'HINT', ['type'=>'string'], $this);
        Helper::editListField($row, 'SORT', ['type'=>'string'], $this);
        //$row->AddSelectField('ENTITY_ID', \Awz\Bx24Lead\Helper::getProviders());
        $row->addViewField('ID', '<a href="/bitrix/admin/awz_tools_property_edit.php?ID='.$row->arRes['ID'].'&lang='.LANG.'">'.$row->arRes['ID'].'</a>');
        $row->addViewField('NAME', '<a href="/bitrix/admin/awz_tools_property_edit.php?ID='.$row->arRes['ID'].'&lang='.LANG.'">'.$row->arRes['NAME'].'</a>');

        $arActions = array();
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
            "TITLE" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
            "ACTION" => "if(confirm('".Loc::getMessage("MAIN_ADMIN_MENU_DELETE")."')) ".$this->getAdminList()->ActionDoGroup($arRes[$this->getParam("PRIMARY")], "delete"),
        );
        $arActions[] = [
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>Loc::getMessage("AWZ_TOOLS_AP_PROPERTY_LIST_EDITPROP"),
            "TITLE"=>Loc::getMessage("AWZ_TOOLS_AP_PROPERTY_LIST_EDITPROP"),
            "ACTION"=>$this->getAdminList()->ActionRedirect('awz_tools_property_edit.php?ID='.$row->arRes['ID'])
        ];
        $row->AddActions($arActions);
    }

    public function trigerInitFilter(){
    }

    public function trigerGetRowListActions(array $actions): array
    {
        return $actions;
    }

    public static function getTitle(): string
    {
        return Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_LIST_TITLE');
    }

    public static function getParams(): array
    {
        if(!Loader::includeModule('iblock')){
            return [];
        }
        $arParams = [
            "ENTITY" => "\\Bitrix\\Iblock\\PropertyTable",
            "FILE_EDIT" => "awz_tools_property_edit.php",
            "BUTTON_CONTEXTS"=> false,
            "ADD_GROUP_ACTIONS"=> ["edit","delete"],
            "ADD_LIST_ACTIONS"=> [],
            "FIND"=> [
                [
                    'id'=>'IBLOCK_ID',
                    'realId'=>'IBLOCK_ID',
                    'name'=>Loc::getMessage('AWZ_TOOLS_AP_PROPERTY_LIST_IBLOCK_ID'),
                    'type'=>'list',
                    "items" => self::getIblockList(),
                ]
            ],
            "FIND_FROM_ENTITY"=>[
                'ID'=>[],'ACTIVE'=>[],
                'IS_REQUIRED'=>[],'FILTRABLE'=>[],'SEARCHABLE'=>[],
                'NAME'=>[],'MULTIPLE'=>[],'CODE'=>[],'PROPERTY_TYPE'=>[]
            ]
        ];
        return $arParams;
    }

    public static $iblockSel = null;
    public static function getIblockList(){
        if(!self::$iblockSel){
            $ar = [];
            if(Loader::includeModule('iblock')){
                $r = \Bitrix\Iblock\IblockTable::getList([
                    'select'=>['ID','NAME','CODE']
                ]);
                while($data = $r->fetch()){
                    $ar[$data['ID']] = '['.$data['ID'].'] - '.$data['NAME'];
                }
            }
            self::$iblockSel = $ar;
        }
        return self::$iblockSel;
    }
}