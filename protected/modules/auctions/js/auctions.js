$(document).ready(function() {
    //Сall the member registration function.
    $("#memberRegistration").on("click", function(){
        var el =  $("#memberRegistration");
        var formName = $(el).data("formName");
        var accountType = 'members';

        return registration(el, formName, accountType);
    });

    // Сall the member restore password function.
    $("#memberRestorePassword").on("click", function(){
        var el =  $("#memberRestorePassword");

        return restorePasswordForm(el);
    });

    // Call addToWatchList() function on click `#watchlist`
    $("#watchlist").on("click", function(){
       var watchlist = $("#watchlist");
       var auctionId = watchlist.data('auctionId');

       addToWatchList(watchlist, auctionId);
    });

    // replace class .fa-plus on click `.sub-category`
    $(".sub-category").click(function(){
        var faEl = $(this).find("i.fa");
        var existPlus = faEl.is(".fa-plus");

        if (existPlus) {
            faEl.removeClass("fa-plus");
            faEl.addClass("fa-minus");
        } else {
            faEl.removeClass("fa-minus");
            faEl.addClass("fa-plus");
        }
    });

    //Сall add shipping address function.
    $("#shipping-new-address-button").on("click", function(){
        auctions_addShippingAddress(this);
    });
});

/**
 * Change country from dropdown box
 * @param frm
 * @param country
 * @param state
 * @param type
 */
function auctions_changeCountry(frm, country, state, type)
{
    // define this to prevent name overlapping
    var $ = jQuery;

    var token = $('#'+frm).find('input[name=APPHP_CSRF_TOKEN]').val();
    // var stateId = $('#'+frm).find('*[name=state]').attr('id');
    var stateId = $('#'+frm+'_state').attr('id');
    var countryCode = (country != null) ? country : '';
    var stateCode = (state != null) ? state : '';

    var ajax = $.ajax({
        url: 'locations/getSubLocations',
        global: false,
        type: 'POST',
        data: ({
            APPHP_CSRF_TOKEN: token,
            act: 'send',
            country_code: countryCode
        }),
        dataType: 'html',
        async: true,
        error: function(html){
            console.error("AJAX: cannot connect to the server or server response error!");
        },
        success: function(html){
            try{
                var obj = $.parseJSON(html);
                if(obj[0].status == "1"){
                    if(type == 'backend'){
                        if(obj.length > 1){
                            $("#"+stateId).replaceWith('<select id="'+stateId+'" name="state"></select>');
                            $("#"+stateId).empty();
                            // add empty option
                            $("<option />", {val: "", text: "--"}).appendTo("#"+stateId);
                            for(var i = 1; i < obj.length; i++){
                                if(obj[i].code == stateCode && stateCode != ''){
                                    $("<option />", {val: obj[i].code, text: obj[i].name, selected: true}).appendTo("#"+stateId);
                                }else{
                                    $("<option />", {val: obj[i].code, text: obj[i].name}).appendTo("#"+stateId);
                                }
                            }
                        }else{
                            $("#"+stateId).replaceWith('<input type="text" id="'+stateId+'" name="state" data-required="false" maxlength="64" value="'+stateCode+'" />');
                        }
                    }else if(type == 'frontend'){
                        if(obj.length > 1){
                            $("#"+stateId).replaceWith('<select class="form-control" id="'+stateId+'" name="state"></select>');
                            $("#"+stateId).empty();
                            // add empty option
                            $("<option />", {val: "", text: "--"}).appendTo("#"+stateId);
                            for(var i = 1; i < obj.length; i++){
                                if(obj[i].code == stateCode && stateCode != ''){
                                    $("<option />", {val: obj[i].code, text: obj[i].name, selected: true}).appendTo("#"+stateId);
                                }else{
                                    $("<option />", {val: obj[i].code, text: obj[i].name}).appendTo("#"+stateId);
                                }
                            }
                        }else{
                            $("#"+stateId).replaceWith('<input type="text" class="form-control" id="'+stateId+'" name="state" data-required="false" maxlength="64" value="'+stateCode+'" />');
                        }
                    }
                }else{
                    if(globalDebug){
                        console.error("An error occurred while receiving data!");
                    }
                }
            }catch(err){
                if(globalDebug){
                    console.error(err);
                }
            }
        }
    });

    return ajax;
}

/**
 * Validates form fields and submit the form
 * @param el form element
 * @param formName
 * @param typeAccount (members)
 */
