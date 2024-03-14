<header class="mb-3">
	<a href="#" class="burger-btn d-block d-xl-none">
		<i data-feather="align-left" class="fs-3"></i>
	</a>
</header>

<?php foreach (directory_map(MODULESPATH, 0) as $folder => $files) {
	if (!empty($folder)) {
		$folder = str_replace('/', '\\', ucfirst($folder));
		$filePath = "Modules\\{$folder}Views\layouts\header";
		if (file_exists(ROOTPATH . str_replace('\\', '/', $filePath) . '.php')) print($this->include($filePath));
	}
} ?>