/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$("form#default_edit_restrictions")
        .ajaxForm(
                {
                    beforeSubmit: () => {
                        $("#message").html("");
                        $("#loading").show();
                    },
                    success: () => {
                        $("#loading").hide();
                        $("#message")
                                .html(`<span style="color:green;">
                        ${Translation.ChangesWasSaved}
                </span>`);
                    }
                });
