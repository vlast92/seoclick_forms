let renderRecaptcha = function () {
    jQuery(function ($) {
        $.each($('.g-recaptcha.seoclick:not(.invisible-recaptcha)'), function (index, captcha) {
            var widgetId = grecaptcha.render(captcha);

            $(captcha).data('recaptcha-widget-id', widgetId);
        });
    });
};

function submitSeoclickForm(token) {

    var module_id = getCookie('seoclick_send_form_id');

    deleteCookie('seoclick_send_form_id');
    jQuery(`#${module_id} form`).submit();
}

// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};

    let expires = options.expires;

    if (typeof expires === "number" && expires) {
        let d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    let updatedCookie = name + "=" + value;

    for (let propName in options) {
        updatedCookie += "; " + propName;
        let propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

function deleteCookie(name) {
    setCookie(name, "", {
        expires: -1
    })
}

jQuery(document).ready(function ($) {

    let inputs,
        sitePattern = "^[\\w \\W]+[\\.]{1}[\\D]{2,4}$",
        phonePattern = "^[\\+ \\- \\( \\d \\) \\s]{7,}$",
        emailPattern = "^[\\w \\W]+[@]{1}[\\w \\W]+[\\.]{1}[\\D]{2,4}$",
        forms = $(".seoclick-forms .form-validate"),
        default_site_tooltip_text = mod_seoclick_forms_language_variables.default_site_tooltip_text,
        default_email_tooltip_text = mod_seoclick_forms_language_variables.default_email_tooltip_text,
        default_phone_tooltip_text = mod_seoclick_forms_language_variables.default_phone_tooltip_text;

    createCustomFileInputs();

    $.each(forms, function (index, form) {

        //получаем валиируемые поля формы
        inputs = $(form).find(".validate");

        //вызываем функцию установки обработчиков на поля формы
        setEvents(inputs);
        //вешам обработчик отправки формы
        $(form).on("submit", validateForm);
    });

    function createCustomFileInputs() {

        $.each($('.seoclick-forms .file-input'), function (index, input) {

            var customInput = $(input).siblings('.custom-fileinput'),
                customFileList = $(input).siblings('.custom-filelist');

            customInput.click(function (e) {

                e.preventDefault();
                $(input).trigger('click');
            });
            $(input).change(function () {

                var filesList = input.files,
                    filesNames = '';

                switch (filesList.length) {

                    case 0:
                        customFileList.text(customFileList.data('nofile'));
                        break;
                    case 1:
                        customFileList.text(customFileList.data('file') + filesList[0].name);
                        break;
                    default:
                        customFileList.text(customFileList.data('files') + ' ' + filesList.length);
                        for (var i = 0; i < filesList.length; i++) {
                            filesNames += filesList[i].name;
                            if (i + 1 !== filesList.length) filesNames += ', '
                        }
                        customFileList.attr('title', filesNames);
                }
            });
        });
    }

    /*
    * Функция проверяет атрибуты pattern и data-tooltip
    * и в случае их отсутсвия присваивает им значения
    * по умолчанию при их наличии и возвращает true
    * иначе false
    * */
    function checkInputPatterns(input, def_pattern, def_tooltip_text) {

        let pattern = input.attr("pattern"),
            tooltip = input.data("tooltip"),
            response = {pattern: false, tooltip: false};

        if (pattern === undefined) {
            if (def_pattern !== undefined) {
                input.attr("pattern", def_pattern);
                response.pattern = true;
            } else {
                response.pattern = false;
            }
        } else {
            response.pattern = true;
        }

        if (tooltip === undefined) {
            if (def_tooltip_text !== undefined) {
                input.data("tooltip", def_tooltip_text);
                response.tooltip = true;
            } else {
                response.tooltip = false;
            }
        } else {
            response.tooltip = true;
        }

        return response;
    }

    /*
    * Функция управления подсказками
    * */
    function tooltip(input, command) {

        let container = input.closest('.form-validate');

        switch (command) {
            case 'create':
                input.after("<div class='seoclick-tooltip'><div>" + input.data("tooltip") + "</div></div>");
                input.next().hide();
                break;
            case 'show':
                input.next().show();
                input.next().css({
                    top: input[0].getBoundingClientRect().bottom - container[0].getBoundingClientRect().top + 8,
                    left: input[0].getBoundingClientRect().left - container[0].getBoundingClientRect().left
                });
                input.next().animate({
                    opacity: 1
                }, 300, () => {
                    input.next().show();
                });
                break;
            case 'hide':
                input.next().animate({
                    opacity: 0
                }, 300, () => {
                    input.next().hide();
                });
                break;
        }
    }

    //функция установки обработчиков полей формы
    function setEvents(inputs) {

        $.each(inputs, function (index, input) {

            let input_obj = $(input),
                data_type = input_obj.attr("data-validate"),
                validation;

            //устанавливвваем шаблоны для полей формы
            switch (data_type) {
                case "site":
                    validation = checkInputPatterns(input_obj, sitePattern, default_site_tooltip_text);
                    break;
                case "email":
                    validation = checkInputPatterns(input_obj, emailPattern, default_email_tooltip_text);
                    break;
                case "phone":
                    validation = checkInputPatterns(input_obj, phonePattern, default_phone_tooltip_text);
                    input_obj.on("paste", checkPasteText);
                    input_obj.on("keypress", checkPressedKey);
                    break;
                default:
                    validation = checkInputPatterns(input_obj);
                    if (!validation.tooltip && !validation.pattern) return true;
            }
            if (validation.tooltip) {
                tooltip(input_obj, 'create');
                input_obj.on("mouseenter", function () {
                    tooltip(input_obj, 'show');
                });
                input_obj.on("focusout mouseleave", function () {

                    tooltip(input_obj, 'hide');
                });
            }
            //устанавливаем обработчик на проверку вводимых данных
            if (validation.pattern) {
                input_obj.on("change keyup", function () {

                    let check = checkPattern($(this));

                    if (validation.tooltip) {
                        if (!check) {
                            tooltip(input_obj, 'show');
                        } else {
                            tooltip(input_obj, 'hide');
                        }
                    }
                });
                input_obj.on("focusout mouseleave", function () {

                    if (input_obj.val() === '') {
                        input_obj.removeClass('invalid');
                    }
                });
            }
        });
    }

    //функция проверки вставленные данные в поле формы по шаблону
    function checkPasteText(event) {
        let pattern,
            data = event.originalEvent.clipboardData.getData('text/plain');

        pattern = getPattern($(this));

        //проверка данных по шаблону
        if (!pattern.test(data)) {
            event.preventDefault();
        }
    }

    /*
    * функция проверки нажатой клавиши
    * Функция возвращет false на все клавиши,
    * кроме цифр, знаков + - ( ) и пробелов
    */
    function checkPressedKey(event) {

        let e = event;

        if (e.ctrlKey || e.altKey || e.metaKey) {
            return;
        }

        let chr = getChar(e);

        // с null надо осторожно в неравенствах, т.к. например null >= '0' => true!
        // на всякий случай лучше вынести проверку chr = null отдельно
        if (chr === null) {
            return;
        }

        if (chr < '0' || chr > '9') {
            if (chr !== "+" && chr !== "-" && chr !== "(" && chr !== ")" && chr !== " ") {
                return false;
            }
        }
    }

    //функция возвращает код символа
    function getChar(event) {

        if (event.code === "Space" || event.code === "Equal" || event.code === "Digit9" || event.code === "Digit0") {
            return 0;
        }
        if (event.which === null) { // IE
            if (event.keyCode < 32) return null; // спец. символ
            return String.fromCharCode(event.keyCode)
        }

        if (event.which !== 0 && event.charCode !== 0) { // все кроме IE
            if (event.which < 32) return null; // спец. символ
            return String.fromCharCode(event.which); // остальные
        }

        return null; // спец. символ
    }

    /*
    * Функция проверки введенных данных по шаблону
    * возвращает false если проверка не пройдена
    */
    function checkPattern(inputData) {

        let value = inputData.val(), pattern;

        pattern = new RegExp(inputData.attr("pattern"));

        //проверка данных по шаблону
        if (!pattern.test(value)) {
            inputData.addClass("invalid");
            return false;
        } else {
            inputData.removeClass("invalid");
            return true;
        }
    }

    //Валидация вложений формы
    function checkFiles(files, form) {


        let responce = {message: "", valid: true};

        $.each(files, function (index, file) {

            let file_size = file.size / 1000, max_size = form.find(".file-input").data("size");

            if (file_size > max_size) {

                responce.message = mod_seoclick_forms_language_variables.file_size_error.file_size_error_text_1 + file.name + mod_seoclick_forms_language_variables.file_size_error.file_size_error_text_2 + max_size + mod_seoclick_forms_language_variables.file_size_error.file_size_error_text_3 +

                    Math.round(file_size) + mod_seoclick_forms_language_variables.file_size_error.file_size_error_text_4;
                responce.valid = false;

                return responce;
            }
        });

        return responce;
    }

    //Валидация формы
    function validateForm(event) {

        event.preventDefault();

        let data,
            formData = new FormData(),
            form = $(this),
            formParams = window[form.data("moduleid")],
            formContainer = form.closest(".seoclick-forms"),
            messageBox = formContainer.find(".message-container"),
            files_data = form.find('.file-input')[0],
            recaptcha = form.find('.g-recaptcha.seoclick'),
            recaptchaResponce;

        if (Number(formParams.recaptchaEnabled)) {

            if (formParams.recaptchaType === 'invisible') {
                formParams.captchaWidgetID = recaptcha.data("recaptcha-widget-id");
                if(typeof formParams.captchaWidgetID == "undefined")
                {
                    let widgetId = grecaptcha.render(recaptcha[0]);

                    recaptcha.data('recaptcha-widget-id', widgetId);
                    formParams.captchaWidgetID = widgetId;
                }
                if(recaptcha.children().length === 0){
                    grecaptcha.reset(formParams.captchaWidgetID);
                }

                recaptchaResponce = grecaptcha.getResponse(formParams.captchaWidgetID);
                if (recaptchaResponce === "") {
                    messageBox.addClass("active");
                    messageBox.html(mod_seoclick_forms_language_variables.recaptcha_check_text);
                    setCookie("seoclick_send_form_id", form.data("moduleid"));
                    grecaptcha.execute(formParams.captchaWidgetID);

                    return false;
                }
            } else {
                formParams.captchaWidgetID = form.find(".g-recaptcha").data("recaptcha-widget-id");
                recaptchaResponce = grecaptcha.getResponse(formParams.captchaWidgetID);
                if (recaptchaResponce === "") {
                    messageBox.addClass("active");
                    messageBox.html(mod_seoclick_forms_language_variables.captcha_validation_error);

                    return false;
                }
            }
        }
        messageBox.addClass("active");
        if (files_data !== undefined) {

            if ($(files_data).next('.custom-fileinput').data('required') && files_data.files.length === 0) {
                messageBox.html(mod_seoclick_forms_language_variables.no_files_error);
                return 0;
            }
            files_data = files_data.files;

            let check = checkFiles(files_data, form);

            if (!check.valid) {
                messageBox.html(check.message);
                return 0;
            }
            for (let i = 0; i < files_data.length; i++) {
                formData.append("file_" + i, files_data[i]);
            }
        }

        data = form.serializeArray();
        $.each(data, function (key, input) {
            formData.append(input.name, input.value);
        });

        //Добавляем параметры запроса
        formData.append('option', 'com_ajax');
        formData.append('module', 'seoclick_forms');
        formData.append('format', 'json');
        formData.append('g-recaptcha-response', recaptchaResponce);

        //Прокрутка окна браузера до окна сообщений формы
        if ($(formContainer).hasClass("seoclick-forms-popup-wrap")) {
            $(formContainer).find(".container").animate({
                scrollTop: 0
            }, 300);
        } else {
            $('html, body').animate({
                scrollTop: formContainer.offset().top - 200
            }, 300);
        }

        sendData(formData, formParams, messageBox);
    }

    //функция отправки данных на сервер
    function sendData(formData, formParams, messageBox) {

        messageBox.html(mod_seoclick_forms_language_variables.sending_text);

        let metricsCode = false;

        if(formData.has('metrics_code'))
        {
            metricsCode = formData.get('metrics_code');
            formData.delete('metrics_code');
        }else if(Boolean(formParams.metricsCode)){
            metricsCode = formParams.metricsCode;
        }

        $.ajax({
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (respond) {

                if(metricsCode)
                {
                    eval(metricsCode);
                }
                if (respond.message !== null) {
                    messageBox.html(respond.message);
                } else {
                    messageBox.html(respond.data);
                }
            },
            error: function (jqXHR, textStatus) {
                messageBox.html('AJAX error: ' + textStatus);
            },
            complete: function () {
                if(Number(formParams.recaptchaEnabled)){
                    if (formParams.recaptchaType === 'invisible')
                    {
                        $('.g-recaptcha.seoclick.invisible-recaptcha').html('');
                    }else{
                        grecaptcha.reset(formParams.captchaWidgetID);
                    }
                }
            }
        });
    }
});