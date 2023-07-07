async function pmGetPdf() {
	const btn = document.getElementById('pmGetPdf')
	btn.disabled = true
	const postId = btn.getAttribute('postId')
	await fetch('/wp-admin/admin-ajax.php?action=pm_get_pdf&post_id=' + postId, {
		method: 'GET',
		credentials: 'same-origin',
	})
		.then(res => res.blob())
		.then((blob) => {
			const url = URL.createObjectURL(blob);
			const a = document.createElement('a');
			a.href = url;
			a.download = 'post-' + postId + '.pdf';
			document.body.appendChild(a);
			a.click();
			a.remove();
		})

	btn.disabled = false
}
