let userBtns = document.querySelectorAll('.user-btn')
userBtns.forEach(btn => {
	btn.addEventListener('click', (ev) => {
		let btn = ev.target
		let target = btn.getAttribute('target')
		let input = document.getElementById('user-type')
		let oldTarget = input.value;
		
		if (oldTarget == target) {
			return
		}

		let oldButton = document.querySelector(`.user-btn[target="${oldTarget}"]`)

		btn.disabled = true
		oldButton.disabled = false
		input.value = target

		document.body.className = target
	})
})

let oneBtnDisabled = false
userBtns.forEach(btn => {
	if (btn.disabled) {
		oneBtnDisabled = true
	}
})

if (!oneBtnDisabled) {
	userBtns[0].disabled = true
	document.body.className = userBtns[0].getAttribute('target')
}
