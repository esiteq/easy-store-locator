var markers = new Array();
var geocoder, map;
var placeSearch, autocomplete;
//
jQuery(document).ready(function($)
{
    $('#esl_gps').click(function(e)
    {
        if ($(this).is(':checked'))
        {
            getMyLocation();            
        }
    });
    //
    /*
    $('#get-center').click(function(e)
    {
        console.log(map.getCenter().lat());
        console.log(map.getCenter().lng());
        e.preventDefault();
    });
    */
});
//
function getMyLocation()
{
    if(navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
    }
    else
    {
        console.log('Geolocation is not supported by the browser');
    }
}
//
function geoSuccess(position)
{
    centerLat = position.coords.latitude;
    centerLng = position.coords.longitude;
    jQuery('esl_lat').val(centerLat);
    jQuery('esl_lng').val(centerLng);
    map.setCenter({lat: centerLat, lng: centerLng});
}
//
function geoError()
{
    console.log('Geolocation failed');
}
//
function initMap()
{
    autocomplete = new google.maps.places.Autocomplete((document.getElementById('esl_address')), {types: ['geocode']});
    autocomplete.addListener('place_changed', fillInAddress);
    map = new google.maps.Map(document.getElementById('mapLocator'),
    {
        zoom: 12,
        center: {lat: centerLat, lng: centerLng}
    });
    jQuery.each(stores, function(i, m)
    {
        var image = m.icon;
        var marker = new google.maps.Marker(
        {
            position: {lat: m.lat, lng: m.lng},
            map: map,
            icon: image,
            title: m.title
        });
        //console.log(m);
        var html = '<div class="esl-info"><div class="esl-info-thumb">'+m.thumb+'</div>';
        html += '<div class="esl-info-desc"><h3 class="esl-info-title">'+m.title+'</h3>'+m.description+'<br /><br /><p><a href="'+m.link+'">View Details</a> | <a href="'+m.navigation+'">Navigation</a></p></div><div style="clear:both"></div></div>';
        var infowindow = new google.maps.InfoWindow({
            content: html
        });
        marker.addListener('click', function()
        {
            openModal(m.id);
            //infowindow.open(map, marker);
        });
    });
}
//
function openModal(id)
{
    jQuery(function($)
    {
        var m = stores[id];
        console.log(m);
        $('#store-modal-title').html(m.title);
        $('#store-modal-thumb').html(m.thumb);
        $('#store-modal-desc').html(m.description);
        $('#store-modal-nav').attr('href', m.navigation);
        var html = '<table class="table table-bordered">';
        if (m.open_hours)
        {
            html += '<tr><th>Open Hours</th><td>'+m.open_hours+'</td></tr>';
        }
        if (m.email)
        {
            html += '<tr><th>Email</th><td><a href="mailto:'+m.email+'">'+m.email+'</a></td></tr>';
        }
        if (m.website)
        {
            html += '<tr><th>Website</th><td><a href="'+m.website+'" target="_blank">'+m.website+'</a></td></tr>';
        }
        if (m.phone)
        {
            html += '<tr><th>Phone</th><td><a href="tel:'+m.phone+'">'+m.phone+'</a></td></tr>';
        }        
        html += '</table>';
        $('#store-modal-table').html(html);
        $('#storeModal').modal();
    });
}
//
function fillInAddress()
{
    var place = autocomplete.getPlace();
    var centerLat = place.geometry.location.lat();
    var centerLng = place.geometry.location.lng();
    map.setCenter({lat: centerLat, lng: centerLng});
    jQuery('esl_lat').val(centerLat);
    jQuery('esl_lng').val(centerLng);
}
//
function geolocate()
{
    if (navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition(function(position)
        {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    }
}