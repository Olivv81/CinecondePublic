
// CONFIGURATION---------------------------------------------------------------------------

var xmlFile = "allocineseances.xml"; //l'url du fichier XML enregistré chez vous
var isAffiche = true; //mettre "false" si ne pas afficher, "true" sinon
var isCasting = true; //mettre "false" si ne pas afficher, "true" sinon
var isSynopsis = true; //mettre "false" si ne pas afficher, "true" sinon
var isBandeAnnonce = true; //mettre "false" si ne pas afficher, "true" sinon
var isResa = true; //mettre "false" si ne pas afficher, "true" sinon
var couleurSeances = "#FF0000";
var petitePolice = "0.8em";

// FIN DE CONFIGURATION--------------------------------------------------------------------


// NE PAS TOUCHER !!
document.write ('<script type="text/javascript" src="fenetres.js"></script>');

ACAjax = {};
ACAjax.Request = function(url, options){
	var request = null;
	var tentatives = [
		function(){return new XMLHttpRequest();},
		function(){return new ActiveXObject("Msxml2.XMLHTTP");},
		function(){return new ActiveXObject("Microsoft.XMLHTTP");},
		];
	for (i=0;(null==request)&&i<tentatives.length;i++)
	{
		try{
			request = tentatives[i]();
		}
		catch(e){
			continue;
		}
	}
	if (null == request)
		throw new Error("XmlHttp non supporté");
	options = options||{};
	request.onreadystatechange = function() {
        if (request.readyState == 4)
			if ( (request.status == 200) && (options.onSuccess) )
	            options.onSuccess(request);
			else if (options.onFailure)
				options.onFailure(request)
    }
	options.method = options.method || "get";
	switch(options.method.toLowerCase()){
		case "get":
		    request.open(options.method.toUpperCase(), ACAjax.sameDomain(url) + (options.parameters?("?" + options.parameters):""), true);
			request.send(null);
			break;
		case "post":
		    request.open(options.method.toUpperCase(), ACAjax.sameDomain(url), true);
			request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			request.setRequestHeader("Content-Length", (options.parameters?("'" + options.parameters.length + "'"):"0"));
			request.send(options.parameters||"");
			break;
		default:
			break;
	}
}

ACAjax.sameDomain = function(url){
	return url.replace(/^[^/]+\/\/[^/]+/,
		window.location.protocol
		+ "//"
		+ window.location.host
		+ (window.location.port?(":" + window.location.port):"")
	);
}

ACAjax.EncodeParameters = function(parameters){
	if ("object" == typeof parameters){
		var ar = [];
		for (name in parameters){
			var value = parameters[name].toString();
			ar.push(encodeURIComponent(name).replace(/%20/,"+") + '=' +
				encodeURIComponent(value).replace(/%20/,"+"));
		}
		return ar.join("&");
	}
	else
		return "";
}

function xmlGetValue(xmldoc, nodename, nodenum, attname)
{if (xmldoc)
 {if (xmldoc.getElementsByTagName(nodename))
  {if (xmldoc.getElementsByTagName(nodename)[nodenum])
   {if (attname==null || attname=='')
    {
	return xmldoc.getElementsByTagName(nodename)[nodenum].text;}
    else
    {
	return xmldoc.getElementsByTagName(nodename)[nodenum].attributes.getNamedItem(attname).value;}
   }
  }
 }
}

