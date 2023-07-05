let rinunciaForm = document.getElementById('deleteForm')
let /** @type {HTMLInputElement} */ deleteInput = deleteForm.querySelector('input')
let firstDeleteBtn = deleteForm.querySelector('button:not([type="submit"])')
let secondDeleteBtn = deleteForm.querySelector('button[type="submit"]')
let annullaDeleteBtn = deleteForm.querySelector('button:not(.warning)')

function activeDeactivateDeleteForm() {
	firstDeleteBtn.classList.toggle('d-none')
	secondDeleteBtn.classList.toggle('d-none')
	annullaDeleteBtn.classList.toggle('d-none')
	deleteInput.checked = true
}

firstDeleteBtn.addEventListener('click', (ev) => {
	ev.preventDefault()
	activeDeactivateDeleteForm()
})
annullaDeleteBtn.addEventListener('click', (ev) => {
	ev.preventDefault()
	activeDeactivateDeleteForm()
})