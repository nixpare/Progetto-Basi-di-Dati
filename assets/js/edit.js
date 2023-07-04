let editBtns = document.querySelectorAll('button[data-edit-target]')
editBtns.forEach(btn => {
	btn.addEventListener('click', ev => {
		let /** @type {HTMLFormElement} */ form = document.getElementById(ev.target.getAttribute('data-edit-target'))
		let container = document.getElementById(form.getAttribute('data-edit-container'))
		let /** @type {HTMLInputElement} */ input = form.querySelector('input, select')

		switch (ev.target.getAttribute('data-edit-action')) {
			case 'edit':
				container.setAttribute('data-edit-state', 'edit')
				input.removeAttribute('disabled')
				input.setAttribute('data-edit-old-value', input.value)
				input.value = ''
				break
			case 'undo':
				container.removeAttribute('data-edit-state')
				input.setAttribute('disabled', '')
				input.value = input.getAttribute('data-edit-old-value')
				input.removeAttribute('data-edit-old-value')
				break
			case 'send':
				container.setAttribute('data-edit-state', 'send')
				form.submit()
				break
		}
	})
})