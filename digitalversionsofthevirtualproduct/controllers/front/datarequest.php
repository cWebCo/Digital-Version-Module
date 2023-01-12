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
require_once _PS_MODULE_DIR_ . 'digitalversionsofthevirtualproduct/classes/ProductUpdates.php';
class DigitalversionsofthevirtualproductDataRequestModuleFrontController extends ModuleFrontControllerCore
{
    public $display_column_left;

    public function init()
    {
        $this->page_name = 'List Versions';
        $this->disableBlocks();
        parent::init();
    }

    protected function disableBlocks()
    {
        $this->display_column_left = false;
    }

    public function initContent()
    {
        parent::initContent();
        if (!$this->context->customer->isLogged() &&
            $this->php_self != 'authentication' && $this->php_self != 'password') {
            Tools::redirect('index.php?controller=authentication?back=my-account');
        } else {
            $lang_id = (int) $this->context->language->id;
            $base_url = _PS_BASE_URL_ . __PS_BASE_URI__;
            $orders_list = Order::getCustomerOrders((int) $this->context->customer->id);
            $products_list = [];
            foreach ($orders_list as $list) {
                $ProductDetailObject = new OrderDetail();
                $product_detail = $ProductDetailObject->getList((int) $list['id_order']);
                foreach ($product_detail as $single_product) {
                    $product_id = (int) $single_product['product_id'];
                    $product = new Product((int) $single_product['product_id'], false, (int) $lang_id);
                    if ($product->is_virtual) {
                        /* Product Image */
                        $link = new Link(); // because getImageLInk is not static function
                        $product_url = $link->getProductLink($product);
                        if (Digitalversionsofthevirtualproduct::isPs17()) {
                            $image = Image::getCover((int) $single_product['product_id']);
                            $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                            $image_type = ImageType::getFormattedName('home');
                            $lr = $product->link_rewrite;
                            $imagePath = $protocol . $link->getImageLink(pSQL($lr), (int) $image['id_image'], $image_type);
                        } else {
                            $image = Product::getCover((int) $single_product['product_id']);
                            $image = new Image($image['id_image']);
                            $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
                            $imagePath = $protocol . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                        }
                        /* Get Number of days available for download */
                        $res = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_download
                            WHERE `id_product` = "' . (int) $single_product['product_id'] . '"');
                        /* Calculate last date for download version */
                        if (!empty($res)) {
                            $nda = (int) $res[0]['nb_days_accessible'];
                        } else {
                            $nda = (int) 0;
                        }
                        $ldu = pSQL($list['date_upd']);
                        $last_dload_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime($ldu)) . ' + ' . $nda . ' days'));
                        if (!array_key_exists((int) $single_product['product_id'], $products_list)) {
                            $versions = ProductUpdates::loadVersions((int) $single_product['product_id']);
                            $products_list[$single_product['product_id']] =
                                    ['order_date' => date('Y-m-d', strtotime($ldu)),
                                    'p_url' => $product_url,
                                    'product_id' => $product_id,
                                    'product_image' => $imagePath,
                                    'product_name' => pSQL($product->name),
                                    'versions' => $versions,
                        ];
                        }
                    }
                }
            }
            $this->context->smarty->assign([
                'list_versions' => $products_list,
                'base_url' => $base_url,
            ]);
            if (Digitalversionsofthevirtualproduct::isPs17()) {
                $temp = 'module:digitalversionsofthevirtualproduct/views/templates/front/list-my-versions.tpl';
                $this->setTemplate($temp);
            } else {
                $this->setTemplate('list-my-versions-1-6.tpl');
            }
        }
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
