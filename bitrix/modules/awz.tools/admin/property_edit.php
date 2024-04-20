<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

global $APPLICATION;
$dirs = explode('/',dirname(__DIR__ . '../'));
$module_id = array_pop($dirs);
unset($dirs);
Loc::loadMessages(__FILE__);

if(!Loader::includeModule($module_id)) return;

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

require_once('check_awz_admin.php');

/* "Awz\Tools\AdminPages\PropertyEdit" replace generator */
use Awz\Tools\AdminPages\PropertyEdit as PageItemEdit;

$APPLICATION->SetTitle(PageItemEdit::getTitle());
$arParams = PageItemEdit::getParams();

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/awz.admin/include/handler_el.php");
/* @var bool $customPrint */
if(!$customPrint) {
    $adminCustom = new PageItemEdit($arParams);
    $adminCustom->defaultInterface();
}