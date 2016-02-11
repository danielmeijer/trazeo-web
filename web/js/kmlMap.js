/**
 * Created by hud on 19/01/16.
 */
function initMap(mapConfig) {

    timeFromLastUpdate = 0;

    lastId = "0";

    if (!mapConfig.editable) {
        $("#comenzar").hide();
        $("#deshacer").hide();
        $("#enviar").hide();
        $("#fileButton").hide();
    };

    //configuracion mapa leaflet
    var gk, topo, thunderforest, osm, waymarkedtrails;
    var menu = null;
    var last_actions = null
    var icon = "leaflet-marker-icon leaflet-zoom-animated leaflet-clickable leaflet-marker-draggable";

    L.Icon.Default.imagePath = mapConfig.iconImagePath;
    L.Icon.Default.prototype.options.iconSize[1] = 35;
    L.Icon.Default.prototype.options.iconSize[0] = 22;
    gk = 'http://opencache.statkart.no/gatekeeper/gk';


    osm = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'tiles &copy; <a target="_blank" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        styleId: 22677
    });

    // preload image icon
    $('#' + mapConfig.mapContainerId).append("<img id='preload' src=" + mapConfig.iconImagePath + "/marker-icon-start.png>");
    $("#preload").hide();

    var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/{styleId}/256/{z}/{x}/{y}.png',
        cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade';
    var midnight   = L.tileLayer(cloudmadeUrl, {styleId: 999, attribution: cloudmadeAttribution}),
        motorways = L.tileLayer(cloudmadeUrl, {styleId: 46561, attribution: cloudmadeAttribution});

    waymarkedtrails = L.tileLayer('http://tile.waymarkedtrails.org/hiking/{z}/{x}/{y}.png', {
        maxZoom: 19,
        opacity: 0.5,
        attribution: 'overlay &copy; <a target="_blank" href="http://hiking.waymarkedtrails.org">Waymarked Trails</a> '
        + '(<a target="_blank" href="http://creativecommons.org/licenses/by-sa/3.0/de/deed.en">CC-BY-SA 3.0 DE</a>)'
    });


    if (mapConfig.points.length > 1) {
        var center = mapConfig.points[0].latLng;
    }
    else {
        center = new L.LatLng(37.8938548, -4.788015299999984)
    }
    map = new L.Map(mapConfig.mapContainerId, {
        layers: [osm]
        , center: center
        , zoom: 16
        , scrollWheelZoom: false
    });


    // Routing Machine plugin info
    router = function (m1, m2, cb) {
        var proxy = 'http://www2.turistforeningen.no/routing.php?url=';
        var route = 'http://www.yournavigation.org/api/1.0/gosmore.php&format=geojson&v=foot&fast=1&layer=mapnik';
        var params = '&flat=' + m1.lat + '&flon=' + m1.lng + '&tlat=' + m2.lat + '&tlon=' + m2.lng;
        $.getJSON(proxy + route + params, function (geojson, status) {
            if (!geojson || !geojson.coordinates || !geojson.coordinates.length === 0) {
                if (typeof console.error === 'function') {
                    console.error('OSM router failed', geojson);
                }
                return cb(new Error());
            }
            return cb(null, L.GeoJSON.geometryToLayer(geojson));
        });
    }

    //añadimos el plugin de routing
    routing = new L.Routing({
        position: 'bottomright'
        , routing: {
            router: router
        }
        , icons: {
            start: new L.Icon({iconUrl: mapConfig.iconImagePath + '/marker-icon-start.png', iconSize: 22})
            ,
            end: new L.Icon({iconUrl: mapConfig.iconImagePath + '/marker-icon-start.png', iconSize: 22})
            ,
            normal: new L.Icon({
                iconUrl: mapConfig.iconImagePath + '/marker-icon.png',
                iconSize: [22, 35],
                iconAnchor: [10, 35]
            })
        }
        , snapping: {
            layers: []
        }
        , shortcut: {
            draw: {
                enable: 68    // 'd'
                , disable: 81  // 'q'
            }
        }
    });
    map.addControl(routing);


    // Map GeoCoder Plugin info
    var options = {

        position: 'topright', /* The position of the control */
        text: 'Locate', /* The text of the submit button */
        bounds: null, /* a L.LatLngBounds object to limit the results to */
        email: null, /*
         * an email string with a contact to provide to
         * Nominatim. Useful if you are doing lots of queries
         */
        callback: function (results) {
            var bbox = results[0].boundingbox,
                first = new L.LatLng(bbox[0], bbox[2]),
                second = new L.LatLng(bbox[1], bbox[3]),
                bounds = new L.LatLngBounds([first, second]);

            this._map.fitBounds(bounds);
        }
    };
    geocoder = L.Control.Geocoder.nominatim();
    control = L.Control.geocoder({
        collapsed: false /* Whether its collapsed or not */
        , geocoder: geocoder
        , placeholder: 'Locate'
        , errorMessage: "Nothing found."
    });
    control.markGeocode = function (result) {
        map.setView(result.center);
    }
    control.addTo(map);
    routing.draw();


    // map custom events
    L.Map.prototype.disableRouting = function () {
        $(".leaflet-marker-icon").css("pointer-events", "none");
        routing.draw(false);

        if (routing.getWaypoints().length > 0) {
            $("#enviar").removeAttr("disabled");
            $("#enviar").css('opacity', '1');
        }
        $("#comenzar").on('click', map.enableRouting);
        $("#comenzar").html('<i class="fa fa-flag"></i>&nbsp' + mapConfig.buttonsText.startButton);
    };
    L.Map.prototype.enableRouting = function () {

        if (!mapConfig.editable) {
            return;
        }

        $(".leaflet-marker-icon").css("pointer-events", "all");

        if (menu == null) {
            routing.draw(true);
        }
        $("#enviar").attr("disabled", "disabled");
        $("#enviar").css('opacity', '0.7');
        $("#deshacer").on('click', map.removeLast);
        $("#comenzar").on('click', map.disableRouting);
        $("#comenzar").html('<i class="fa fa-flag"></i>&nbsp' + mapConfig.buttonsText.endButton);

    };
    // data functions
    L.Map.prototype.getWaypoints = function () {
        return routing.getWaypoints();
    };
    L.Map.prototype.loadFromEvents = function (events) {
        var i = 0;

        if (typeof poly === 'undefined') {
            poly = new L.polyline(events[i].latLng, {dashArray: "10, 20", color: "green"});
            poly.addTo(map);
            i++
        }

        for (; i < events.length; i++) {
            poly.addLatLng(events[i].latLng);
        }
    };

    L.Map.prototype.loadFromWaypoints = function (waypoints) {
        var points = [];
        var colors=['green','red','blue'];
        var j=0;
        for (var i=0; i < waypoints.length; i++) {
            if (waypoints[i].pickup) {
                var marker = new L.Marker(waypoints[i].latLng);
                marker.feature=true;
                marker.addTo(map);
            } else {
                points.push(waypoints[i].latLng);
            }
        }
        poly = new L.polyline(points, {color: colors[j]});
        poly.feature=true;
        poly.addTo(map);
    };

    // map save handler function
    $("form").submit(function(){
        // create input
        if(document.getElementById("inputPoints")==null){
            var lat=document.createElement("INPUT");
            lat.id="inputPoints";
            lat.name="inputPoints";
            lat.type="hidden";
            document.getElementById("form").appendChild(lat);
        }

        if(document.getElementById("distanceInput")==null){
            var dist=document.createElement("INPUT");
            dist.id="distanceInput";
            dist.name="distanceInput";
            dist.type="hidden";
            document.getElementById("form").appendChild(dist);
        }

        if(document.getElementById("kmlInput")==null){
            var kmlInput=document.createElement("INPUT");
            kmlInput.id="kmlInput";
            kmlInput.name="kmlInput";
            kmlInput.type="hidden";
            document.getElementById("form").appendChild(kmlInput);
        }

        // populate inputs
        if(!$("#cityInput").html()!='Desconocido'){
            $("#cityInput").val($("#start").html());
        }
        $("#inputPoints").val(pointList);
        var distance=map.getDistance(arrayPoints);
        $("#distanceInput").val(distance);
        $("#kmlInput").val(kml);
    });

    // methods
    L.Map.prototype.updateRouteInfo=function(e){
        var first=null;
        var last=null;
        // getting points info from kml
        arrayPoints=[];
        pointList="";

        for(var i in map._layers){
            if(map._layers[i] instanceof L.Polyline && map._layers[i].feature) {
                for (var j in map._layers[i]._latlngs) {
                    var latlng = map._layers[i]._latlngs[j];
                    arrayPoints.push(latlng);
                    pointList+=latlng.lat+","+latlng.lng+",0,;";
                }
            }
            else if(map._layers[i] instanceof L.Marker && map._layers[i].feature) {
                var marker = map._layers[i];
                var pick= '1,Punto Recogida;';
                if(first==null) {
                    first= marker._latlng;
                    pick='1,Punto inicio;';
                }
                last = marker._latlng;
                pointList+=marker._latlng.lat+","+marker._latlng.lng+","+pick;
                arrayPoints.push(map._layers[i]._latlng);
            }
        }

        var distance=0;

        if(first) {
            geocoder.reverse(first, map.options.crs.scale(16), function(results) {
                var r = results[0];
                var aux=r.name.split(',');
                aux.splice(1,2);
                aux.splice(3,1);
                $("#start").html(aux.toString()||'');
            });
            $("#start").html('Desconocido');
        }

        if(last) {
            geocoder.reverse(last, map.options.crs.scale(16), function(results) {
                var r = results[0];
                var aux=r.name.split(',');
                aux.splice(1,2);
                aux.splice(3,1);
                $("#finish").html(aux.toString());
            });
            $("#finish").html('Desconocido');
        }

        if(first && last) {
            $("#distance").html(map.getDistance(arrayPoints)+" m");
        }

        $(".leaflet-marker-icon").on();

        routing.rerouteAllSegments(function(){});
        // routing._segments.clearLayers();
    };

    L.Map.prototype.showResumeInfo=function(){
        if(mapConfig.events){
            var first=mapConfig.events[0].latLng;
            if(mapConfig.events.length!=1)var last=mapConfig.events[mapConfig.events.length-2].latLng;
            else{
                last=first;
            }
            var distance=0;
            if(first){
                geocoder.reverse(first, map.options.crs.scale(16), function(results) {
                    var r = results[0];
                    var aux=r.name.split(',');
                    aux.splice(1,2);
                    aux.splice(3,1);
                    $("#start_resume").html(aux.toString());
                });
            }
            if(last){
                geocoder.reverse(last, map.options.crs.scale(16), function(results) {
                    var r = results[0];
                    var aux=r.name.split(',');
                    aux.splice(1,2);
                    aux.splice(3,1);
                    $("#finish_resume").html(aux.toString());
                });
            }
            var aux_distance=new Array();
            for(var i=0;i<mapConfig.events.length-1;i++){
                aux_distance.push(mapConfig.events[i].latLng);
            }
            if(first && last){
                var distance=map.getDistance(aux_distance);
                $("#distance_resume").html(distance+" m");
                $("#enviroment_resume").html((distance*0.0001).toFixed(2)+" litros de carburante");
                $("#safe_resume").html((distance*0.0001*1.5).toFixed(2)+" € en carburante");
                $("#pollution_resume").html((distance*0.0001*0.4).toFixed(2)+" kg");
            }

            routing.rerouteAllSegments(function(){});
        }
        // routing._segments.clearLayers();
    };

    L.Map.prototype.getDistance=function(points){
        var distance=0;
        if(points){
            for(var i=0;points.length-1>i;i++){
                var actual=points[i];
                var next=points[i+1];
                distance+=Math.floor(actual.distanceTo(next));
            }
        }
        else{
            for(var i=0;map.getWaypoints().length-1>i;i++){
                var actual=map.getWaypoints()[i];
                var next=map.getWaypoints()[i+1];
                distance+=Math.floor(actual.distanceTo(next));
            }
        }
        return distance;
    };
    /**
    *    @param {string/timestamp} a
    *    @return number time in secs
    **/

    var timestampToSecs=function(a){
        return(a.split(" ")[0]*360+a.split(" ")[1]*60+a.split(" ")[0]*1);
    };

    lastId=0;


    //Kml controls
    $('#fileButton').click(function(){
        $('#selectFile').trigger('click');
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                for(var i in map._layers) {
                    if (map._layers[i]._latlngs) {
                        map.removeLayer(map._layers[i]);
                    }
                }
                omnivore.kml(e.target.result).addTo(map).on('ready', function() {
                    for(var i in map._layers) {
                        if(map._layers[i]._latlngs) {
                            map._layers[i].setStyle({'color':(i%2?'red':'green')});
                        } else if (map._layers[i]._latlng) {
                            marker = new L.Marker(map._layers[i]._latlng);
                        }
                        var last=map._layers[i]._latlngs
                    }
                    map.updateRouteInfo();
                    $("#enviar").removeAttr("disabled");
                    $("#enviar").css('opacity','1');
                    map.panTo(last);
                });
                kml=true;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#selectFile").change(function(){
        readURL(this);
    });

    // Eventos para el mostrar el paseo en tiempo real
    var addLast=function(){
        $.ajax({
            url: lastRoute+lastId
            ,success: function(response){
                if(response.length==0) {
                    timeFromLastUpdate+=0;
                }
                else{
                    for(var i=0;i<response.length;i++){
                        lastId=response[i].id;
                        switch(response[i].action){
                            case 'point':
                                map.eventManager.addPoint(response[i]);
                                break;
                            case 'in':
                                map.eventManager.joinChild(response[i]);
                                break;
                            case 'out':
                                map.eventManager.disjoinChild(response[i]);
                                break;
                            case 'finish':
                                map.eventManager.finishRoute(response[i]);
                                clearInterval(dbRequest);
                                break;
                            case 'report':
                                map.eventManager.addIssue(response[i]);
                                break;
                        }
                    }
                    map.eventManager.updateTrazeoIcon(response[i-1]);
                }
            }
            ,error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                console.log('Error : ' + errorThrown);
            }
        })

    }

    // Event Manager(handle events from DB)
    L.Map.prototype.eventManager=
    {
        addPoint: function(response){
            // tiempo de respuesta cortado
            if(timeFromLastUpdate>120)this.conexionLostHandler(response);
            // evento nuevo
            else{
                var point=new POINT(response.location.latitude,response.location.longitude);
                if(typeof poly==='undefined'){
                    poly=new L.polyline(point.latLng,{dashArray: "10, 20",color:"green"});
                    poly.addTo(map);
                }

                else poly.addLatLng(point.latLng);
                timeFromLastUpdate=0;
            }

        },
        updateTrazeoIcon: function(response){
            if($('.trazeo-map-icon').length==0){
                var htmlInner='<img class="img-responsive pull-left" width="25" height="25" src="../../../../images/trazeito.png" alt="Trazeo" title="Trazeo" style="position: absolute">';
                var myIcon = L.divIcon({className: 'trazeo-map-icon',html: htmlInner});
                var point=new POINT(response.location.latitude,response.location.longitude);
                // add join child icon to map
                trazeoMapMarker=L.marker(point.latLng, {icon: myIcon});
                trazeoMapMarker.addTo(map);
            }
            else{
                var point=new POINT(response.location.latitude,response.location.longitude);
                trazeoMapMarker.setLatLng(point.latLng)
            }
        },
        joinChild:	function(response){
            // tiempo de respuesta cortado
            if(timeFromLastUpdate>120)this.conexionLostHandler(response);

            var point=new POINT(response.location.latitude,response.location.longitude);
            // add join child icon to map
            var myIcon = L.divIcon({className: '',html: "<i class='fa fa-user fa-2x' id='"+response.id+"'></i>"});
            L.marker(point.latLng, {icon: myIcon}).addTo(map);
            $("#"+response.id).css('cssText', 'color: green !important');
        },

        disjoinChild:	function(response) {
            // tiempo de respuesta cortado
            if (timeFromLastUpdate > 120)this.conexionLostHandler(response);
            var point = new POINT(response.location.latitude, response.location.longitude);
            // add join child icon to map
            var myIcon = L.divIcon({className: '', html: "<i class='fa fa-user fa-2x' id='" + response.id + "'></i>"});
            L.marker(point.latLng, {icon: myIcon}).addTo(map);
            $("#" + response.id).css('cssText', 'color: red !important');
        },

        finishRoute:	function(response){
            // tiempo de respuesta excedido
            if(timeFromLastUpdate>120)this.conexionLostHandler(response);

            var point=new POINT(response.location.latitude,response.location.longitude);

            var myIcon = L.divIcon({className: '',html: "<i class='fa fa-flag fa-2x' id='"+response.id+"'></i>"});
            L.marker(point.latLng, {icon: myIcon}).addTo(map);
            $("#"+response.id).css('cssText', 'color: green !important');
            $("#"+response.id).attr('title','fin de ruta');
        },
        addIssue:	function(response){
            // tiempo de respuesta excedido
            if(timeFromLastUpdate>120)this.conexionLostHandler(response);

            var point=new POINT(response.location.latitude,response.location.longitude);

            var myIcon = L.divIcon({className: '',html: "<i rel='tooltip-top' class='fa fa-exclamation-circle fa-2x' id='"+response.id+"'></i>"});
            L.marker(point.latLng, {icon: myIcon}).addTo(map);
            $("#"+response.id).css('cssText', 'color: red !important');
            $("#"+response.id).attr('title',response.data.split('/')[1]);

        }
    };

    // Mapa tiempo real
    if(mapConfig.realTime){
        //if(events.length>0)map.loadFromEvents(events);
        var dbRequest=setInterval(addLast,5000);
    }

    if (mapConfig.resume) {
        setTimeout(map.showResumeInfo,1000);
    }

    if(mapConfig.points){
        map.loadFromWaypoints(mapConfig.points);
    }
    map.updateRouteInfo();
    map.disableRouting();
}