function registration(el, formName, typeAccount)
{
    if(el == null || jQuery(el).hasClass('hover') || formName == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var token = $(el).closest('form').find('input[name=APPHP_CSRF_TOKEN]').val();
    var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,7})+$/;

    var firstName       = $('#'+formName+'_first_name').val();
    var lastName        = $('#'+formName+'_last_name').val();
    var gender          = $('#'+formName+'_gender').val();
    var birthDate       = $('#'+formName+'_birth_date').val();
    var website         = $('#'+formName+'_website').val();
    var company         = $('#'+formName+'_company').val();
    var phone           = $('#'+formName+'_phone').val();
    var fax             = $('#'+formName+'_fax').val();
    var address         = $('#'+formName+'_address').val();
    var address2        = $('#'+formName+'_address_2').val();
    var city            = $('#'+formName+'_city').val();
    var zipCode         = $('#'+formName+'_zip_code').val();
    var countryCode     = $('#'+formName+'_country_code').val();
    var state           = $('#'+formName+'_state').val();
    var email           = $('#'+formName+'_email').val();
    var username        = $('#'+formName+'_username').val();
    var password        = $('#'+formName+'_password').val();
    var confirmPassword = $('#'+formName+'_confirm_password').val();
    var languageCode    = $('#'+formName+'_language_code').val();
    var notifications   = $('#'+formName+'_notifications:checked').val();
    var iAgree          = $('#'+formName+'_i_agree:checked').val();
    var objCaptcha      = $('#'+formName+'_captcha');
    var captcha         = $('#'+formName+'_captcha').val();

    var firstNameRequired       = $('#'+formName+'_first_name').data('required') == 1 ? true : false;
    var lastNameRequired        = $('#'+formName+'_last_name').data('required') == 1 ? true : false;
    var genderRequired          = $('#'+formName+'_gender').data('required') == 1 ? true : false;
    var birthDateRequired       = $('#'+formName+'_birth_date').data('required') == 1 ? true : false;
    var websiteRequired         = $('#'+formName+'_website').data('required') == 1 ? true : false;
    var companyRequired         = $('#'+formName+'_company').data('required') == 1 ? true : false;
    var phoneRequired           = $('#'+formName+'_phone').data('required') == 1 ? true : false;
    var faxRequired             = $('#'+formName+'_fax').data('required') == 1 ? true : false;
    var addressRequired         = $('#'+formName+'_address').data('required') == 1 ? true : false;
    var address2Required        = $('#'+formName+'_address_2').data('required') == 1 ? true : false;
    var cityRequired            = $('#'+formName+'_city').data('required') == 1 ? true : false;
    var zipCodeRequired         = $('#'+formName+'_zip_code').data('required') == 1 ? true : false;
    var countryCodeRequired     = $('#'+formName+'_country_code').data('required') == 1 ? true : false;
    var stateRequired           = $('#'+formName+'_state').data('required') == 1 ? true : false;
    var emailRequired           = $('#'+formName+'_email').data('required') == 1 ? true : false;
    var confirmPasswordRequired = $('#'+formName+'_confirm_password').data('required') == 1 ? true : false;
    var languageCodeRequired    = $('#'+formName+'_language_code').data('required') == 1 ? true : false;

    $('.alert-error').hide();
    $('#message_success').hide();
    $('#message_info').hide();
    $('#message_error').hide();

    if(firstNameRequired && !firstName){
        $('#'+formName+'_first_name').focus();
        $('#first_name_alert').show();
        scrollTo('#first_name_alert');
    }else if(lastNameRequired && !lastName){
        $('#'+formName+'_last_name').focus();
        $('#last_name_alert').show();
        scrollTo('#last_name_alert');
    }else if(genderRequired && !gender){
        $('#'+formName+'_gender').focus();
        $('#gender_alert').show();
        scrollTo('#gender_alert');
    }else if(birthDateRequired && !birthDate){
        $('#'+formName+'_birth_date').focus();
        $('#birth_date_alert').show();
        scrollTo('#birth_date_alert');
    }else if(websiteRequired && !website){
        $('#'+formName+'_website').focus();
        $('#website_alert').show();
        scrollTo('#website_alert');
    }else if(companyRequired && !company){
        $('#'+formName+'_company').focus();
        $('#company_alert').show();
        scrollTo('#company_alert');
    }else if(phoneRequired && !phone){
        $('#'+formName+'_phone').focus();
        $('#phone_alert').show();
        scrollTo('#phone_alert');
    }else if(faxRequired && !fax){
        $('#'+formName+'_fax').focus();
        $('#fax_alert').show();
        scrollTo('#fax_alert');
    }else if(addressRequired && !address){
        $('#'+formName+'_address').focus();
        $('#address_alert').show();
        scrollTo('#address_alert');
    }else if(address2Required && !address2){
        $('#'+formName+'_address_2').focus();
        $('#address_2_alert').show();
        scrollTo('#address_2_alert');
    }else if(cityRequired && !city){
        $('#'+formName+'_city').focus();
        $('#city_alert').show();
        scrollTo('#city_alert');
    }else if(zipCodeRequired && !zipCode){
        $('#'+formName+'_zip_code').focus();
        $('#zip_code_alert').show();
        scrollTo('#zip_code_alert');
    }else if(countryCodeRequired && !countryCode){
        $('#'+formName+'_country_code').focus();
        $('#country_code_alert').show();
        scrollTo('#country_code_alert');
    }else if(stateRequired && !state){
        $('#'+formName+'_state').focus();
        $('#state_alert').show();
        scrollTo('#state_alert');
    }else if(emailRequired && !email){
        $('#'+formName+'_email').focus();
        $('#email_alert').show();
        scrollTo('#email_alert');
    }else if(email && !re.test(email)){
        $('#'+formName+'_email').focus();
        $('#email_alert_valid').show();
        scrollTo('#email_alert_valid');
    }else if(!username){
        $('#'+formName+'_username').focus();
        $('#username_alert').show();
        scrollTo('#username_alert');
    }else if(!password){
        $('#'+formName+'_password').focus();
        $('#password_alert').show();
        scrollTo('#password_alert');
    }else if(confirmPasswordRequired && !confirmPassword){
        $('#'+formName+'_confirm_password').focus();
        $('#confirm_password_alert').show();
        scrollTo('#confirm_password_alert');
    }else if(confirmPasswordRequired && confirmPassword != password){
        $('#'+formName+'_confirm_password').focus();
        $('#confirm_password_alert_equal').show();
        scrollTo('#confirm_password_alert_equal');
    }else if(languageCodeRequired && !languageCode){
        $('#'+formName+'_language_code').focus();
        $('#language_code_alert').show();
        scrollTo('#language_code_alert');
    }else if(!iAgree){
        $('#'+formName+'_i_agree').focus();
        $('#i_agree_alert').show();
        scrollTo('#i_agree_alert');
    }else if(objCaptcha.length > 0 && !captcha){
        $('#'+formName+'_captcha').focus();
        $('#'+formName+'_captcha_alert').show();
        scrollTo('#'+formName+'_captcha_alert');
    }else{
        $(el).val($(el).data('sending'));
        $(el).prop("disabled", true);

        $.ajax({
            url: typeAccount + '/ajaxRegistration',
            global: false,
            type: 'POST',
            data: ({
                APPHP_CSRF_TOKEN        : token,
                first_name              : firstName,
                last_name               : lastName,
                gender                  : gender,
                birth_date              : birthDate,
                website                 : website,
                company                 : company,
                phone                   : phone,
                fax                     : fax,
                address                 : address,
                address_2               : address2,
                city                    : city,
                zip_code                : zipCode,
                country_code            : countryCode,
                state                   : state,
                email                   : email,
                username                : username,
                password                : password,
                confirm_password        : confirmPassword,
                notifications           : notifications,
                language_code           : languageCode,
                captcha                 : captcha,
                i_agree                 : iAgree
            }),
            dataType: 'html',
            async: true,
            error: function(html){
                $('#message_error').show();
            },
            success: function(html){
                try{
                    var obj = $.parseJSON(html);
                    if(obj.status == '1'){
                        $('.alert-error').hide();
                        $('#'+formName+'_first_name').val('');
                        $('#'+formName+'_last_name').val('');
                        $('#'+formName+'_gender').val('');
                        $('#'+formName+'_birth_date').val('');
                        $('#'+formName+'_website').val('');
                        $('#'+formName+'_company').val('');
                        $('#'+formName+'_phone').val('');
                        $('#'+formName+'_fax').val('');
                        $('#'+formName+'_address').val('');
                        $('#'+formName+'_address_2').val('');
                        $('#'+formName+'_city').val('');
                        $('#'+formName+'_zip_code').val('');
                        $('#'+formName+'_country_code').val('');
                        $('#'+formName+'_state').val('');
                        $('#'+formName+'_email').val('');
                        $('#'+formName+'_username').val('');
                        $('#'+formName+'_password').val('');
                        $('#'+formName+'_confirm_password').val('');
                        $('#'+formName+'_language_code').val('');
                        $('#'+formName+'_captcha').val('');
                        $('#'+formName+'_notifications').attr('checkbox', '');
                        $('#'+formName+'_i_agree').attr('checkbox', '');
                        $('#'+formName).slideUp();
                        if(obj.error !== ''){
                            $('#message_success label').text(obj.error);
                        }
                        $('#message_success').show();
                        $('#message_info').show();
                        scrollTo('#message_success');
                    }else{
                        raiseError(el, formName, obj.error, obj.error_field);
                    }
                }catch(err){
                    raiseError(el, formName, err.message);
                }
            }
        });
    }
}

