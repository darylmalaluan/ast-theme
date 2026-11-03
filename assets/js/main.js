(function () {
	var root = document.documentElement;
	var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	var skipReveal = reduce || new URLSearchParams(window.location.search).has("noreveal");

	function revealAll(nodes) {
		nodes.forEach(function (node) {
			node.classList.add("is-revealed");
		});
	}

	var revealNodes = Array.prototype.slice.call(document.querySelectorAll("[data-reveal]"));

	if (skipReveal || !("IntersectionObserver" in window)) {
		revealAll(revealNodes);
	} else {
		var loadNodes = revealNodes.filter(function (node) {
			return node.getAttribute("data-reveal-on") === "load";
		});
		window.requestAnimationFrame(function () {
			revealAll(loadNodes);
		});

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					entry.target.classList.add("is-revealed");
					observer.unobserve(entry.target);
				});
			},
			{ threshold: 0.15, rootMargin: "0px 0px -10% 0px" }
		);

		revealNodes.forEach(function (node) {
			if (node.getAttribute("data-reveal-on") !== "load") {
				observer.observe(node);
			}
		});
	}

	document.querySelectorAll("[data-carousel]").forEach(function (carousel) {
		var track = carousel.querySelector(".carousel__track");
		var slides = carousel.querySelectorAll(".carousel__slide");
		var prev = carousel.querySelector(".carousel__btn--prev");
		var next = carousel.querySelector(".carousel__btn--next");
		if (!track || slides.length < 2 || !prev || !next) {
			return;
		}

		function index() {
			var width = track.clientWidth || 1;
			return Math.round(track.scrollLeft / width);
		}

		function update() {
			var i = index();
			prev.disabled = i <= 0;
			next.disabled = i >= slides.length - 1;
			prev.setAttribute("aria-disabled", prev.disabled ? "true" : "false");
			next.setAttribute("aria-disabled", next.disabled ? "true" : "false");
		}

		function go(to) {
			var width = track.clientWidth;
			track.scrollTo({
				left: to * width,
				behavior: reduce ? "auto" : "smooth"
			});
		}

		prev.addEventListener("click", function () {
			go(Math.max(0, index() - 1));
		});
		next.addEventListener("click", function () {
			go(Math.min(slides.length - 1, index() + 1));
		});
		track.addEventListener("scroll", update, { passive: true });
		carousel.addEventListener("keydown", function (event) {
			if (event.key === "ArrowRight") {
				event.preventDefault();
				go(Math.min(slides.length - 1, index() + 1));
			}
			if (event.key === "ArrowLeft") {
				event.preventDefault();
				go(Math.max(0, index() - 1));
			}
		});
		update();
	});

	document.addEventListener("wpcf7invalid", function (event) {
		var field = event.target.querySelector(".wpcf7-not-valid");
		if (field) {
			field.focus();
		}
	}, false);

	document.addEventListener("wpcf7mailsent", function (event) {
		var output = event.target.querySelector(".wpcf7-response-output");
		if (output) {
			output.tabIndex = -1;
			output.focus();
		}
	}, false);

	document.addEventListener("wpcf7beforesubmit", function (event) {
		event.target.setAttribute("aria-busy", "true");
	}, false);

	document.addEventListener("wpcf7submit", function (event) {
		event.target.removeAttribute("aria-busy");
	}, false);

	var status = document.querySelector(".ast-form-status");
	if (status) {
		status.focus();
	}

	root.classList.add("ast-ready");
})();
