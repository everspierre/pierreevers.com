var firstScrollSpyEl = document.querySelector('[data-bs-spy="scroll"]');
firstScrollSpyEl.addEventListener('activate.bs.scrollspy', function () {
    var element = $(this).find("a.active").attr("href");
    console.log(element);
    if (element == '#accueil') {
        $(".navbar").removeClass("fixed-top").addClass("fixed-bottom");
        $(".navbar").animate({opacity: 0.5}, 500);
    } else {
        $(".navbar").removeClass("fixed-bottom").addClass("fixed-top");
        $(".navbar").animate({opacity: 1}, 500);
    }
});
