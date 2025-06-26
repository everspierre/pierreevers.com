let navbar = document.getElementById('navigation');
let shouldStickPosition = navbar.offsetTop;
function addOrRemoveStickyClass() {
    if (window.scrollY >= shouldStickPosition) {
        navbar.classList.add('sticky');
    } else {
        navbar.classList.remove('sticky');
    }
}
window.onscroll = () => {
    addOrRemoveStickyClass();
}

window.onresize = () => {
    shouldStickPosition = navbar.offsetTop;
}