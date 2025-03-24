$(document).ready(function () {
    // SUBLIST FUNCTIONAL START
    const solutionsLinkAndDropdown = $('#solutionsLink, #solutionsDropdown');
    const langBtnAndDropdown = $('#langBtn, #langDropdown');
    const mobileLangBtn = $('#mobileLangBtn');
    const solutionsDropdown = $('#solutionsDropdown');
    const langDropdown = $('#langDropdown');
    const mobileLangDropdown = $('#mobileLangDropdown');

    let timeout;

    function showDropdown(type) {
        clearTimeout(timeout);
        if (type === 'solutions') {
            solutionsDropdown.addClass('show_dropdown').removeClass('hide_dropdown');
        } else if (type === 'language') {
            langDropdown.addClass('show_dropdown').removeClass('hide_dropdown');
        } else if (type === 'mobileLanguage') {
            mobileLangDropdown.addClass('show_dropdown--top').removeClass('hide_dropdown--top');
        } else {
            console.log('Something went wrong!');
        }
    }

    function hideDropdown(type) {
        timeout = setTimeout(() => {
            if (type === 'solutions') {
                solutionsDropdown.removeClass('show_dropdown').addClass('hide_dropdown');
            } else if (type === 'language') {
                langDropdown.removeClass('show_dropdown').addClass('hide_dropdown');
            } else if (type === 'mobileLanguage') {
                mobileLangDropdown.removeClass('show_dropdown--top').addClass('hide_dropdown--top');
            } else {
                console.log('Something went wrong!');
            }
        }, 200);
    }

    solutionsLinkAndDropdown.hover(
        function () { showDropdown('solutions'); },
        function () { hideDropdown('solutions'); }
    );

    langBtnAndDropdown.hover(
        function () { showDropdown('language'); },
        function () { hideDropdown('language'); }
    );

    mobileLangBtn.click(function () {
        return mobileLangDropdown.hasClass('hide_dropdown--top') ? showDropdown('mobileLanguage') : hideDropdown('mobileLanguage')
    })
    // SUBLIST FUNCTIONAL END

    // BURGER SUBLIST FUNCTIONAL START
    const burgerSublistBtn = $('.burgerSublistBtn');
    const burgerSublistInner = $('.burgerSublistInner');

    function showBurgerSublist (sublistTitle) {
        burgerSublistInner.each(function () {
            if ($(this).data('sublist-inner') === sublistTitle) {
                $(this).removeClass('hide_burger-sublist').addClass('show_burger-sublist');
            }
        })
    }

    function closeBurgerSublist (sublistTitle) {
        burgerSublistInner.each(function () {
            if ($(this).data('sublist-inner') === sublistTitle) {
                $(this).addClass('hide_burger-sublist').removeClass('show_burger-sublist');
            }
        })
    }

    function closeAddBurgerSublist () {
        burgerSublistInner.addClass('hide_burger-sublist').removeClass('show_burger-sublist');
    }

    burgerSublistBtn.each(function () {
        $(this).click(function () {
            if (!$(this).hasClass('active_burger-sublist--btn')) {
                $(this).addClass('active_burger-sublist--btn').children().last().addClass('rotate-180');
                showBurgerSublist($(this).data('sublist-title'));
            } else {
                $(this).removeClass('active_burger-sublist--btn').children().last().removeClass('rotate-180');
                closeAddBurgerSublist();
                closeBurgerSublist($(this).data('sublist-title'));
            }
        })
    })
    // BURGER SUBLIST FUNCTIONAL END

    // DOCUMENT BODY CHANGES START
    const body = $('body');
    function stickyBody() {
        let isMobile = window.innerWidth < 769;
        body.css({ height: '100vh', overflow: 'hidden', paddingRight: isMobile ? 0 : 15 });
    }
    function scrollBody() {
        body.css({ height: '100%', overflow: 'auto', paddingRight: '0' });
    }
    // DOCUMENT BODY CHANGES END

    // BURGER MENU FUNCTIONAL START
    const burgerMenuShow = $('#burgerMenuShow');
    const burgerMenuClose = $('#burgerMenuClose');
    const burgerMenuInner = $('#burgerMenu');

    burgerMenuShow.click(showBurgerMenu);
    burgerMenuClose.click(closeBurgerMenu);
    function showBurgerMenu () {
        burgerMenuClose.addClass('show_burger-btn').removeClass('hide_burger-btn');
        burgerMenuShow.removeClass('show_burger-btn').addClass('hide_burger-btn');
        burgerMenuInner.removeClass('hide_burger-menu').addClass('show_burger-menu');
        stickyBody();
    }

    function closeBurgerMenu () {
        burgerMenuClose.addClass('hide_burger-btn').removeClass('show_burger-btn');
        burgerMenuShow.removeClass('hide_burger-btn').addClass('show_burger-btn');
        burgerMenuInner.addClass('hide_burger-menu').removeClass('show_burger-menu');
        scrollBody();
    }
    // BURGER MENU FUNCTIONAL END

    // GET DEMO MODAL FUNCTIONAL START
    const getDemoModalWrap = $('#getDemoModalWrap');
    const getDemoModalShow = $('.getDemoModalShow');
    const getDemoModalClose = $('#getDemoModalClose');
    const getDemoModalInner = $('#getDemoModalInner');

    getDemoModalWrap.click(function (e) {
        if (e.target.id === 'getDemoModalWrap') closeGetDemoModal()
    })
    getDemoModalShow.each(function () {
        $(this).click(showGetDemoModal)
    })
    getDemoModalClose.click(closeGetDemoModal)

    function showGetDemoModal () {
        getDemoModalWrap.removeClass('hide_modal--wrap').addClass('show_modal--wrap');
        getDemoModalInner.removeClass('hide_modal').addClass('show_modal');
        stickyBody();
    }

    function closeGetDemoModal () {
        getDemoModalWrap.removeClass('show_modal--wrap').addClass('hide_modal--wrap');
        getDemoModalInner.removeClass('show_modal').addClass('hide_modal');
        scrollBody();
    }
    // GET DEMO MODAL FUNCTIONAL END

    // MESSAGE SENT MODAL FUNCTIONAL START
    const messageSentModalWrap = $('#messageSentModalWrap');
    const messageSentModalShow = $('.messageSentModalShow');
    const messageSentModalClose = $('.messageSentModalClose');
    const messageSentModalInner = $('#messageSentModalInner');

    messageSentModalWrap.click(function (e) {
        if (e.target.id === 'messageSentModalWrap') closeMessageSentModal()
    })
    messageSentModalShow.each(function () {
        $(this).click(showMessageSentModal)
    })
    messageSentModalClose.click(closeMessageSentModal)

    function showMessageSentModal () {
        closeGetDemoModal()
        messageSentModalWrap.removeClass('hide_modal--wrap').addClass('show_modal--wrap');
        messageSentModalInner.removeClass('hide_modal').addClass('show_modal');
        stickyBody();
    }

    function closeMessageSentModal () {
        messageSentModalWrap.removeClass('show_modal--wrap').addClass('hide_modal--wrap');
        messageSentModalInner.removeClass('show_modal').addClass('hide_modal');
        scrollBody();
    }
    // MESSAGE SENT MODAL FUNCTIONAL END

    // BLOGS CAROUSEL START
    const blogsCarouselTrack = $(".carousel-track");
    const blogsCarouselItem = $(".carousel-item");

    let currentBlogsIndex = 0;
    let blogsToShow = 3;
    const totalBlogs = blogsCarouselItem.length;
    let blogWidth = blogsCarouselItem.outerWidth(true) + 20;

    function updateItemsToShow() {
        const windowWidth = $(window).width();

        if (windowWidth < 640) {
            blogsToShow = 1; // Мобильные экраны
        } else if (windowWidth < 1024) {
            blogsToShow = 2; // Планшеты
        } else {
            blogsToShow = 3; // Десктоп
        }

        document.documentElement.style.setProperty("--blogs-to-show", blogsToShow);
        blogWidth = blogsCarouselItem.outerWidth(true);
        updateCarousel();
    }

    function updateCarousel() {
        const newTransform = -(currentBlogsIndex * (blogWidth)) + "px";
        blogsCarouselTrack.css("transform", `translateX(${newTransform})`);
    }

    $("#blogs-carousel .next").click(function () {
        if (currentBlogsIndex < totalBlogs - blogsToShow) {
            currentBlogsIndex++;
            updateCarousel();
        }
    });

    $("#blogs-carousel .prev").click(function () {
        if (currentBlogsIndex > 0) {
            currentBlogsIndex--;
            updateCarousel();
        }
    });

    $(window).resize(updateItemsToShow);

    updateItemsToShow(); // Вызов при загрузке страницы
    // BLOGS CAROUSEL END

    // BLOGS TAGS FUNCTIONAL START
    const blogsTagsItems = $('#blogsTags').children();
    const blogTagActive = 'bg-secondary border-secondary text-white';
    const blogTagInactive = 'bg-white border-secondary text-secondary';

    blogsTagsItems.click(function () {
        const activeItems = blogsTagsItems.filter('.bg-secondary');

        if ($(this).hasClass('bg-secondary')) {
            if (activeItems.length > 1) {
                $(this).removeClass(blogTagActive).addClass(blogTagInactive);
            }
        } else {
            $(this).addClass(blogTagActive).removeClass(blogTagInactive);
        }
    });
    // BLOGS TAGS FUNCTIONAL END
});
