var ids = [];
var opened = true;

function parseNotification() {
    var allNotification = $(".notification-new");

    allNotification.each(function () {
        if(this.innerHTML == 1) {
            $("#new-notification").addClass("badge badge-important bubble-only");
            ids.push(this.id);
        }
    });

}

parseNotification();

$("#my-task-list").click(function () {

    if(opened && ids.length > 0) {
        $("#new-notification").removeClass("badge badge-important bubble-only");
        $.ajax({
           method: "POST",
           url: "/user/read_not",
           data:{'ids': ids}
        }).done(function (data) {

        });
        opened = false;
    }
});

$('.tiles-body').click(function () {
    window.location.href = "/user/active_orders";
});
