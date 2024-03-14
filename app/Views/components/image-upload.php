<?php if (url_is('add*') || url_is('update*')) : ?>
	<!-- filepond validation -->
	<link href="https://cdn.jsdelivr.net/combine/npm/filepond@4.30.3/dist/filepond.min.css,npm/filepond-plugin-image-preview@4.6.10/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/combine/npm/filepond@4.30.3,npm/filepond-plugin-image-preview@4.6.10,npm/filepond-plugin-file-validate-size@2.2.5,npm/filepond-plugin-file-validate-type@1.2.6"></script>
	<script>
		if (document.getElementsByClassName('image-upload')) {
			// register desired plugins...
			FilePond.registerPlugin(
				FilePondPluginImagePreview,
				FilePondPluginFileValidateSize,
				FilePondPluginFileValidateType,
			);

			// Filepond: Image Preview
			const defaultOptions = {
				// required: true,
				credits: false,
				storeAsFile: true,
				allowReplace: true,
				maxFileSize: '2MB',
				allowImageCrop: false,
				allowImagePreview: true,
				allowImageFilter: false,
				allowFileSizeValidation: true,
				allowImageExifOrientation: false,
				acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
				fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
					resolve(type);
				})
			}

			const imageSelectorElements = document.querySelectorAll(".image-upload");

			imageSelectorElements.forEach(function(element) {
				const prevImage = element.parentElement.querySelector('.prev-image-upload');

				FilePond.create(element, {
					...defaultOptions,
					files: [...(prevImage ? [{
						source: prevImage.value
					}] : [])],
				});
			});
		}
	</script>
<?php endif; ?>