let disableClick = false;
var RawadyMarioAccordions = new function() {
	this.showTabContent = function(el) {
		if (window.jQuery) {
			RawadyMarioAccordions.showTabContentJquery(el);
		}
		else {
			RawadyMarioAccordions.showTabContentJs(el);
		}
	};

	this.showTabContentJs = function(el) {
		const key = el.dataset.key;
		const parentEl = el.parentElement;
		const isActive = el.classList.contains("active");

		const activeTabs = parentEl.querySelectorAll(".accordion-element.active");
		const activeTops = parentEl.querySelectorAll(".accordion-element.active .top.active");
		[].forEach.call(activeTops, function(activeTop) {
			activeTop.classList.remove("active");
		});
		[].forEach.call(activeTabs, function(activeTab) {
			activeTab.classList.remove("active");
		});
		if (!isActive) {
			parentEl.querySelector(`#accordion_element_${key}`).classList.add("active");
			parentEl.querySelector(`#accordion_element_${key} .top`).classList.add("active");
		}
	};

	this.showTabContentJquery = function(el) {
		if (!disableClick) {
			disableClick = true;
			const $parentEl = $(el).parents(".rawaymario-accordion").first();
			const isActive = $(el).hasClass("active");

			$parentEl.find(".accordion-element.active .top.active").removeClass("active")
			$parentEl.find(".accordion-element.active .bottom").slideUp(500, () => {
				$parentEl.find(".accordion-element.active").removeClass("active");
				disableClick = false;
			});
			if (!isActive) {
				$(el).find(".top").addClass("active");
				$(el).find(".bottom").slideDown(500, () => {
					$(el).addClass("active");
					disableClick = false;
				});
			}
		}
	};

};