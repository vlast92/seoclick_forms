jQuery(document).ready(function ($) {
    /*TODO сделать сообщения о валидации*/
    let inputs,
        sitePattern = "^[\\w \\.]+[\\.]{1}[\\D]{2,4}$",
        phonePattern = "^[\\d \\+]{1,5}[\\( \\d \\) \\s]{1,10}[-?\\d \\s]+$",
        emailPattern = "^[\\w \\.]+[@]{1}[\\w]+[\\.]{1}[\\D]{2,4}$",
        forms = $(".form-validate");

    $.each(forms, function (index, form) {

        //получаем валиируемые поля формы
        inputs = $(form).find(".validate");

        //вызываем функцию установки обработчиков на поля формы
        setEvents(inputs);
        //вешам обработчик отправки формы
        $(form).on("submit", sendData);
    });

    //функция установки обработчиков полей формы
    function setEvents(inputs) {

        $.each(inputs, function (index, input) {

            let name = $(input).attr("data-validate");

            //устанавливвваем шаблоны для полей формы
            switch (name) {
                case "site":
                    $(input).attr("pattern", sitePattern);
                    break;
                case "email":
                    $(input).attr("pattern", emailPattern);
                    break;
                case "phone":
                    $(input).attr("pattern", phonePattern);
                    $(input).on("paste", checkPasteText);
                    $(input).on("keypress", checkPressedKey);
                    break;
                default:
                    return true;
            }
            //устанавливаем обработчик на проверку вводимых данных
            $(input).on("focusin change keyup", function () {

                checkPattern($(this));
            });
        });
    }

    //функция проверки вставленные данные в поле формы по шаблону
    function checkPasteText(event) {
        console.log(event);
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

        let value = $(inputData).val(), pattern;

        pattern = getPattern(inputData);

        //проверка данных по шаблону
        if (!pattern.test(value)) {
            inputData.addClass("invalid");
            return false;
        } else {
            inputData.removeClass("invalid");
            return true;
        }
    }

    //Функция возвращает RegExp объект для переданного поля формы
    function getPattern(inputData) {

        let pattern;

        //создание RegExp объекта из шаблона
        switch ($(inputData).attr("data-validate")) {
            case "phone":
                pattern = new RegExp(phonePattern);
                break;
            case "email":
                pattern = new RegExp(emailPattern);
                break;
            case "site":
                pattern = new RegExp(sitePattern);
                break;
            default:
                return 0;
        }

        return pattern;
    }

    //функция отправки данных на сервер
    function sendData(event) {

        event.preventDefault();

        let form = $(this), capchaResponse, request, data,
            formContainer = form.closest(".seoclick-forms"),
            messageBox = formContainer.find(".message-container");

        data = form.serializeArray();
        data = objectifyFormData(data);

        messageBox.addClass("active");
        if($(formContainer).hasClass("seoclick-forms-popup-wrap")){
            $(formContainer).find(".container").animate({
                scrollTop: 0
            }, 300);
        }else{
            $('html, body').animate({
                scrollTop: formContainer.offset().top - 200
            }, 300);
        }

        capchaResponse = form.find(".g-recaptcha-response").val();
        if (capchaResponse === "") {
            messageBox.html("Не пройдена проверка 'Я не робот'");
            return false;
        }

        messageBox.html("Отправка...");
        // Формируем параметры запроса
        request = {
            'option': 'com_ajax', // Используем AJAX интерфейс
            'module': 'seoclick_forms', // Название модуля без mod_
            'format': 'json', // Формат возвращаемых данных
            'g-recaptcha-response': capchaResponse, //ответ капчи
            'data': data // данные формы
        };

        $.ajax({
            type: 'POST',
            data: request,

            success: function (respond) {

                messageBox.html(respond.data);
            },
            error: function (jqXHR, textStatus) {
                messageBox.html('Ошибка AJAX запроса: ' + textStatus);
            }
        });

        grecaptcha.reset();
        return true;
    }

    /*
    * Функция преобразования сериализованного массива
    * функция принимает сериализованый двухмерный массив данных
    * производит его преобразования из двухмерного массива
    * в одномерный и возвращает полученный массив
    */
    function objectifyFormData(formArray) {//преобразование массива

        let returnArray = {};
        for (let i = 0; i < formArray.length; i++) {
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    }
});
