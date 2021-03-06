var map; // Global declaration of the map
var drawingManager;
var lastpolygon = null;
var polygons = [];


function initMap() {

    map = new google.maps.Map(document.getElementById("map"), {
        center: {
            lat: 0.00
            , lng: 0.00
        }
        , zoom: 8
        ,
    });


    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON
        , drawingControl: true
        , drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER
            , drawingModes: [
                google.maps.drawing.OverlayType.POLYGON
                ,]
            ,
        }
        , markerOptions: {
            icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"
            ,
        }
        , circleOptions: {
            fillColor: "#ffff00"
            , fillOpacity: 1
            , strokeWeight: 5
            , clickable: false
            , editable: true
            , zIndex: 1
            ,
        }
        ,
    });

    drawingManager.setMap(map);

    //get current location block
    // infoWindow = new google.maps.InfoWindow();
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude
                    , lng: position.coords.longitude
                    ,
                };
                map.setCenter(pos);
            });
    }


    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {

        if (lastpolygon != null) {
            lastpolygon.setMap(null);
        }
        var coordinates = event.overlay.getPath().getArray();
        lastpolygon = event.overlay;
        livewire.emit('selectedCoordinates', coordinates);
    });
}

function initEditMap(coordinates) {

    map = new google.maps.Map(document.getElementById("editMap"), {
        center: {
            lat: 0.00,
            lng: 0.00
        },
        zoom: 8,
    });


    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON,
            ],

        },
        markerOptions: {
            icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",

        },
        circleOptions: {
            fillColor: "#ffff00",
            fillOpacity: 1,
            strokeWeight: 5,
            clickable: false,
            editable: true,
            zIndex: 1,
        },
    });
    // 
    drawingManager.setMap(map);
    // set prviouse selected data
    lastpolygon = new google.maps.Polygon({
        paths: coordinates,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.1,
    });
    lastpolygon.setMap(map);


    var polygonBounds = new google.maps.LatLngBounds();
    lastpolygon.getPaths().forEach(function(path) {
      path.forEach(function(latlng) {
        polygonBounds.extend(latlng);
        map.fitBounds(polygonBounds);
      });
    });
    map.setCenter(polygonBounds.center,13)


    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {

        if (lastpolygon != null) {
            lastpolygon.setMap(null);
        }
        var coordinates = event.overlay.getPath().getArray();
        lastpolygon = event.overlay;
        livewire.emit('selectedCoordinates', coordinates);
    });
}


//
livewire.on("initiateEditMap", (data) => {
    initEditMap(data);
});

//
livewire.on("resetMap", (data) => {
    if (lastpolygon != null) {
        lastpolygon.setMap(null);
    }
});