/**
 * Validates form fields and submit the form
 * @param el form element
 */
function restorePasswordForm(el)
{
    if(el == null) return false;
    // define this to prevent name overlapping
    var $ = jQuery;

    var frm = $(el).closest('form');
    var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,7})+$/;

    var email = $('#email').val();

    var valueEl = $(el).val();
    $(el).val($(el).data('sending'));
    $(el).prop("disabled", true);

    $('.alert').hide();
    $('#email_empty').hide();
    $('#email_valid').hide();

    if(!email){
        $(el).val(valueEl);
        $(el).prop("disabled", false);
        $('#email').focus();
        $('#email_empty').show();
    }else if(email && !re.test(email)){
        $(el).val(valueEl);
        $(el).prop("disabled", false);
        $('#email').focus();
        $('#email_valid').show();
    }else{
        $('#email_empty').hide();
        $('#email_valid').hide();
        frm.submit();
        return true;
    }
    // prevent the default form submission occurring
    return false;
}

/**
 * Raise error message
 * @param el
 * @param formName
 * @param errorDescription
 * @param errorField
 */
function raiseError(el, formName, errorDescription, errorField)
{
    $('.alert-error').hide();
    var messageErrorId = '#message_error';
    if(errorField !== null){
        if(errorField !== 'captcha') {
			$('#' + formName + '_' + errorField).focus();
			messageErrorId = '#' + errorField + '_alert';
		}
    }
    if(errorDescription !== null) $(messageErrorId).html(errorDescription);
    $(messageErrorId).show();

    $(el).val($(el).data('send'));
    $(el).prop("disabled", false);
    scrollTo(messageErrorId);
}

