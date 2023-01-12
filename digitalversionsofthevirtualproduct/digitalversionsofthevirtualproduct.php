<?php
/**
* 2007-2022 PrestaShop.
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
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/classes/ProductUpdates.php';
class Digitalversionsofthevirtualproduct extends Module
{
    protected $config_form = false;
    public $prefix;

    public function __construct()
    {
        $this->name = 'digitalversionsofthevirtualproduct';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Webgarh';
        $this->prefix = 'pu_digitalversionsofthevirtualproduct';
        $this->module_key = 'acdf4feeb78e1bddab0c1e6c6c45de6c';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Digital versions of the virtual product');
        $this->description = $this->l('This module allow to manage the updates and download of digital products.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update.
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayCustomerAccount');
    }

    public function uninstall()
    {
        Configuration::deleteByName(Tools::strtoupper($this->prefix . '_EXTENSIONS'));
        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        if (Tools::isSubmit('submit' . $this->name)) {
            $this->postProcess();
        }
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
         . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a extensions list'),
                        'name' => Tools::strtoupper($this->prefix . '_EXTENSIONS'),
                        'label' => $this->l('Extensions'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
               Tools::strtoupper($this->prefix . '_EXTENSIONS') => Configuration::get(Tools::strtoupper($this->prefix . '_EXTENSIONS')),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->registerStylesheet('modules-digitalversionsofthevirtualproduct-css', 'modules/' . $this->name . '/views/css/' . $this->name . '_front.css');
        $this->context->controller->registerJavascript('modules-digitalversionsofthevirtualproduct-js', 'modules/' . $this->name . '/views/js/' . $this->name . '_front.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addJS($this->_path . 'views/js/' . $this->name . '_back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/' . $this->name . '_back.css');
    }

    public function hookDisplayCustomerAccount()
    {
        $this->context->smarty->assign([
            'version' => self::isPs17(),
        ]);

        return $this->display(__FILE__, 'views/templates/front/my_account_link.tpl');
    }

    public function hookModuleRoutes()
    {
        return [
            'module-' . $this->name . '-datarequest' => [
                'controller' => 'datarequest',
                'rule' => 'my-account/data-request',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
            'module-' . $this->name . '-downloadfile' => [
                'controller' => 'downloadfile',
                'rule' => 'my-account/download-file/{id}',
                'keywords' => [
                    'id' => ['regexp' => '[0-9]+', 'param' => 'id'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (!empty((int) $params['id_product'])) {
            $id_product = (int) $params['id_product'];
        } else {
            $id_product = (int) Tools::getValue('id_product');
        }
        $selected_extension = str_replace(', ', ',', Configuration::get(Tools::strtoupper($this->prefix . '_EXTENSIONS')));
        // $base_url = _PS_BASE_URL_ . __PS_BASE_URI__;
        $base_url = Context::getContext()->shop->getBaseURL(true);
        $extentions = Configuration::get(Tools::strtoupper($this->prefix . '_EXTENSIONS'));
        $explode_wxtentions = explode('.', $extentions);
        $trimmedArray = array_filter($explode_wxtentions);
        $imp_extensions = preg_replace('/\s+/', '', implode(',', $trimmedArray));
        $product = new Product((int) $id_product);
        if ($product->is_virtual) {
            $upload_dir_path = _PS_BASE_URL_ . __PS_BASE_URI__ . 'upload/';
            $versions = ProductUpdates::loadVersions($id_product);
            $split_int = array_map('intval', str_split((int) $id_product));
            foreach ($split_int as $folder) {
                $upload_dir_path = $upload_dir_path . $folder . '/';
            }
            $versions = ProductUpdates::loadVersions($id_product);
            $this->context->smarty->assign([
                'list_versions' => $versions,
                'extensions' => $imp_extensions,
                'id_product' => (int) $id_product,
                'base_url' => $base_url,
                'version' => self::isPs17(),
                'product_name' => pSQL($product->name[1]),
                'upload_path' => $upload_dir_path,
                'upload_exension' => ($selected_extension) ? $selected_extension : '',
            ]);

            return $this->display(__FILE__, 'views/templates/admin/list_versions.tpl');
        } else {
            return '<span class="not_virtual">This product is not virtual product</span>';
        }
    }

    public static function isPs17()
    {
        return (bool) version_compare(_PS_VERSION_, '1.7', '>=');
    }

    public function hookActionProductUpdate($params)
    {
        if (Tools::getValue('key_tab') == 'ModuleDigitalversionsofthevirtualproduct') {
            if (empty(Tools::getValue('version'))) {
                $this->context->controller->errors[] =
                    $this->l('Please Enter Version field');
            } elseif (empty(Tools::getValue('compatibility_min'))) {
                // $this->context->controller->errors[] =
                // $this->l('Please Choose Min Compatibility field');
            } elseif (empty(Tools::getValue('compatibility_max'))) {
                // $this->context->controller->errors[] =
                // $this->l('Please Choose Max Compatibility field');
            } elseif (empty($_FILES['file']['name'])) {
                $this->context->controller->errors[] =
                    $this->l('Please Add Zip File');
            } else {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'zip') {
                    if ($_FILES['file']['error'] == 1) {
                        $this->context->controller->errors[] =
                            $this->l('Error While Uploading Zip File');
                    } else {
                        $extention_explode = explode(',', pSQL(Tools::getValue('extention')));
                        $wrong_extention = [];
                        $zip = new ZipArchive();
                        if ($zip->open($_FILES['file']['tmp_name'])) {
                            for ($i = 0; $i < $zip->numFiles; ++$i) {
                                $entry = $zip->statIndex($i);
                                if ($entry['size'] > 0) {
                                    $f_extract = $zip->getNameIndex($i);
                                    $path_info = pathinfo(basename($f_extract));
                                    if (!in_array($path_info['extension'], $extention_explode)) {
                                        $wrong_extention[] = $path_info['extension'];
                                    }
                                }
                            }
                            $zip->close();
                        }
                        if (!empty($wrong_extention)) {
                            $res_msg = $this->l('Wrong Extentions in Zip File are: %s');
                            $res_error = sprintf($res_msg, implode(' , ', $wrong_extention));
                            $this->context->controller->errors[] =
                                $this->l($res_error);
                        } else {
                            $upload_dir_path = _PS_ROOT_DIR_ . '/upload/';
                            $split_int = array_map('intval', str_split((int) $params['id_product']));
                            foreach ($split_int as $folder) {
                                $upload_dir_path = $upload_dir_path . $folder . '/';
                                if (!is_dir($upload_dir_path)) {
                                    mkdir($upload_dir_path, 0777, true);
                                }
                            }

                            $file_name = pSQL($params['product']->name[1]) . '-' . pSQL(Tools::getValue('version')) . '.zip';
                            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir_path . $file_name);
                            $insertData = [
                                'id_product' => (int) Tools::getValue('product_id'),
                                'file' => $file_name,
                                'version' => pSQL(Tools::getValue('version')),
                                'compatibility_min' => pSQL(Tools::getValue('compatibility_min')),
                                'compatibility_max' => pSQL(Tools::getValue('compatibility_max')),
                            ];
                            Db::getInstance()->insert('pu_digitalversionsofthevirtualproduct', $insertData);
                        }
                    }
                } else {
                    $this->context->controller->errors[] =
                        $this->l('Only Zip Extention Allowed');
                }
            }

            return true;
        }
    }
}
