<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class ProductUpdates extends ObjectModel
{
    /** @var string Name */
    public $id_digitalversionsofthevirtualproduct;

    /** @var int */
    public $id_product;

    /** @var string */
    public $file;

    /** @var string */
    public $version;

    /** @var string */
    public $extentions;

    /** @var string */
    public $compatibility;

    /** @var string */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'pu_digitalversionsofthevirtualproduct',
        'primary' => 'id_digitalversionsofthevirtualproduct',
        'multilang' => false,
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'file' => ['type' => self::TYPE_STRING],
            'version' => ['type' => self::TYPE_STRING],
            'extentions' => ['type' => self::TYPE_STRING],
            'compatibility' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_STRING],
        ],
    ];

    public static function loadVersions($id_product)
    {
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'pu_digitalversionsofthevirtualproduct`
            WHERE `id_product` = ' . (int) $id_product);

        return $result;
    }

    public static function getVersion($id)
    {
        $result = Db::getInstance()->getRow('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'pu_digitalversionsofthevirtualproduct`
            WHERE `id_digitalversionsofthevirtualproduct` = ' . (int) $id);
        return $result;
    }
}