/**
 * Raise error message
 * @param el
 * @param auctionId
 */
function addToWatchList(el, auctionId)
{
    var disabledButton = el.prop("disabled") ? el.prop("disabled") : false;
    if(disabledButton) return false;

    var faIcon = $("#watchlist i");

    ajaxLoading(faIcon, 'replace');
    el.prop("disabled", true);

    var ajax = $.ajax({
        url: 'auctions/ajaxAddWatchlist',
        global: false,
        type: 'POST',
        data: ({
            auctionId: auctionId
        }),
        dataType: 'html',
        async: true,
        error: function(html){
            console.error("AJAX: cannot connect to the server or server response error!");
        },
        success: function(html){

            try{
                resetAjaxLoading(faIcon);
                faIcon.removeClass("loading");
                var obj = $.parseJSON(html);
                if(obj.status == "1"){

                    var elAddedWatchList        = 'v-emerald';
                    var faIconAddedWatchList    = 'fa fa-check';
                    var elRemovedWatchList      = 'v-peter-river';
                    var faIconRemovedWatchList  = 'fa fa-plus';
                    var typeOperation = (obj.operation !== null && obj.operation !== undefined) ? obj.operation : 'add';

                    if(typeOperation == 'add'){
                        el.removeClass(elRemovedWatchList);
                        faIcon.removeClass(faIconRemovedWatchList);
                        faIcon.addClass(faIconAddedWatchList);
                        el.addClass(elAddedWatchList);
                    }else if(typeOperation == 'remove'){
                        el.removeClass(elAddedWatchList);
                        faIcon.removeClass(faIconAddedWatchList);
                        faIcon.addClass(faIconRemovedWatchList);
                        el.addClass(elRemovedWatchList);
                    }
                    apAlert(obj.message,'success');
                    $('#watchlist span').text(obj.textButton);
                    el.prop("disabled", false);
                }else{
                    faIcon.addClass("fa fa-plus");
                    apAlert(obj.message,'error');
                    el.prop("disabled", false);
                    console.error("An error occurred while receiving data!");
                }
            }catch(err){
                faIcon.addClass("fa fa-plus");
                el.prop("disabled", false);
                console.error(err);
            }
        }
    });

    return ajax;
}

