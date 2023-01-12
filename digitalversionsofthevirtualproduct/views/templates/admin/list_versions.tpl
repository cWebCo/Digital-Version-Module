{*
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<input type="hidden" name="extension_pdf" id="extension_pdf" value="{$upload_exension}"/>
{if $version neq ''}
     
    <div class="add_new_version">
        <h2 class="new_version_heading">{l s='Add New Version' mod='digitalversionsofthevirtualproduct'}</h2>
        {*<form name="product_versions" id="product_versions_post" method="post" enctype="multipart/form-data">*}
            <span class="error"></span>
            <span class="success"></span>
            <table class="new_version">
                <tr>
                    <td>{l s='Version' mod='digitalversionsofthevirtualproduct'} <sup class="required_field">*</sup></td>
                    <td>
                        <input type="text" required="required" class="form-control" name="version" id="version" value="">
                    </td>
                </tr>
                <tr>
                    <td>{l s='File' mod='digitalversionsofthevirtualproduct'} <sup class="required_field">*</sup></td>
                    <td class="file_main">
                        <input type="file" class="form-control" name="file" id="file" value="">
                        <label class="file_label">
                            <span class="file_name_display"></span>
                            <strong>{l s='Choose a file' mod='digitalversionsofthevirtualproduct'}</strong>
                        </label>
                    </td>
                </tr>
                <tr>
                    <input type="hidden" name="extention" id="extention" value="{$extensions|escape:'html':'UTF-8'}">
                    <input type="hidden" name="product_id" id="product_id" value="{$id_product|escape:'html':'UTF-8'}">
                    <input type="hidden" name="submit_new_version" id="submit_new_version" value="submit new version">
                    <input type="hidden" name="main_version" id="main_version" value="{$version|escape:'html':'UTF-8'}">
                    <input type="hidden" name="base_url" id="base_url" value="{$base_url|escape:'html':'UTF-8'}">
                    <input type="hidden" name="p_name" id="p_name" value="{$product_name|escape:'html':'UTF-8'}">
                    <td>{l s='Compatibility' mod='digitalversionsofthevirtualproduct'} {*<sup class="required_field">*</sup>*}</td>
                    <td>
                        <select name="compatibility_min" id="compatibility_min" class="form-control">
                            <option value="">-- {l s='Min Compatibility Version' mod='digitalversionsofthevirtualproduct'} --</option>
                            <option value="1.5">{l s='Version 1.5' mod='digitalversionsofthevirtualproduct'}</option>
                            <option value="1.6">{l s='Version 1.6' mod='digitalversionsofthevirtualproduct'}</option>
                            <option value="1.7">{l s='Version 1.7' mod='digitalversionsofthevirtualproduct'}</option>
                        </select>
                    </td>
                    <td>
                        <select name="compatibility_max" id="compatibility_max" class="form-control">
                            <option value="">-- {l s='Max Compatibility Version' mod='digitalversionsofthevirtualproduct'} --</option>
                            <option value="1.5">{l s='Version 1.5' mod='digitalversionsofthevirtualproduct'}</option>
                            <option value="1.6">{l s='Version 1.6' mod='digitalversionsofthevirtualproduct'}</option>
                            <option value="1.7">{l s='Version 1.7' mod='digitalversionsofthevirtualproduct'}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <button type="submit" id="submitAddproductAndStay" name="submitAddproductAndStay" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i> {l s='Save and stay' mod='digitalversionsofthevirtualproduct'}
                        </button>
                    </td>
                </tr>
            </table>
        {*</form>*}
    </div>
    <h3 class="list_heading">{l s='List All Versions' mod='digitalversionsofthevirtualproduct'}</h3>
    <span class="error_list"></span>
    <span class="success_list"></span>
    <table class="versions" id="versions">
        <tr>
            <th>{l s='File' mod='digitalversionsofthevirtualproduct'}</th>
            <th>{l s='Version' mod='digitalversionsofthevirtualproduct'}</th>
            <th>{l s='Min Compatibility' mod='digitalversionsofthevirtualproduct'}</th>
            <th>{l s='Max Compatibility' mod='digitalversionsofthevirtualproduct'}</th>
            <th>{l s='Publish Date' mod='digitalversionsofthevirtualproduct'}</th>
            <th></th>
        </tr>
	 
        {if empty($list_versions)}
            <tr><td colspan="6">{l s='No Versions Found' mod='digitalversionsofthevirtualproduct'}</td></tr>
        {else}
            {foreach $list_versions as $list}
                <tr>
                    <td><a href="{$upload_path|escape:'html':'UTF-8'}{$list['file']|escape:'html':'UTF-8'}" download><i class="material-icons">file_download</i></a>{$list['file']|escape:'html':'UTF-8'}</td>
                    <td>{$list['version']|escape:'html':'UTF-8'}</td>
                    <td>{if !empty($list['compatibility_min'])}Version {$list['compatibility_min']|escape:'html':'UTF-8'}{else}--{/if}</td>
                    <td>{if !empty($list['compatibility_max'])}Version {$list['compatibility_max']|escape:'html':'UTF-8'}{else}--{/if}</td>
                    <td>{$list['date_add']|escape:'html':'UTF-8'}</td>
                    <td><a href="javascript:void(0)" class="del" onclick="del({$list['id_digitalversionsofthevirtualproduct']|escape:'html':'UTF-8'},{$id_product|escape:'html':'UTF-8'})"><i class="material-icons">delete_forever</i></a></td>
                </tr>
            {/foreach}
	    {/if}
    </table>
{else}
    <div id="product-tab-content-digitalversionsofthevirtualproduct" class="product-tab-content" style="">
        <div id="product-new-version-add" class="panel product-tab">
            <input type="hidden" name="submitted_tabs[]" value="digitalversionsofthevirtualproduct">
            <h3 class="tab"> <i class="icon-info"></i> {l s='Add New Version' mod='digitalversionsofthevirtualproduct'}</h3>
            <span class="error"></span>
            <span class="success"></span>
            <div id="product_options" class="form-group">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="col-lg-1"><span class="pull-right"></span></div>
                        <label class="control-label col-lg-2">
                            {l s='Version' mod='digitalversionsofthevirtualproduct'} <sup class="required_field">*</sup>
                        </label>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" name="version" id="version" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-1"><span class="pull-right"></span></div>
                        <label class="control-label col-lg-2">
                            {l s='File' mod='digitalversionsofthevirtualproduct'} <sup class="required_field">*</sup>
                        </label>
                        <div class="col-lg-3">
                            <input type="hidden" name="extention" value="{$extensions|escape:'html':'UTF-8'}">
                            <input type="hidden" name="product_id" value="{$id_product|escape:'html':'UTF-8'}">
                            <input type="hidden" name="submit_new_version" value="submit new version">
                            <input type="hidden" name="base_url" id="base_url" value="{$base_url|escape:'html':'UTF-8'}">
                            <input type="hidden" id="main_version" value="{$version|escape:'html':'UTF-8'}">
                            <input type="file" class="form-control" name="file" onchange="checkfile(this.value)" id="filepu" value="">
                            <label class="file_label">
                                <span class="file_name_display"></span>
                                <strong>{l s='Choose a file' mod='digitalversionsofthevirtualproduct'}</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-1"><span class="pull-right"></span></div>
                        <label class="control-label col-lg-2">
                            {l s='Compatibility' mod='digitalversionsofthevirtualproduct'} {*<sup class="required_field">*</sup>*}
                        </label>
                        <div class="col-lg-3">
                            <select name="compatibility_min" id="compatibility_min" class="form-control">
                                <option value="">-- {l s='Min Compatibility Version' mod='digitalversionsofthevirtualproduct'} --</option>
                                <option value="1.5">{l s='Version 1.5' mod='digitalversionsofthevirtualproduct'}</option>
                                <option value="1.6">{l s='Version 1.6' mod='digitalversionsofthevirtualproduct'}</option>
                                <option value="1.7">{l s='Version 1.7' mod='digitalversionsofthevirtualproduct'}</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select name="compatibility_max" id="compatibility_max" class="form-control">
                                <option value="">-- {l s='Min Compatibility Version' mod='digitalversionsofthevirtualproduct'} --</option>
                                <option value="1.5">{l s='Version 1.5' mod='digitalversionsofthevirtualproduct'}</option>
                                <option value="1.6">{l s='Version 1.6' mod='digitalversionsofthevirtualproduct'}</option>
                                <option value="1.7">{l s='Version 1.7' mod='digitalversionsofthevirtualproduct'}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="panel-footer">
		        <a href="index.php?controller=AdminProducts&token={Tools::getValue('token')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> Cancel</a>
		        <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> Save</button>
                <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> Save and stay</button>
            </div>
        </div>
    </div>
    <div id="product-tab-content-digitalversionsofthevirtualproduct" class="product-tab-content" style="">
        <div id="product-new-version-add" class="panel product-tab">
            <h3 class="tab"> <i class="icon-info"></i> {l s='List All Versions' mod='digitalversionsofthevirtualproduct'}</h3>
            <span class="error_list"></span>
            <span class="success_list"></span>
            <div id="product_options" class="form-group">
                <!-- List Versions -->
                <table class="table tableDnD" id="versions">
                    <thead>
                        <tr class="nodrag nodrop">
                            <th class="fixed-width-lg"><span class="title_box">{l s='File' mod='digitalversionsofthevirtualproduct'}</span></th>
                            <th class="fixed-width-lg"><span class="title_box">{l s='Version' mod='digitalversionsofthevirtualproduct'}</span></th>
                            <th class="fixed-width-lg"><span class="title_box">{l s='Min Compatibility' mod='digitalversionsofthevirtualproduct'}</span></th>
                            <th class="fixed-width-lg"><span class="title_box">{l s='Max Compatibility' mod='digitalversionsofthevirtualproduct'}</span></th>
                            <th class="fixed-width-lg"><span class="title_box">{l s='Publish Date' mod='digitalversionsofthevirtualproduct'}</span></th>
                            <th></th> <!-- action -->
                        </tr>
                    </thead>
                    <tbody id="imageList">
                        {if empty($list_versions)}
                            <tr><td colspan="6">{l s='No Versions Found' mod='digitalversionsofthevirtualproduct'}</td></tr>
                        {else}
                            {foreach $list_versions as $list}
                                <tr>
                                    <td>
                                        <a href="{$upload_path|escape:'html':'UTF-8'}{$list['file']|escape:'html':'UTF-8'}" download>
                                            <i class="icon-download-alt"></i>
                                        </a> {$list['file']|escape:'html':'UTF-8'}
                                    </td>
                                    <td>{$list['version']|escape:'html':'UTF-8'}</td>
                                    <td>{if !empty($list['compatibility_min'])}Version {$list['compatibility_min']|escape:'html':'UTF-8'}{else}--{/if}</td>
                                    <td>{if !empty($list['compatibility_max'])}Version {$list['compatibility_max']|escape:'html':'UTF-8'}{else}--{/if}</td>
                                    <td>{$list['date_add']|escape:'html':'UTF-8'}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="del" onclick="del({$list['id_digitalversionsofthevirtualproduct']|escape:'html':'UTF-8'},{$id_product|escape:'html':'UTF-8'})">
                                            <i class="icon-trash"></i> {l s='Delete' mod='digitalversionsofthevirtualproduct'}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}
