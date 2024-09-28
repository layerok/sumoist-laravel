window.addEventListener("load", function(event) {
    gsap.registerPlugin(ScrollTrigger);
    gsap.to('#nav', {
        scrollTrigger: {
            trigger: "#nav",
            pin: true,
            end: () => document.body.offsetHeight,
            onLeaveBack: () => {
                //document.querySelector('.cart-in-menu').classList.add('dn');
            },
            onEnter: () => {
               // document.querySelector('.cart-in-menu').classList.remove('dn');
            }
        },
        top: -1
    })




})
