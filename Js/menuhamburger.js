const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('ul');

hamburger.addEventListener('click', () => {
    navMenu.classList.toggle('show-menu');
});
