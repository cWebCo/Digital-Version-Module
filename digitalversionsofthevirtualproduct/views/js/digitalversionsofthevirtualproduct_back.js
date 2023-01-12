/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(function() {
    if($('body').hasClass('adminproducts')) {
        $('body').addClass('product-page');
    }
    $("#submitAddproductAndStay").click(function (event) {
        var base_url=$("#base_url").val();
        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        //var form = $('.add_new_version')[0];

        // Create an FormData object 
        //var data = new FormData(form);
        var selected_extension = $('#extension_pdf').val().trim();
        if(selected_extension !='') {
           var extn_split =  selected_extension.split(', ');
        }
          
        // disabled the submit button
        $("#submitAddproductAndStay").prop("disabled", true);

        /*var version = $("#version").val();
        var compat_min = $("#compatibility_min").val();
        var compat_max = $("#compatibility_max").val();*/
        var filename = $("#file").val();
         

        var formData = new FormData();
        formData.append('version',$("#version").val());
        formData.append('compatibility_min',$("#compatibility_min").val());
        formData.append('compatibility_max',$("#compatibility_max").val());

        formData.append('extention',$("#extention").val());
        formData.append('product_id',$("#product_id").val());
        formData.append('submit_new_version',$("#submit_new_version").val());
        formData.append('main_version',$("#main_version").val());
        formData.append('p_name',$("#p_name").val());

        formData.append('file', $('#file')[0].files[0]);

        //console.log('formData'+formData);
        var split_extention=filename.substr((filename.lastIndexOf('.') + 1));
        if ($("#version").val()=='' || $("#file").val()=='') {
        //if ($("#version").val()=='' || $("#compatibility_min").val()=='' || $("#compatibility_max").val()=='' || $("#file").val()=='') {
            $(".error").html('Please fill all required fields');
            $("#submitAddproductAndStay").prop("disabled", false);
        } else {
            $(".error").html('');
            // Use a regular expression to trim everything before final dot
            //if (split_extention=='zip') {
             //if($.inArray('.'+split_extention, extn_split) != -1) {
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: base_url+'modules/digitalversionsofthevirtualproduct/ajax-call.php',
                    data: formData,
                    cache: false,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false, 
                    success: function (data) {
                        var result = JSON.parse(data);
                        $(".versions").html(result['htmldata']);
                        if (result['res']=='Error While Uploading Zip File' || result['res'].indexOf('Wrong Extentions in Zip File') >= 0) {
                            $(".error").html(result['res']);
                            $(".success").html('');
                        }
                        if (result['res']=='Data has been saved successfully') {
                            $(".error").html('');
                            $(".success").html(result['res']);
                            $('#version').val('');
                            $('#compatibility_min option:selected').prop("selected", false);
                            $('#compatibility_max option:selected').prop("selected", false);
                            $(".file_name_display").html('');
                        }
                        $("#submitAddproductAndStay").prop("disabled", false);
                        return false;
                    },
                });
            //} else{
                //$(".error").html('Only Zip Extention Allowed');
                //$("#submitAddproductAndStay").prop("disabled", false);
                //return false;
            //}
        }
        return false;
    });

    $("#file").on('change',function(e){
		var fileName = e.target.files[0].name;
        $(".file_name_display").html(fileName);
    });
});

function checkfile(str){
    var split_n=str.split('\\');
    $(".file_name_display").html(split_n[2]);
}

function del(str,str1){
    if ($("#main_version").val()) {
        var version_ps = '1.7';
    } else {
        var version_ps = '1.6';
    }
    var base_url=$("#base_url").val();
    var version_id=str;
    var product_id=str1;
    if (confirm('Are you sure you want to delete this?')) {
        $.ajax({
            type: "POST",
            url: base_url+'modules/digitalversionsofthevirtualproduct/ajax-call.php',
            data: 'version_id='+version_id+'&type=delete_version&product_id='+product_id+'&main_version='+version_ps,
            success: function (data) {
                var result = JSON.parse(data);
                $("#versions").html(result['htmldata']);
                if (result['res']=='Data has not been deleted. Please Try Again!') {
                    $(".error_list").html(result['res']);
                    $(".success_list").html('');
                    $(".success").html('');
                }
                if (result['res']=='Data has been deleted successfully') {
                    $(".error_list").html('');
                    $(".success_list").html(result['res']);
                    $(".success").html('');
                }
                return false;
            },
        });
    }
}
