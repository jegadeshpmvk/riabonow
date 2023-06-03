if($('#map-canvas').length) {
    var homeLatlng = new google.maps.LatLng(coord.lat, coord.lng);
    var homeMarker = new google.maps.Marker({
        position: homeLatlng,
        map: map,
        draggable: true
    });
    		
    		
    google.maps.event.addListener(homeMarker, 'position_changed', function(){
        var lat = homeMarker.getPosition().$a;
        var lng = homeMarker.getPosition().ab;
    	$('#office-lat').val(homeMarker.getPosition().lat());
    	$('#office-lon').val(homeMarker.getPosition().lng());
    });

    var myOptions = {
        center: new google.maps.LatLng(coord.lat, coord.lng),
        zoom: 17,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

    google.maps.event.addListener(map, 'center_changed', function(){
        var lat = homeMarker.getPosition().$a;
        var lng = homeMarker.getPosition().ab;
    });

    var input = document.getElementById('office-place');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo('bounds', map);
    		
    //executed when a place is selected from the search bar
    google.maps.event.addListener(autocomplete, 'place_changed', function(){
        var place = autocomplete.getPlace();
        if (place.geometry.viewport)
            map.fitBounds(place.geometry.viewport);
        else{
            map.setCenter(place.geometry.location);
            map.setZoom(17); 
        }
        
        homeMarker.setMap(map);
        homeMarker.setPosition(place.geometry.location);
        
        var address = '';
        if (place.address_components) {
            address = [(place.address_components[0] &&
                        place.address_components[0].short_name || ''),
                       (place.address_components[1] &&
                        place.address_components[1].short_name || ''),
                       (place.address_components[2] &&
                        place.address_components[2].short_name || '')
                      ].join(' ');
        }
    });
    function checkEnter(e){
    	e = e || event;
    	var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
    	return txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
    }
    function markit(){
    	homeMarker.setMap(map);
    	homeMarker.setPosition(map.getCenter());
    	map.setCenter(homeMarker.getPosition());
    	map.setZoom(17);
    	$('#office-lat').val(homeMarker.getPosition().lat());
    	$('#office-lon').val(homeMarker.getPosition().lng());
    }
}