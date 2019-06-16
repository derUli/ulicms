$(function () {
    $("select#language").change(
            function () {
                var url = "index.php?action=motd&language="
                        + $("select#language option:selected").val();
                location.replace(url);
            });
    $("#motd_form").ajaxForm(
            {
                beforeSubmit: function (e) {
                    $("#message").html("");
                    $("#loading").show();
                },
                success: function (e) {
                    $("#loading").hide();
                    $("#message").html(
                            "<span style=\"color:green;\">"
                            + Translation.ChangesWasSaved + "</span>");
                    $("#loading").hide();
                }


            });
});