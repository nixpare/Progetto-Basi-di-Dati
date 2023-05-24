let collapseBtns = document.querySelectorAll('button[data-bs-toggle="collapse"]')
collapseBtns.forEach(btn => {
	btn.addEventListener('click', (ev) => {
		/**@type{ HTMLElement} */
		let icon = ev.target.querySelector('i')

		if (icon.style.transform == '') {
			icon.style.transform = 'rotate(90deg)'
		} else {
			icon.style.transform = ''
		}
	})
})
