<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="page-heading">
	<h3><?= lang("Lang.appBirdEyeView", [(config('Settings')->siteName ?? lang("Lang.dashboard"))]) ?></h3>
</div>
<div class="page-content" style="background-color: var(--bs-body-bg);" id="fullscreen-div">
	<section class="row">
		<div class="col-12 col-lg-12">
			<div class="row">
				<div class="col-12 col-lg-9">
					<div class="card overflow-auto">
						<div class="card-body">
							<div id="myGoogleMap" class="shadow" style="height:60vh; border-radius: 24px">
								<noscript><?= lang("Lang.mapAreNotLoaded") ?></noscript>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-3">
					<div class="card overflow-auto">
						<div class="card-header">
							<h2><?= lang("Lang.allOnlineDrivers") ?></h2>
						</div>
						<div id="driverList"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&key=<?= config('Settings')->googleMapApi; ?>"></script>
<style>
	.pac-container:after {
		content: none !important;
	}
</style>

<script>
	var googleMap = google.maps;
	window.markers = [];
	window.drivers = [];

	setInterval(function() {
		fetch('<?= base_url('api/locations') ?>').then(response => response.json()).
		then(data => {
			if (data.status == 200) {
				window.drivers = Object.values(data?.data);
				if (window.markers.length > 0) {
					for (var i = 0; i < window.markers.length; i++) {
						window.markers[i].setMap(null);
					}
					window.markers = [];
				}
				for (var i = 0; i < window.drivers.length; i++) {
					console.log(window.drivers[i]?.category.category_name.replace(' ', '_'));
					setMarker(window.drivers[i]?.user.firstname, window.drivers[i]?.user.lat, window.drivers[i]?.user.long, window.drivers[i]?.category.category_name.replace(' ', '_'), window.drivers[i]);
				}
				updateDriverRender();
			}
			console.log(window.drivers);
		}).catch(error => console.error(error))
	}, 5000);



	googleMap?.event.addDomListener(window, "load", () => {
		window.map = new googleMap.Map(document.getElementById('myGoogleMap'), {
			zoom: 10,
			disableDefaultUI: true,
			fullscreenControl: true,
			keyboardShortcuts: false,
			mapTypeId: googleMap.MapTypeId.ROADMAP,
			center: new googleMap.LatLng(<?= config('Settings')->defaultLat ?>, <?= config('Settings')->defaultLong ?>),
		});
	});

	function setMarker(title, lat, lng, icon = null, drivers = null) {
		console.log(title, lat, lng, icon);
		if (typeof lat === 'string') lat = parseFloat(lat);
		if (typeof lng === 'string') lng = parseFloat(lng);

		var marker = new googleMap.Marker({
			title,
			map: window.map,
			position: new googleMap.LatLng(lat, lng),
			icon: {
				url: drivers?.category?.category_icon ?? `<?= site_url('assets/img/') ?>mini.png`, // url
				scaledSize: new googleMap.Size(50, 50), // scaled size
				origin: new googleMap.Point(0, 0), // origin
				anchor: new googleMap.Point(0, 0) // anchor
			},
		});

		const infoWindow = new googleMap.InfoWindow();

		marker.addListener("click", () => {
			infoWindow.close();
			infoWindow.setContent(`<div class="container"><h1 class="text-capitalize">${title}</h1><ul class="list-group"><li class="list-group-item">Category: ${drivers?.category?.category_name}</li><li class="list-group-item">Vehicle Brand: ${drivers?.vehicle?.vehicle_brand}</li><li class="list-group-item">Vehicle Number: <span class="text-uppercase">${drivers?.vehicle?.vehicle_number}</span></li><li class="list-group-item">Vehicle Modal: ${drivers?.vehicle?.vehicle_modal}</li><li class="list-group-item">Speed: ${drivers?.user?.speed}</li><li class="list-group-item">Status: ${drivers?.status}</li></ul></div>`);
			infoWindow.open(marker.getMap(), marker);
		});

		window.markers.push(marker);

		// var bounds = new googleMap.LatLngBounds();
		// for (var i = 0; i < window.markers.length; i++) {
		// 	bounds.extend(window.markers[i].getPosition());
		// }

		// window.map.fitBounds(bounds);
		return marker;
	}

	function updateDriverRender() {
		var driverListParent = document.getElementById('driverList');
		var driverChild = '';
		for (var i = 0; i < window.drivers.length; i++) {
			driverChild += `<div class="card-body border mb-3"><div class="d-flex align-items-center"><div class="flex-shrink-0"><div class="avatar avatar-xl"><div class="avatar bg-warning avatar-xl"><img src="${window.drivers[i]?.user?.profile_pic??'https://picsum.photos/200'}"></div></div></div><div class="flex-grow-1 ms-3"><a class="lead text-truncate mb-1 text-capitalize">${window.drivers[i]?.user?.firstname+' '+window.drivers[i]?.user?.lastname}</a><p class="lead text-truncate mb-1 text-capitalize">${window.drivers[i]?.vehicle?.vehicle_number}</p><p class="lead text-truncate mb-1 text-capitalize">${window.drivers[i]?.status}</p></div></div></div>`;
		}
		driverListParent.innerHTML = driverChild;
	}
</script>
<?= $this->endSection(); ?>