if (typeof google !== "undefined" && typeof google.maps !== "undefined") {
	let selectedShape, map, drawingManager;
	const googleMap = google.maps;
	const mapInput = document.getElementById("myGoogleMap");
	const controlDiv = document.createElement("div");
	const placesInput = document.getElementById("places");
	const defaultLat = document.getElementById("defaultLat");
	const defaultLong = document.getElementById("defaultLong");

	let defaultLatLong = new googleMap.LatLng(34.225727, -77.94471);
	if (defaultLat?.value && defaultLong?.value)
		defaultLatLong = new googleMap.LatLng(
			parseFloat(defaultLat.value),
			parseFloat(defaultLong.value)
		);

	function initializeMap() {
		return new googleMap.Map(mapInput, {
			zoom: 8,
			center: defaultLatLong,
			disableDefaultUI: true,
			fullscreenControl: true,
			keyboardShortcuts: false,
			mapTypeId: googleMap.MapTypeId.ROADMAP,
		});
	}

	function clearSelection() {
		if (selectedShape) {
			selectedShape.setEditable(false);
			selectedShape = null;
		}
	}

	function setSelection(shape) {
		clearSelection();
		selectedShape = shape;
		shape.setEditable(true);
	}

	function initializeAutoComplete() {
		const autocomplete = new googleMap.places.Autocomplete(placesInput, {
			types: ["(regions)"],
		});

		autocomplete.addListener("place_changed", () => {
			const place = autocomplete.getPlace();

			if (!place.geometry || !place.geometry.location) {
				window.alert(`No details available for input: '${place.name}'`);
				return;
			}

			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			} else {
				map.setCenter(place.geometry.location);
				map.setZoom(17);
			}
		});
	}

	function initializeDrawingManager() {
		drawingManager = new googleMap.drawing.DrawingManager({
			drawingMode: googleMap.drawing.OverlayType.POLYGON,
			drawingControlOptions: { drawingModes: [googleMap.drawing.OverlayType.POLYGON] },
			polygonOptions: {
				strokeWeight: 3,
				fillOpacity: 0.3,
				strokeOpacity: 0.8,
				fillColor: getRandomColor(),
				strokeColor: getRandomColor(),
			},
		});

		drawingManager.setMap(map);

		googleMap.event.addDomListener(drawingManager, "overlaycomplete", (event) => {
			if (event.type === googleMap.drawing.OverlayType.POLYGON) {
				drawingManager.setDrawingMode(null);
				drawingManager.setOptions({ drawingControl: false });

				let newShape = event.overlay;
				newShape.type = event.type;
				googleMap.event.addListener(newShape, "click", function () {
					setSelection(newShape);
				});
				setSelection(newShape);
			}
		});
	}

	function initializeButton(Selector) {
		const controlUI = document.createElement("div");

		controlUI.style.backgroundColor = "#fff";
		controlUI.style.border = "2px solid #fff";
		controlUI.style.borderRadius = "3px";
		controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
		controlUI.style.cursor = "pointer";
		controlUI.style.marginTop = "8px";
		controlUI.style.marginBottom = "22px";
		controlUI.style.textAlign = "center";
		controlUI.title = "Click to clear the map";
		Selector.appendChild(controlUI);

		// Set CSS for the control interior.
		const controlText = document.createElement("div");

		controlText.style.color = "rgb(25,25,25)";
		controlText.style.fontFamily = "Roboto,Arial,sans-serif";
		controlText.style.fontSize = "16px";
		controlText.style.lineHeight = "38px";
		controlText.style.paddingLeft = "5px";
		controlText.style.paddingRight = "5px";
		controlText.innerHTML = "Clear Map";
		controlUI.appendChild(controlText);

		controlUI.addEventListener("click", () => {
			selectedShape.setMap(null);
			drawingManager.setDrawingMode(googleMap.drawing.OverlayType.POLYGON);
			drawingManager.setOptions({ drawingControl: true });
		});

		map.controls[googleMap.ControlPosition.TOP_CENTER].push(Selector);
	}

	document.getElementById("saveLocation").addEventListener("submit", function (event) {
		event.preventDefault();
		if (typeof selectedShape === "undefined" || selectedShape === null) {
			window.alert("Please create a area first.");
			return;
		}

		document.getElementById("polygons").value = JSON.stringify(
			selectedShape.getPath().getArray()
		);

		document.getElementById("saveLocation").submit();
	});

	function getRandomColor() {
		var letters = "0123456789ABCDEF";
		var color = "#";
		for (var i = 0; i < 6; i++) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

	googleMap?.event.addDomListener(window, "load", () => {
		map = initializeMap();
		initializeButton(controlDiv);
		initializeDrawingManager();
		initializeAutoComplete();
	});
}
