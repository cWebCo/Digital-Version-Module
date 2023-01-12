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
class DigitalversionsofthevirtualproductDownloadFileModuleFrontController extends ModuleFrontControllerCore
{
    public function init()
    {
        if (!$this->context->customer->isLogged() &&
            $this->php_self != 'authentication' && $this->php_self != 'password') {
            Tools::redirect('index.php?controller=authentication?back=my-account');
        } else {
            $version_detail = ProductUpdates::getVersion((int) Tools::getValue('id'));
            $orders = Order::getCustomerOrders((int) $this->context->customer->id);
            $order_date = [];
            foreach ($orders as $list) {
                $ProductDetailObject = new OrderDetail();
                $product_detail = $ProductDetailObject->getList((int) $list['id_order']);
                foreach ($product_detail as $single) {
                    if ((int) $single['product_id'] == (int) $version_detail['id_product']) {
                        $order_date['order_date'] = pSQL($list['date_upd']);

                        break;
                    }
                }
            }
            if (empty($order_date)) {
                $this->displayCustomError('This product is not purchased by you.');
            } else {
                $res = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_download
                    WHERE `id_product` = "' . (int) $version_detail['id_product'] . '"');
                /* Calculate last date for download version */
                if (!empty($res)) {
                    $nda = (int) $res[0]['nb_days_accessible'];
                } else {
                    $nda = (int) 0;
                }
                $odate = pSQL($order_date['order_date']);
                $last_download_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime($odate)) . ' + ' . $nda . ' days'));
                $upload_version_date = date('Y-m-d', strtotime(pSQL($version_detail['date_add'])));
                if (strtotime($upload_version_date) <= strtotime($last_download_date)) {
                    $upload_dir_path = _PS_ROOT_DIR_ . '/upload/';
                    $split_int = array_map('intval', str_split((int) $version_detail['id_product']));
                    foreach ($split_int as $folder) {
                        $upload_dir_path = $upload_dir_path . $folder . '/';
                    }
                    $filepath = $upload_dir_path . pSQL($version_detail['file']);

                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/download');
                    header('Content-Disposition: attachment; filename=' . pSQL($version_detail['file']));
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($filepath));
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    flush();
                    readfile($filepath);
                } else {
                    $this->displayCustomError('Expiration date has passed, you cannot download this version.');
                }
            }
            exit;
        }
    }

    protected function displayCustomError($msg)
    {
        if (Digitalversionsofthevirtualproduct::isPs17()) {
            $translations = [
                'Not Purchased.' => $this->trans('This product is not purchased by you.'),
                'Expiration' => $this->trans('Expiration date has passed, you cannot download this product.'),
            ];
        } else {
            $translations = [
                'Not Purchased.' => Tools::displayError('This product is not purchased by you.'),
                'Expiration' => Tools::displayError('Expiration date has passed, you cannot download this version.'),
            ];
        }

        if (isset($translations[$msg])) {
            $msg = html_entity_decode($translations[$msg], ENT_QUOTES, 'UTF-8');
        } else {
            $msg = html_entity_decode($msg, ENT_QUOTES, 'UTF-8');
        }
        ?>
        <script type="text/javascript">
        //<![CDATA[
        alert("<?php echo $msg; ?>");
        window.location.href = '<?php echo __PS_BASE_URI__; ?>index.php?controller=authentication?back=my-account';
        //]]>
        </script>
        <?php
        exit;
    }
}
