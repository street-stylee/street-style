document.addEventListener('DOMContentLoaded', function() {
    
    let menuIcon = document.querySelector('#menu-icon');
    let navbar = document.querySelector('.navmenu'); 

    if (menuIcon && navbar) {
        
        menuIcon.onclick = () => {
            menuIcon.classList.toggle('bx-x');
            navbar.classList.toggle('active'); 
        };

        window.onscroll = () => {
            menuIcon.classList.remove('bx-x');
            navbar.classList.remove('active');
        };
    }
});