/**
 * Add shipping address for order
 * @param el
 * @return bool
 */
function auctions_addShippingAddress(el){
    if(el === null){
        return false;
    }

    var $ = jQuery;
    var data = {};
    var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,7})+$/;
    var formName = $(el).closest('form').attr('id');

    data.APPHP_CSRF_TOKEN = $(el).closest('form').find('input[name=APPHP_CSRF_TOKEN]').val();
    data.act = $(el).closest('form').find('input[name=act]').val();
    data.order_number = $(el).closest('form').find('input[name=order_number]').val();
    $(".error").hide();

    var sendAjax = false;

    var shippingAddress = $("#" + formName + "_address_id option:selected").val();
    if (shippingAddress == "") {
        $("#" + formName + "_address_id").focus();
        $("#shippingErrorEmptyAddressId").show();
    } else if (shippingAddress == "new_address") {
        data.new_address = 1;

        var firstName             = $("#" + formName + "_first_name").val();
        var isFirstNameRequired   = $("#" + formName + "_first_name").data("required");
        var lastName              = $("#" + formName + "_last_name").val();
        var isLastNameRequired    = $("#" + formName + "_last_name").data("required");
        var company               = $("#" + formName + "_company").val();
        var isCompanyRequired     = $("#" + formName + "_company").data("required");
        var address               = $("#" + formName + "_address").val();
        var isAddressRequired     = $("#" + formName + "_address").data("required");
        var address2              = $("#" + formName + "_address_2").val();
        var isAddress2Required    = $("#" + formName + "_address_2").data("required");
        var city                  = $("#" + formName + "_city").val();
        var isCityRequired        = $("#" + formName + "_city").data("required");
        var countryText           = $("#" + formName + "_country_code option:selected").text();
        var countryCode           = $("#" + formName + "_country_code").val();
        var isCountryCodeRequired = $("#" + formName + "_country_code").data("required");
        var state                 = $("#" + formName + "_state").val();
        var isStateRequired       = $("#" + formName + "_state").data("required");
        var zipCode               = $("#" + formName + "_zip_code").val();
        var isZipCodeRequired     = $("#" + formName + "_zip_code").data("required");
        var phone                 = $("#" + formName + "_phone").val();
        var isPhoneRequired       = $("#" + formName + "_phone").data("required");
        var fax                   = $("#" + formName + "_fax").val();
        var isFaxRequired         = $("#" + formName + "_fax").data("required");

        if (!firstName && isFirstNameRequired) {
            $("#" + formName + "_first_name").focus();
            $("#shippingErrorEmptyFirstName").show();
        } else if (!lastName && isLastNameRequired) {
            $("#" + formName + "_last_name").focus();
            $("#shippingErrorEmptyLastName").show();
        } else if (!phone && isPhoneRequired) {
            $("#" + formName + "_phone").focus();
            $("#shippingErrorEmptyPhone").show();
        } else if (!fax && isFaxRequired) {
            $("#" + formName + "_fax").focus();
            $("#shippingErrorEmptyFax").show();
        } else if (!company && isCompanyRequired) {
            $("#" + formName + "_country_code").focus();
            $("#shippingErrorEmptyCompany").show();
        } else if (!address && isAddressRequired) {
            $("#" + formName + "_address").focus();
            $("#shippingErrorEmptyAdderss").show();
        } else if (!address2 && isAddress2Required) {
            $("#" + formName + "_address_2").focus();
            $("#shippingErrorEmptyAdderss2").show();
        } else if (!city && isCityRequired) {
            $("#" + formName + "_city").focus();
            $("#shippingErrorEmptyCity").show();
        } else if (!zipCode && isZipCodeRequired) {
            $("#" + formName + "_zip_code").focus();
            $("#shippingErrorEmptyZipCode").show();
        } else if (!countryCode && isCountryCodeRequired) {
            $("#" + formName + "_country_code").focus();
            $("#shippingErrorEmptyCountryCode").show();
        } else if (!state && isStateRequired) {
            $("#" + formName + "_state").focus();
            $("#shippingErrorEmptyState").show();
        } else {
            data.first_name   = firstName;
            data.last_name    = lastName;
            data.company      = company;
            data.address      = address;
            data.address_2    = address2;
            data.city         = city;
            data.country_code = countryCode;
            data.state        = state;
            data.zip_code     = zipCode;
            data.phone        = phone;
            data.fax          = fax;
            sendAjax = true;
        }
    } else {
        data.new_address = 0;
        data.address_id = $("#" + formName + "_address_id option:selected").val();
        sendAjax = true;
    }

    if (sendAjax) {
        $(el).html($(el).data('sending'));
        $(el).addClass('hover');
        $(el).attr('disabled', 'disabled');

        $.ajax({
            url: 'members/ajaxAddShipmentAddressInOrder',
            global: false,
            type: 'POST',
            data: (data),
            dataType: 'html',
            async: true,
            error: function (html) {
                $('.error').hide();
                $('#messageError').show();
                $(el).html($(el).data('send'));
                $(el).removeClass('hover');
                $(el).attr('disabled', '');
            },
            success: function (html) {
                try {
                    var obj = $.parseJSON(html);
                    if (obj.status == '1') {
                        var selectOptionVal = $("#" + formName + "_address_id").val();
                        if (selectOptionVal == 'new_address') {
                            var addressText = '';
                            if (company != '') {
                                addressText = company + '; ';
                            } else if (firstName != '' || lastName != '') {
                                addressText = firstName + ' ' + lastName + '; ';
                            }
                            if (address != '') {
                                addressText = addressText + address + ', ';
                            }
                            if (city != '') {
                                addressText = addressText + city + ', ';
                            }
                            if (zipCode != '') {
                                addressText = addressText + zipCode + ', ';
                            }
                            if (state != '') {
                                addressText = addressText + state + ', ';
                            }
                            if (countryText != '') {
                                addressText = addressText + countryText + ';';
                            } else {
                                addressText[addressText.length - 2] = ';';
                            }
                            $("#" + formName + "_address_id").append("<option value='" + obj.addressId + "'>" + addressText + "</option>");
                            $("#" + formName + "_address_id").append("<option value='" + obj.addressId + "'>" + addressText + "</option>");
                            $("#" + formName + "_address_id option[value='" + obj.addressId + "']").prop("selected", true);
                        }
                        $('.error').hide();

                        $(el).html($(el).data('send'));
                        $(el).removeClass('hover');
                        $(el).removeAttr('disabled');

                        $('#shipping-new-address-form').slideUp();
                        $('#payment-button').slideDown();
                    } else {
                        $('#shippingMessageError').html(obj.message);
                        if (obj.error_field !== null) $("#" + formName + "_" + obj.error_field).focus();
                        $('#shippingMessageError').show();

                        $(el).html($(el).data('send'));
                        $(el).removeClass('hover');
                        $(el).removeAttr('disabled');
                    }
                } catch (err) {
                    $('#shippingMessageError').html(err.message);
                    $('#shippingMessageError').show();

                    $(el).html($(el).data('send'));
                    $(el).removeClass('hover');
                    $(el).removeAttr('disabled');
                }
            }
        });
    }
}

