let passwordEyeContainers = document.querySelectorAll('.password-eye-container')
passwordEyeContainers.forEach(pec => {
	let passwordInput = pec.querySelector('input[type="password"]')
	let eye = pec.querySelector('i')

	eye.addEventListener('click', (ev) => {
		ev.target.classList.toggle('fa-eye-slash')
		let type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password'
		passwordInput.setAttribute('type', type)
	})
})