$(function() {

    $(".action-show").click(function() {
        me = $(this);
        if (me.attr("state") == 1) {
            me.attr("state", 0);
            me.html(me.attr("hide"));
        } else {
            me.attr("state", 1);
            me.html(me.attr("show"));
        }

        $.ajax({
            url: '../certification/user_course_visibility.php',
            type: 'post',
            data: { "id": me.attr("data"), "visible": me.attr("state") },
            success: function() {

            }
        });

    });
});