/**
 * Scroll To Element
 * @param idEl
 */
function scrollTo(idEl)
{
    if($(idEl).length){
        $('html, body').animate({ scrollTop: $(idEl).offset().top-300 }, 500);
    }
    // Prevent the default form submission occurring
    return false;
}

/**
 * Inserts ajax_loading.gif image
 * @param el
 * @param position (after|before|replace|append)
 */
function ajaxLoading(el, position)
{
    if(el.hasClass("loading")) return false;
    var existImage = $("img").is("#ajax_loading");
    if(existImage == false && !el.hasClass("loading")){
        var ajaxImage = '<img id="ajax_loading" src="templates/default/images/ajax_loading.gif" alt="loading" />';
        if(position == "after"){
            el.after(ajaxImage);
        }else if(position == "before"){
            el.before(ajaxImage);
        }else if(position == "replace"){
            el.append(ajaxImage);
            el.removeClass();
        }else if(position == "append"){
            el.append(ajaxImage);
        }
        el.addClass("loading");
    }else{
        console.log(el.hasClass("loading"));
    }
}

/**
 * Delete ajax_loading.gif image
 * @param el
 */
function resetAjaxLoading(el)
{
    if(el.hasClass("loading")){
        $("#ajax_loading").remove();
        el.removeClass("loading");
    }else{
        return false;
    }
}

/**
 * My alert
 * @param string message
 * @param string type
 * @return void
 */
function apAlert(message, type){
    if(message === null){
        return false;
    }
    type = type == null ? 'info' : type;

    // Init toastr
    // See: http://codeseven.github.io/toastr/demo.html
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    toastr[type](message);
}