document.addEventListener("DOMContentLoaded", function () {
    // STICKY BODY FUNCTIONAL
    function stickyBody() {
        body.classList.add('no-scroll');
    }
    function scrollBody() {
        body.classList.remove('no-scroll');
    }

    // DROPDOWNS FUNCTIONAL

    const dropdownMap = {
        solutions: {
            trigger: document.querySelectorAll('#solutionsLink, #solutionsDropdown'),
            dropdown: document.querySelector('#solutionsDropdown')
        },
        // language: {
        //     trigger: document.querySelectorAll('#langBtn, #langDropdown'),
        //     dropdown: document.querySelector('#langDropdown')
        // },
        // mobileLanguage: {
        //     trigger: document.querySelector('#mobileLangBtn'),
        //     dropdown: document.querySelector('#mobileLangDropdown')
        // }
    };

    let timeout;

    function showDropdown(type) {
        clearTimeout(timeout);
        dropdownMap[type].dropdown.classList.add('show_dropdown');
        dropdownMap[type].dropdown.classList.remove('hide_dropdown');
    }

    function hideDropdown(type) {
        timeout = setTimeout(() => {
            dropdownMap[type].dropdown.classList.add('hide_dropdown');
            dropdownMap[type].dropdown.classList.remove('show_dropdown');
        }, 200);
    }

    // Наведение (десктоп)
    [
        'solutions',
        // 'language'
    ].forEach(type => {
        dropdownMap[type].trigger.forEach(el => {
            el.addEventListener('mouseenter', () => showDropdown(type));
            el.addEventListener('mouseleave', () => hideDropdown(type));
        });
    });

    // Клик (мобильный)
    // dropdownMap.mobileLanguage.trigger?.addEventListener('click', () => {
    //     dropdownMap.mobileLanguage.dropdown.classList.toggle('show_dropdown');
    //     dropdownMap.mobileLanguage.dropdown.classList.toggle('hide_dropdown');
    // });

    // BURGER MENU FUNCTIONAL START
    const burgerMenuShow = document.querySelector('#burgerMenuShow');
    const burgerMenuClose = document.querySelector('#burgerMenuClose');
    const burgerMenuInner = document.querySelector('#burgerMenu');
    const body = document.body;

    const burgerButtons = [burgerMenuShow, burgerMenuClose];

    function toggleBurgerMenu() {
        const isMenuOpen = burgerMenuInner.classList.toggle('show_burger-menu');
        burgerMenuInner.classList.toggle('hide_burger-menu', !isMenuOpen);

        burgerMenuShow.classList.toggle('show_burger-btn', !isMenuOpen);
        burgerMenuShow.classList.toggle('hide_burger-btn', isMenuOpen);

        burgerMenuClose.classList.toggle('show_burger-btn', isMenuOpen);
        burgerMenuClose.classList.toggle('hide_burger-btn', !isMenuOpen);

        isMenuOpen ? stickyBody() : scrollBody();

        requestAnimationFrame(() => {
            body.style.paddingRight = isMenuOpen && window.innerWidth >= 769 ? '15px' : '0';
        });
    }

    burgerButtons.forEach(button => button.addEventListener('click', toggleBurgerMenu));

    // BURGER SUBLIST FUNCTIONAL
    const burgerSublistBtn = document.getElementById("burgerSublistBtn");
    const burgerSublistInner = document.getElementById("burgerSublistInner");

    function toggleSublist(button) {
        // Закрываем все вложенные списки
        if (burgerSublistInner.classList.contains('show_burger-sublist')) {
            burgerSublistInner.classList.remove("show_burger-sublist");
            burgerSublistInner.classList.add("hide_burger-sublist");
        } else {
            burgerSublistInner.classList.add("show_burger-sublist");
            burgerSublistInner.classList.remove("hide_burger-sublist");
        }

        // Переключаем активное состояние кнопки
        button.classList.toggle("active_burger-sublist--btn");
        button.querySelector(":scope > *:last-child").classList.toggle("rotate-180");
    }

    // Делегирование событий
    burgerSublistBtn.addEventListener("click", () => toggleSublist(burgerSublistBtn));

    // GET DEMO MODAL FUNCTIONAL START
    const getDemoModalWrap = document.querySelector('#getDemoModalWrap');
    const getDemoModalShow = document.querySelectorAll('.getDemoModalShow');
    const getDemoModalClose = document.querySelector('#getDemoModalClose');
    const getDemoModalInner = document.querySelector('#getDemoModalInner');

    function showGetDemoModal() {
        getDemoModalWrap.classList.add('show_modal--wrap');
        getDemoModalWrap.classList.remove('hide_modal--wrap');
        getDemoModalInner.classList.add('show_modal');
        getDemoModalInner.classList.remove('hide_modal');
        stickyBody();
    }

    function closeGetDemoModal() {
        getDemoModalWrap.classList.add('hide_modal--wrap');
        getDemoModalWrap.classList.remove('show_modal--wrap');
        getDemoModalInner.classList.add('hide_modal');
        getDemoModalInner.classList.remove('show_modal');
        scrollBody();
    }

    getDemoModalWrap.addEventListener("click", (e) => {
        if (e.target.id === 'getDemoModalWrap') closeGetDemoModal();
    });
    getDemoModalShow.forEach(el => el.addEventListener("click", showGetDemoModal));
    getDemoModalClose.addEventListener("click", closeGetDemoModal);

    // MESSAGE SENT MODAL FUNCTIONAL START
    const messageSentModalWrap = document.querySelector('#messageSentModalWrap');
    const messageSentModalClose = document.querySelectorAll('.messageSentModalClose');
    const messageSentModalInner = document.querySelector('#messageSentModalInner');

    function showMessageSentModal() {
        closeGetDemoModal();
        messageSentModalWrap.classList.add('show_modal--wrap');
        messageSentModalWrap.classList.remove('hide_modal--wrap');
        messageSentModalInner.classList.remove('hide_modal');
        stickyBody();
    }

    function closeMessageSentModal() {
        messageSentModalWrap.classList.add('hide_modal--wrap');
        messageSentModalWrap.classList.remove('show_modal--wrap');
        messageSentModalInner.classList.add('hide_modal');
        messageSentModalInner.classList.remove('show_modal');
        scrollBody();
    }

    messageSentModalWrap.addEventListener("click", (e) => {
        if (e.target.id === 'messageSentModalWrap') closeMessageSentModal();
    });
    messageSentModalClose.forEach(el => el.addEventListener("click", closeMessageSentModal));

    // BLOGS CAROUSEL
    const blogsCarouselTrack = document.getElementById('carousel-track');
    const blogsCarouselItems = document.querySelectorAll('.carousel-item');
    const blogsCarouselPrev = document.getElementById('carousel-prev');
    const blogsCarouselNext = document.getElementById('carousel-next');

    let currentBlogsIndex = 0;
    let blogsToShow = 3;
    const totalBlogs = blogsCarouselItems.length;
    let blogWidth = blogsCarouselItems[0]?.offsetWidth + 20;

    function updateCarousel() {
        const newTransform = -(currentBlogsIndex * blogWidth) + "px";
        if (blogsCarouselTrack) blogsCarouselTrack.style.transform = `translateX(${newTransform})`;
    }

    blogsCarouselPrev?.addEventListener('click', function () {
        if (currentBlogsIndex > 0) {
            currentBlogsIndex--;
            updateCarousel();
        }
    })

    blogsCarouselNext?.addEventListener('click', function () {
        if (currentBlogsIndex < totalBlogs - blogsToShow) {
            currentBlogsIndex++;
            updateCarousel();
        }
    })

    function updateItemsToShow() {
        const windowWidth = window.innerWidth;

        if (windowWidth < 640) {
            blogsToShow = 1; // Мобильные экраны
        } else if (windowWidth < 1024) {
            blogsToShow = 2; // Планшеты
        } else {
            blogsToShow = 3; // Десктоп
        }

        document.documentElement.style.setProperty("--blogs-to-show", blogsToShow);
        blogWidth = blogsCarouselItems[0]?.offsetWidth + 20;
        updateCarousel();
    }

    let resizeTimeout;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateItemsToShow, 150);
    });

    // Начальная настройка
    updateItemsToShow();

    // BLOGS TAGS FUNCTIONAL
    const blogsTagsItems = document.querySelectorAll('#blogsTags button');
    const blogTagActive = ['bg-secondary', 'border-secondary', 'text-white'];
    const blogTagInactive = ['bg-white', 'border-secondary', 'text-secondary'];

    blogsTagsItems.forEach(item => {
        item.addEventListener('click', function (e) {
            const activeItems = Array.from(blogsTagsItems).filter(el => el.classList.contains('bg-secondary'));

            if (item.classList.contains('bg-secondary')) {
                if (activeItems.length > 1) {
                    item.classList.remove(...blogTagActive);
                    item.classList.add(...blogTagInactive);
                }
            } else {
                item.classList.add(...blogTagActive);
                item.classList.remove(...blogTagInactive);
            }
        })
    })
});
