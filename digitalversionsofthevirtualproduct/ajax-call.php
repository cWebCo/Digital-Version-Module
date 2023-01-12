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
include dirname(__FILE__) . '/../../config/config.inc.php';
$extension = str_replace(', ', ',', Configuration::get(Tools::strtoupper('pu_digitalversionsofthevirtualproduct_EXTENSIONS')));
if (Tools::getValue('submit_new_version') == 'submit new version') {
    $myObj = [];
    if (pSQL(Tools::getValue('main_version'))) {
        $version = '1.7';
    } else {
        $version = '1.6';
    }
    if ($_FILES['file']['error'] == 1) {
        $results_files = get_all_versions((int) Tools::getValue('product_id'), $version);
        $myObj['res'] = 'Error While Uploading Zip File';
        $myObj['htmldata'] = $results_files;
        echo $myJSON = json_encode($myObj);
    } else {
        $extention_explode = explode(',', $extension);
        $wrong_extention = [];
        $zip = new ZipArchive();
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (!in_array('.' . $ext, $extention_explode)) {
            $wrong_extention[] = $ext;
        }
        if ($zip->open($_FILES['file']['tmp_name'])) {
            $files = [];
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                $entry = $zip->statIndex($i);
                if ($entry['size'] > 0) {
                    $f_extract = $zip->getNameIndex($i);
                    $path_info = pathinfo(basename($f_extract));
                    if (!in_array('.' . $path_info['extension'], $extention_explode)) {
                        $wrong_extention[] = $path_info['extension'];
                    }
                }
            }
            $zip->close();
        }
        if (!empty($wrong_extention)) {
            $myObj['res'] = 'Wrong Extentions in Zip File are:- ' . implode(' , ', $wrong_extention);
        } else {
            $upload_dir_path = _PS_ROOT_DIR_ . '/upload/';
            $split_int = array_map('intval', str_split((int) Tools::getValue('product_id')));
            foreach ($split_int as $folder) {
                $upload_dir_path = $upload_dir_path . $folder . '/';
                if (!is_dir($upload_dir_path)) {
                    mkdir($upload_dir_path, 0777, true);
                }
            }
            $file_name = pSQL(Tools::getValue('p_name')) . '-' . pSQL(Tools::getValue('version')) . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir_path . $file_name);
            $insertData = [
                'id_product' => (int) Tools::getValue('product_id'),
                'file' => pSQL($file_name),
                'version' => pSQL(Tools::getValue('version')),
                'compatibility_min' => pSQL(Tools::getValue('compatibility_min')),
                'compatibility_max' => pSQL(Tools::getValue('compatibility_max')),
            ];
            Db::getInstance()->insert('pu_digitalversionsofthevirtualproduct', $insertData);
            $myObj['res'] = 'Data has been saved successfully';
        }
        $results_files = get_all_versions((int) Tools::getValue('product_id'), $version);
        $myObj['htmldata'] = $results_files;
        echo $myJSON = json_encode($myObj);
    }
}
if (Tools::getValue('type') == 'delete_version') {
    $myObj = [];
    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'pu_digitalversionsofthevirtualproduct
        WHERE id_digitalversionsofthevirtualproduct = "' . (int) Tools::getValue('version_id') . '"';
    if (!Db::getInstance()->execute($sql)) {
        $myObj['res'] = 'Data has not been deleted. Please Try Again!';
    } else {
        $myObj['res'] = 'Data has been deleted successfully';
    }
    $results_files = get_all_versions((int) Tools::getValue('product_id'), pSQL(Tools::getValue('main_version')));
    $myObj['htmldata'] = $results_files;
    echo $myJSON = json_encode($myObj);
}
function get_all_versions($pid, $version)
{
    $res = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'pu_digitalversionsofthevirtualproduct
        WHERE `id_product` = ' . (int) $pid);
    $html = '<thead><tr class="nodrag nodrop">
        <th class="fixed-width-lg"><span class="title_box">File</span></th>
        <th class="fixed-width-lg"><span class="title_box">Version</span></th>
        <th class="fixed-width-lg"><span class="title_box">Min Compatibility</span></th>
        <th class="fixed-width-lg"><span class="title_box">Max Compatibility</span></th>
        <th class="fixed-width-lg"><span class="title_box">Publish Date</span></th>
        <th></th>
    </tr>';
    if (!empty($res)) {
        foreach ($res as $list) {
            $ip = (int) $list['id_digitalversionsofthevirtualproduct'];
            $pi = (int) Tools::getValue('product_id');
            $html .= '<tr>';
            if ($version == '1.7') {
                $html .= '<td>
                    <a href="' . pSQL(Tools::getValue('base_url')) . 'upload/' . $list['file'] . '" download>
                        <i class="material-icons">file_download</i>
                    </a>' . $list['file'] . '</td>';
            } else {
                $html .= '<td>
                        <a href="' . pSQL(Tools::getValue('base_url')) . 'upload/' . $list['file'] . '" download>
                            <i class="icon-download-alt"></i>
                        </a>' . $list['file'] . '</td>';
            }
            $html .= '<td>' . $list['version'] . '</td>';
            if ($list['compatibility_min']) {
                $html .= '<td>Version ' . $list['compatibility_min'] . '</td>';
            } else {
                $html .= '<td>--</td>';
            }
            if ($list['compatibility_max']) {
                $html .= '<td>Version ' . $list['compatibility_max'] . '</td>';
            } else {
                $html .= '<td>--</td>';
            }
            $html .= '<td>' . $list['date_add'] . '</td>';
            if ($version == '1.7') {
                $html .= '<td>
                        <a href="javascript:void(0)" class="del" onclick="del(' . $ip . ',' . $pi . ')">
                            <i class="material-icons">delete_forever</i>
                        </a>
                    </td>';
            } else {
                $html .= '<td>
                        <a href="javascript:void(0)" class="del" onclick="del_1_6(' . $ip . ',' . $pi . ')">
                            <i class="icon-trash"></i> Delete
                        </a>
                    </td>';
            }
            $html .= '</tr></thead>';
        }
    } else {
        $html .= '<tr><td colspan="6">No Versions Found</td></tr></thead>';
    }
    return $html;
}
exit;
