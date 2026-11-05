(function () {
	"use strict";

	var SVG_NS = "http://www.w3.org/2000/svg";
	var GREEN_PATH = "M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z";
	var BORDEAUX_PATH = "M706.125 113.179C339.204 10.2306 82.4911 -4.50822 0 0.990986L9.89911 284.761L1524.44 284.761L1524.44 202.27C1404.55 215.469 1073.05 216.128 706.125 113.179Z";

	function createWave(className, viewBox, pathData, fill) {
		var svg = document.createElementNS(SVG_NS, "svg");
		var path = document.createElementNS(SVG_NS, "path");

		svg.setAttribute("class", className);
		svg.setAttribute("viewBox", viewBox);
		svg.setAttribute("preserveAspectRatio", "xMinYMin meet");
		svg.setAttribute("aria-hidden", "true");
		svg.setAttribute("focusable", "false");
		path.setAttribute("d", pathData);
		path.setAttribute("fill", fill);
		svg.appendChild(path);

		return svg;
	}

	function injectChoiceWaves() {
		var section = document.querySelector(".tp-choice-section");

		if (!section || section.querySelector(".choice__wave--green")) {
			return;
		}

		section.classList.add("choice");
		section.insertBefore(
			createWave("choice__wave choice__wave--green", "0 0 1811.7627 428.3477", GREEN_PATH, "#054726"),
			section.firstElementChild
		);
		section.insertBefore(
			createWave("choice__wave choice__wave--bordeaux", "0 0 1524.4385 284.7612", BORDEAUX_PATH, "#560B22"),
			section.firstElementChild
		);
	}

	function initChoiceCards() {
		var cards = ".tp-choice-card, .choice-card, .tp-scelta-scheda";

		injectChoiceWaves();
		// The cards of the section made of Divi modules take the keyboard focus, as the rebuilt ones.
		Array.prototype.forEach.call(document.querySelectorAll(".tp-scelta-scheda:not([tabindex])"), function (card) {
			card.setAttribute("tabindex", "0");
		});
		window.tpBindActiveState(document.querySelectorAll(cards), { outside: cards });
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initChoiceCards);
	} else {
		initChoiceCards();
	}
})();
