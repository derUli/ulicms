let hamburger = document.querySelector('.hamburger');
let menu = document.querySelector('nav.navbar');
let container = document.querySelector('.root');

menu.classList.remove('active');


hamburger.addEventListener('click', function () {
    hamburger.classList.toggle('isactive');
    menu.classList.toggle('active');
});

menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', function (e) {
        let target = e.target;
        let url = target.getAttribute('href');

        if (!url.startsWith('#')) {
            hamburger.classList.remove('isactive');
            menu.classList.remove('active');
            container.classList.add('fadeout');
        }
    });
});
