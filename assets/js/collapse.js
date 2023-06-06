let collapseBtns = document.querySelectorAll('button[data-bs-toggle="collapse"]')
collapseBtns.forEach(btn => {
	btn.addEventListener('click', (ev) => {
		/**@type{ HTMLElement} */
		let btn = ev.target
		let icon = btn.querySelector('i')

		if (icon.style.transform == '') {
			icon.style.transform = 'rotate(90deg)'

			collapseBtns.forEach(oBtn => {
				if (oBtn == btn) {
					return
				}

				oBtn.querySelector('i').style.transform = ''
			})
		} else {
			icon.style.transform = ''
		}
	})
})