function WriteSeances(xmlSTimes)
{
	var tempSeances = '';
	var semaineOld = '';
	var nbFilms = xmlSTimes.getElementsByTagName("film").length;
	var semaines = xmlSTimes.getElementsByTagName('semaine'),
              dateSemaine,
              films,
              lenSemaine = semaines.length,
              lenFilm,
              i = 0,
              j = 0,
              vo,
              version,
              projection,
              soustitre;
          for (i; i<lenSemaine; i++) {
            j = 0;
			dateSemaine = semaines[i].getAttribute('date');
            films = semaines[i].getElementsByTagName('film');
            lenFilm = films.length;
			tempSeances+='<table><tr><td><b><h1>Semaine du ' + dateSemaine + '</h1></b></td></tr></table>'
			 
			  for (j; j<lenFilm; j++) {
			
					tempSeances+='<table><tr>'
					
					
					//affiche
					if(isAffiche) tempSeances+='<td width="160" height="180" valign="middle"><img src="' + films[j].getAttribute('affichette').replace('.fr/', '.fr/r_150_x') + '" border="0" style="float:left;clear:both;margin-bottom:15px;margin-right:10px;" /></td>'
					//titre
					tempSeances+='<td valign="top"><b>' + films[j].getAttribute('titre') + '</b>'
					//casting
					if(isCasting){
					tempSeances+='<div style="font-size:'+petitePolice+'; margin-top:5px;">'
					tempSeances+='de ' + films[j].getAttribute('realisateurs') + '<br />'
					tempSeances+='avec ' + films[j].getAttribute('acteurs') + '<br />'
					tempSeances+='</div>'}
					//synopsis
					if(isSynopsis){
					tempSeances+='<div style="font-size:'+petitePolice+'; margin-top:5px;">'
					tempSeances+='(' + films[j].getAttribute('genreprincipal') + ' - ' + films[j].getAttribute('anneeproduction') + ' - ' + films[j].getAttribute('nationalite') + ') ' + films[j].getAttribute('synopsis') + '<br />'
					tempSeances+='</div>'}
					if(isBandeAnnonce){
						if (films[j].getAttribute('video') != ''){
						tempSeances+='<div style="font-size:'+petitePolice+'; margin-top:5px;">'
						tempSeances+='<a href="javascript:FenACVision(' + films[j].getAttribute('video') + ', 1, \'\', \'salles\')">Voir la bande annonce</a><br />'
						tempSeances+='</div>'}
					}
					//allociné (ne pas supprimer)
					tempSeances+='<div style="font-size:'+petitePolice+'; margin-top:5px;">'
					tempSeances+='Plus d\'infos sur <a target="_allocine" href="http://www.allocine.fr/film/fichefilm_gen_cfilm=' + films[j].getAttribute('id') + '.html">' + films[j].getAttribute('titre') + '</a> avec <a href="http://www.allocine.fr/" target="_allocine"><img src="http://a69.g.akamai.net/n/69/10688/v1/img5.allocine.fr/acmedia/images/as/allocine-85x26.png" border="0" alt="AlloCiné" style="vertical-align:middle;" /></a>'
					tempSeances+='</div>'
					//type projection
					var horaires = films[j].getElementsByTagName('horaire_web');

					for (var g=0; g < horaires.length; g++){
					var horaire = horaires[g];
						vo = horaire.getAttribute('vo'),
						projection = horaire.getAttribute('projection'),
						
						tempSeances+='<div style="font-size:'+petitePolice+'; margin-top:5px;">'
						if (vo == 1) {
							tempSeances+='<b>En VO</b>' 
						}
						else {
							tempSeances+='<b>En VF</b>'
						}
						if (projection != ''){
							tempSeances+=', ' + projection
						}
						
						tempSeances+='</div>'
					
					
						//séances
						tempSeances+='<div style="color:'+couleurSeances+'; margin-top:5px;">'
						var dates = horaire.text || horaire.textContent || horaire.nodeValue;
						if (dates)
						{
							tempSeances += dates.replace(/ \| /gi, '#').replace(/\|/gi, '<br />').replace(/#/gi, ' | ');
						}
						tempSeances+='</div>'
					}
					
						
					tempSeances+='</td></tr></table><hr />'
					
					
				}
			}
	document.getElementById("allocine_seances").innerHTML = tempSeances;
	
	var a = parent.document.getElementById("allocineSalles");
	if (a !== null){
		a.height = document.body.offsetHeight + 100 /* title size */;
	}
}


ACAjax.Request (xmlFile,
	{
		method: "get",
		onSuccess: function (xhr) {WriteSeances (xhr.responseXML);},
		onFailure: function (xhr) {window.alert ("error", xhr.status);}
	}
);

// FIN DE "NE PAS TOUCHER !!"
