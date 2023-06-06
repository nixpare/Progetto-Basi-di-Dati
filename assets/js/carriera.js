let rinunciaForm = document.getElementById('rinunciaForm')
let /** @type {HTMLInputElement} */ rinunciaInput = rinunciaForm.querySelector('input')
let firstRinunciaBtn = rinunciaForm.querySelector('button:not([type="submit"])')
let secondRinunciaBtn = rinunciaForm.querySelector('button[type="submit"]')
let annullaRinunciaBtn = rinunciaForm.querySelector('button:not(.warning)')

function activeDeactivateRinunciaForm() {
	firstRinunciaBtn.classList.toggle('d-none')
	secondRinunciaBtn.classList.toggle('d-none')
	annullaRinunciaBtn.classList.toggle('d-none')
	rinunciaInput.checked = true
}

firstRinunciaBtn.addEventListener('click', (ev) => {
	ev.preventDefault()
	activeDeactivateRinunciaForm()
})
annullaRinunciaBtn.addEventListener('click', (ev) => {
	ev.preventDefault()
	activeDeactivateRinunciaForm()
})