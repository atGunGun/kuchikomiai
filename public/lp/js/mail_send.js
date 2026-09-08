var url_ = '/contact_send.php';

var fName = '#contactForm';

$(function() {

    $("body").append('<div id="dialog" style="display: none;"></div>');

    $(fName).validationEngine('attach', {
        ajaxFormValidation: true,
        onBeforeAjaxFormValidation: submitForm
    });

    function submitForm() {

        $("#dialog").html('送信してよろしいですか？');

        $("#dialog").dialog({
            resizable: false,
            draggable: false,
            closeOnEscape: false,
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
            modal: true,
            title: '確認',
            width: 400,
            height: 400,
            buttons: {
                'OK': function() {
                    submitData();
                },
                '閉じる': function() {
                    $(this).dialog('close');
                }
            }
        });
    }

    function submitData() {

        var f = $(fName);
        var method_ = f.prop('method');
        var formdata = new FormData(f.get(0));

        $.ajax({
            url: url_,
            method: method_,
            type: 'POST',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,

            success: function(data) {

                $("#dialog").dialog({
                    buttons: {}
                });

                $("#dialog").html("送信完了しました。");

                setTimeout(function() {
                    compPage('/contact');
                }, 1000);
            }

        }).fail(function(data) {

            $("#dialog").dialog({
                buttons: {}
            });

            $("#dialog").html("送信失敗しました！");

            setTimeout(function() {
                compPage('/contact');
            }, 1000);
        });
    }

    function compPage(url) {
        location.href = url + '?sendFlg=1';
    }

});