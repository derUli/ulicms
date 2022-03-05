let hamburger = document.querySelector('.hamburger');
let menu = document.querySelector('nav.navbar');
let bod = document.querySelector('.container');

menu.classList.remove('active');


hamburger.addEventListener('click', function () {
    hamburger.classList.toggle('isactive');
    menu.classList.toggle('active');
});
