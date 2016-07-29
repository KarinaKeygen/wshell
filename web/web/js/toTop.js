$("a[href='#top']").click(function() {
    $("html, body").animate({ scrollTop: 0 }, "fast");
    return false;
});