$(function () {
    $("#include-footer").load("footer.html");
});

$(document).ready(function () {
    let current = 0;
    const slides = $('#slideshow .slide');
    const dots = $('.slide-dots .dot');
    const total = slides.length;
    let timer;

    function showSlide(idx) {
        slides.removeClass('active').css('opacity', 0);
        $(slides[idx]).addClass('active').css('opacity', 1);
        dots.removeClass('active');
        $(dots[idx]).addClass('active');
    }

    function nextSlide() {
        current = (current + 1) % total;
        showSlide(current);
    }

    function prevSlide() {
        current = (current - 1 + total) % total;
        showSlide(current);
    }

    function goToSlide(idx) {
        current = idx;
        showSlide(current);
    }

    $('.slide-btn.next').click(function () {
        nextSlide();
        resetTimer();
    });

    $('.slide-btn.prev').click(function () {
        prevSlide();
        resetTimer();
    });

    dots.each(function (i) {
        $(this).click(function () {
            goToSlide(i);
            resetTimer();
        });
    });

    function startTimer() {
        timer = setInterval(nextSlide, 4000);
    }

    function resetTimer() {
        clearInterval(timer);
        startTimer();
    }

    showSlide(current);
    startTimer();
});
