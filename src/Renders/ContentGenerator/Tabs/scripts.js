var RawadyMarioTabs = new function() {

	this.showTabContent = function(el) {
		const key = el.dataset.key;
		const parentEl = el.parentElement.parentElement.parentElement;

		const activeTabs = parentEl.querySelectorAll(".tab-elem.active");
		[].forEach.call(activeTabs, function(activeTab) {
			activeTab.classList.remove("active");
		});
		parentEl.querySelector(`#tab_elem_${key}`).classList.add("active");

		const activeContents = parentEl.querySelectorAll(".tab-content.active");
		[].forEach.call(activeContents, function(activeContent) {
			activeContent.classList.remove("active");
		});
		parentEl.querySelector(`#tab_content_${key}`).classList.add("active");
	};

};