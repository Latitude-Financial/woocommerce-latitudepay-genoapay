var snippetSelectors = "img[src*='https://images.latitudepayapps.com/v2/snippet.svg']," +
    " img[src*='https://images.latitudepayapps.com/api/banner?brand=lpay']";
jQuery(document).on('click', snippetSelectors, refreshModal);

function refreshModal(e) {
    var img = e.target;
    var imgLink = new URL(img.src);
    img.style.cursor = "pointer";
    if (0 === document.getElementsByClassName("lpay-modal-wrapper").length) {
        var t = new XMLHttpRequest;
        t.onreadystatechange = function () {
            4 === t.readyState &&
            200 === t.status &&
            null != t.responseText &&
            (document.body.insertAdjacentHTML("beforeend", t.responseText), document.querySelector(".lpay-modal-wrapper").style.display = "block");

            setTimeout(updateLpayModal,100);
            setTimeout(updateLpayModal,500);
        }, t.open("GET", "https://images.latitudepayapps.com/v2/modal.html" + imgLink.search, !0), t.send(null)
    } else document.querySelector(".lpay-modal-wrapper").style.display = "block"
}

var updateLpayModal = function(){
    var lpaySvgModal = document.querySelector('.lpay-modal svg');
    var lpayModal = document.querySelector('.lpay-modal');
    if(lpaySvgModal && lpayModal){
        var lpaySvgModalOriginHeight = parseInt(lpaySvgModal.getAttribute('height').replace('px'));
        var lpaySvgModalOriginWidth = parseInt(lpaySvgModal.getAttribute('width').replace('px'));
        lpaySvgModal.style.width = '100%';
        lpaySvgModal.style.height = '100%';
        if(lpaySvgModalOriginHeight === 650 && lpaySvgModalOriginWidth === 520 && window.innerHeight > lpaySvgModalOriginHeight && window.innerWidth > lpaySvgModalOriginWidth){
            return;
        }
        if(window.innerHeight >= window.innerWidth){
            if(window.innerHeight - window.innerWidth <=160){
                lpayModal.style.width = ((window.innerHeight - 30) / lpaySvgModalOriginHeight) * lpaySvgModalOriginWidth + 'px';
                lpayModal.style.height = (window.innerHeight - 30) + 'px';
            } else {
                lpayModal.style.width =  (window.innerWidth - 30) + 'px';
                lpayModal.style.height = (lpayModal.clientWidth / lpaySvgModalOriginWidth) * lpaySvgModalOriginHeight + 'px';
            }

        } else {
            lpayModal.style.width = ((window.innerHeight - 30) / lpaySvgModalOriginHeight) * lpaySvgModalOriginWidth + 'px';
            lpayModal.style.height = (window.innerHeight - 30) + 'px';
        }
    }
}

window.onclick = function (event) {
    var lModal = document.querySelector(".lpay-modal");
    var lModalWrapper = document.querySelector('.lpay-modal-wrapper');
    if (event.target === lModalWrapper && lModal && event.target !== lModal && !lModal.contains(event.target)) {
        lModalWrapper.remove();
    }

    var gModal = document.querySelector(".g-infomodal-content");
    var gModalWrapper = document.querySelector('.g-infomodal-container');
    if (event.target === gModalWrapper && gModal && event.target !== gModal && !gModal.contains(event.target)) {
        gModalWrapper.remove();
    }
}

function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}
docReady(updateLpayModal);
window.addEventListener('resize', function(){
    updateLpayModal();
    setTimeout(updateLpayModal,2000);
});
if (window.screen && screen.orientation) {
    screen.orientation.onchange = updateLpayModal;
} else {
    window.screen.orientation = updateLpayModal;
}