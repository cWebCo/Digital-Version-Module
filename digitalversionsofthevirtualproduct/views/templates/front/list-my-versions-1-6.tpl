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

{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account' mod='digitalversionsofthevirtualproduct'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe|escape:'html':'UTF-8'}
    </span>
    <span class="navigation_page">
    {l s='List All Digital Product Versions' mod='digitalversionsofthevirtualproduct'}
    </span>
{/capture}
<h1 class="page-heading">{l s='List All Digital Product Versions' mod='digitalversionsofthevirtualproduct'}</h1>
{block name='page_content'}
    {if empty($list_versions)}
        <p class="info-account">{l s='You have not purchased any Digital Product yet.' mod='digitalversionsofthevirtualproduct'}</p>
    {else}
        <div class="div_my_data">
            {foreach $list_versions as $list}
                <div class="order_vitual_products list_1_6">
                    <div class="product_cover_image for_1_6"><img src="{$list['product_image']|escape:'html':'UTF-8'}"></div>
                    <div class="all_versions">
                        <div class="versions_main_heading">{$list['product_name']|escape:'html':'UTF-8'} - {l s='All Versions' mod='digitalversionsofthevirtualproduct'}</div>
                        <div class="version_column_heading">
                            <ul>
                                <li>{l s='Version' mod='digitalversionsofthevirtualproduct'}</li>
                                <li>{l s='Min Compatibility' mod='digitalversionsofthevirtualproduct'}</li>
                                <li>{l s='Max Compatibility' mod='digitalversionsofthevirtualproduct'}</li>
                                <li>{l s='Action' mod='digitalversionsofthevirtualproduct'}</li>
                            </ul>
                        </div>
                        <div class="product_versions_list">
                            {if empty($list['versions'])}
                                <ul class="no-version-found">{l s='No Version Available' mod='digitalversionsofthevirtualproduct'}</ul>
                            {else}
                                {foreach $list['versions'] as $single_version}
                                    <ul>
                                        <li>{$single_version['version']|escape:'html':'UTF-8'}</li>
                                        <li>{$single_version['compatibility_min']|escape:'html':'UTF-8'}</li>
                                        <li>{$single_version['compatibility_max']|escape:'html':'UTF-8'}</li>
                                        <li>
                                           <a class="get_1_6" href="{$link->getModuleLink('digitalversionsofthevirtualproduct','downloadfile', ['id' => $single_version['id_digitalversionsofthevirtualproduct']])|escape:'html':'UTF-8'}" >
                                                    <i class="icon-download-alt"></i>
                                         </a>
                                      </li>
                                    </ul>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
	        {/foreach}
        </div>
    {/if}
{/block}
