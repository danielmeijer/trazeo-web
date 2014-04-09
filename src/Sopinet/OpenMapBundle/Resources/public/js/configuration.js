/* Leaflet configuration*/

/* Creación del  mapa */
var map = L.map('map').setView([37.8938548, -4.788015299999984], 12);

L.tileLayer('http://{s}.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/997/256/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
    maxZoom: 18
}).addTo(map);


/* Definir dónde se encuentran de las imágenes*/
var baseUrl = '/template-openmap-bundle/Resources/public/css';
var greenIcon = L.icon({
    iconUrl: baseUrl + '/images/marker-icon-start.png',
    shadowUrl: baseUrl + '/images/marker-shadow.png',

});
/* */
var marker = L.marker([ 37.8938548, -4.788015299999984 ],{icon: greenIcon}).addTo(map);
marker.bindPopup("<b>Comienzo</b><br>Punto de partida.").openPopup();
