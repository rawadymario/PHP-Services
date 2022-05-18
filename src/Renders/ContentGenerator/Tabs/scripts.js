var RawadyMarioTabs = new function() {

	this.showTabContent = function(el) {
		const key = el.dataset.key;
		const tabsEl = el.parentElement.parentElement.parentElement;

		const activeTabs = tabsEl.querySelectorAll(".tab-elem.active");
		[].forEach.call(activeTabs, function(activeTab) {
			activeTab.classList.remove("active");
		});
		tabsEl.querySelector(`#tab_elem_${key}`).classList.add("active");

		const activeContents = tabsEl.querySelectorAll(".tab-content.active");
		[].forEach.call(activeContents, function(activeContent) {
			activeContent.classList.remove("active");
		});
		tabsEl.querySelector(`#tab_content_${key}`).classList.add("active");
	};

};