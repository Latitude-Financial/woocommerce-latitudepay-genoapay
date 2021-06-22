<?php
/**
 * @var WC_LatitudeFinance_Method_Latitudepay $this
 */
?>
<script>
	!function () {
		var e = document.querySelectorAll("img[src*='<?php echo $this->getImagesApiUrl(); ?>snippet.svg'], img[src*='<?php echo $this->getImagesApiUrl(); ?>api/banner'], img[src*='<?php echo $this->getImagesApiUrl(); ?>LatitudePayPlusSnippet.svg']");
		[].forEach.call(
			e, function (e) {
				e.style.cursor = "pointer",
					e.addEventListener("click", handleClick)
			})
		function handleClick(e) {
			if (0 == document.getElementsByClassName("lpay-modal-wrapper").length) {
				var t = new XMLHttpRequest;
				t.onreadystatechange = function () {
					4 == t.readyState && 200 == t.status && null != t.responseText && (document.body.insertAdjacentHTML("beforeend", t.responseText),
						document.querySelector(".lpay-modal-wrapper").style.display = "block")
				},
					t.open("GET", "<?php echo $this->getImagesApiUrl(); ?>modal.html", !0),
					t.send(null)
			} else document.querySelector(".lpay-modal-wrapper").style.display = "block"
		}
	}();
</script>
